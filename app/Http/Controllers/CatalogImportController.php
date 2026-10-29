<?php

namespace App\Http\Controllers;

use App\Imports\CatalogImportException;
use App\Services\CatalogImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class CatalogImportController extends Controller
{
    public function __construct(private readonly CatalogImportService $catalogImportService)
    {
    }

    public function import(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:20480'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $file = $request->file('excel_file');
            $path = $file->getRealPath();
            $readerType = strtolower($file->getClientOriginalExtension()) === 'xls'
                ? ExcelType::XLS
                : ExcelType::XLSX;

            $this->catalogImportService->import($path, $readerType);

            return response()->json([
                'success' => true,
                'message' => 'Catálogos importados correctamente',
            ], 200);
        } catch (ExcelValidationException $e) {
            $errors = collect($e->failures())
                ->map(fn ($failure) => "Fila {$failure->row()} ({$failure->attribute()}): " . implode(' ', $failure->errors()))
                ->implode(' | ');

            return response()->json([
                'success' => false,
                'message' => $errors !== '' ? $errors : 'El archivo contiene datos inválidos',
            ], 422);
        } catch (CatalogImportException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Falló la importación del catálogo inicial.', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'No fue posible importar los catálogos. Revisa el archivo e inténtalo de nuevo.',
            ], 500);
        }
    }
}
