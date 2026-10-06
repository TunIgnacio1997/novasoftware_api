<?php

namespace App\Http\Controllers;

use App\Imports\CatalogImportException;
use App\Models\Company;
use App\Models\Role;
use App\Models\Sucursal;
use App\Models\User;
use App\Services\CatalogImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class CompanyController extends Controller
{
    //
    public function getCompanyInfo(Request $request){
        return Company::query()->first();
    }
    public function create(Request $request){
        $comp = Company::query()->first();
        $comp ??= new Company();
        $comp->conceptnamecompany = $request->companyName;
        $comp->mail = $request->correo;
        $comp->url = '';
        $comp->logo = $request->logo;
        $comp->access_key  = '';
        $comp->direccion = $request->direccion;
        $comp->iva = 16.0;
        $comp->rfc = $request->rfc;
        $comp->regimen_fiscal = $request->regimen;
        if ($comp->update()) {
            return response(['mensaje'=>'Los datos de la compania se guardaron correctamente', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'Ocurrio un error al guardar datos', 'success'=>false], 404);
        }
    }
    public function checkCompanyOnboarding(Request $request){
        $comp = Company::query()->first();
        $superAdmin = User::where('rol_id', 0)->first();
        $sucursalMatriz = Sucursal::where('nombre', 'Matriz')->first();
        $superAdminHasMatriz = $superAdmin
            && $sucursalMatriz
            && (int) $superAdmin->sucursal_id === (int) $sucursalMatriz->id;

        return response()->json([
            'company_exists' => (bool) $comp,
            'company_configured' => (bool) ($comp && !empty($comp->conceptnamecompany)),
            'super_admin_exists' => (bool) $superAdmin,
            'super_admin_has_matriz' => (bool) $superAdminHasMatriz,
            'onboarded' => (bool) ($comp
                && !empty($comp->conceptnamecompany)
                && $superAdminHasMatriz),
        ]);
    }

    public function onboarding(Request $request, CatalogImportService $catalogImportService)
    {
        $company = Company::query()->first();
        $companyConfigured = $company && !empty($company->conceptnamecompany);
        $superAdminExists = User::where('rol_id', 0)->exists();

        $data = $request->validate([
            'companyName' => [Rule::requiredIf(!$companyConfigured), 'nullable', 'string', 'max:255'],
            'correo' => ['nullable', 'email', 'max:255'],
            'logo' => [
                'nullable',
                Rule::when(
                    $request->hasFile('logo'),
                    ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                    ['string']
                ),
            ],
            'direccion' => ['nullable', 'string', 'max:255'],
            'rfc' => ['nullable', 'string', 'max:20'],
            'regimen' => ['nullable', 'string', 'max:255'],
            'name' => [Rule::requiredIf(!$superAdminExists), 'nullable', 'string', 'max:255'],
            'user' => [Rule::requiredIf(!$superAdminExists), 'nullable', 'string', 'max:255', 'unique:users,user'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => [Rule::requiredIf(!$superAdminExists), 'nullable', 'string', 'min:8'],
            'excel_file' => ['nullable', 'file', 'mimes:xlsx,xls', 'max:20480'],
        ]);

        try {
            $result = DB::transaction(function () use ($data, $request, $catalogImportService) {
                Role::whereKey(0)->lockForUpdate()->firstOrFail();

                $company = Company::query()->lockForUpdate()->first();
                $companyConfigured = $company && !empty($company->conceptnamecompany);
                $superAdmin = User::where('rol_id', 0)->lockForUpdate()->first();
                $logo = $request->hasFile('logo')
                    ? $request->file('logo')->store('logos', 'public')
                    : ($data['logo'] ?? null);

                if (!$companyConfigured) {
                    $company ??= new Company();
                    $company->conceptnamecompany = $data['companyName'];
                    $company->mail = $data['correo'] ?? null;
                    $company->logo = $logo;
                    $company->direccion = $data['direccion'] ?? null;
                    $company->rfc = $data['rfc'] ?? null;
                    $company->regimen_fiscal = $data['regimen'] ?? null;
                    $company->iva ??= 16.0;
                    $company->onboarding_completed = false;
                    $company->save();
                }

                $sucursalMatriz = Sucursal::firstOrCreate(
                    ['nombre' => 'Matriz'],
                    ['id_company' => $company->id]
                );
                if ((int) $sucursalMatriz->id_company !== (int) $company->id) {
                    $sucursalMatriz->id_company = $company->id;
                    $sucursalMatriz->save();
                }

                $alreadyOnboarded = $companyConfigured
                    && $superAdmin
                    && (int) $superAdmin->sucursal_id === (int) $sucursalMatriz->id;

                if (!$superAdmin) {
                    $superAdmin = User::create([
                        'name' => $data['name'],
                        'user' => $data['user'],
                        'email_verified_at' => $data['email'] ?? null,
                        'password' => Hash::make($data['password']),
                        'sucursal_id' => $sucursalMatriz->id,
                        'rol_id' => 0,
                    ]);
                }

                if ((int) $superAdmin->sucursal_id !== (int) $sucursalMatriz->id) {
                    $superAdmin->sucursal_id = $sucursalMatriz->id;
                    if (! $superAdmin->save()) {
                        throw new \RuntimeException(
                            'No se pudo asignar la sucursal Matriz al Super Admin.'
                        );
                    }
                }

                $catalogsImported = false;
                if ($request->hasFile('excel_file')) {
                    $catalogImportService->import($request->file('excel_file')->getRealPath());
                    $catalogsImported = true;
                }

                if ($company && !$company->onboarding_completed) {
                    $company->update(['onboarding_completed' => true]);
                }

                return [
                    'company' => $company,
                    'superAdmin' => $superAdmin,
                    'catalogsImported' => $catalogsImported,
                    'alreadyOnboarded' => (bool) $alreadyOnboarded,
                ];
            });
        } catch (ExcelValidationException $exception) {
            $errors = collect($exception->failures())
                ->map(fn ($failure) => "Fila {$failure->row()} ({$failure->attribute()}): " . implode(' ', $failure->errors()))
                ->implode(' | ');

            return response()->json([
                'success' => false,
                'message' => $errors !== '' ? $errors : 'El archivo contiene datos inválidos.',
            ], 422);
        } catch (CatalogImportException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => $result['alreadyOnboarded']
                ? 'La configuración inicial ya estaba completa.'
                : 'Configuración inicial completada correctamente.',
            'onboarded' => true,
            'catalogs_imported' => $result['catalogsImported'],
            'company' => $result['company'],
            'user' => $result['superAdmin']->load(['rol', 'sucursal']),
        ], $result['alreadyOnboarded'] ? 200 : 201);
    }
}
