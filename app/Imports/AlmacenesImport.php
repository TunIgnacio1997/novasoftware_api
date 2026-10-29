<?php

namespace App\Imports;

use App\Models\Almacen;
use App\Models\Sucursal;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Sheet "Almacenes". Expected headers: nombre, sucursal (nombre), id_company, principal.
 */
class AlmacenesImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function prepareForValidation(array $data, int $index): array
    {
        if (!isset($data['id_company']) || trim((string) $data['id_company']) === '') {
            $data['id_company'] = 1;
        }

        $data['principal'] = $data['principal'] ?? $data['es_almacen_principal'] ?? null;
        $principal = mb_strtolower(trim((string) $data['principal']));

        if (in_array($principal, ['si', 'sí', 'yes', 'true'], true)) {
            $data['principal'] = 1;
        } elseif (in_array($principal, ['no', 'false'], true)) {
            $data['principal'] = 0;
        }

        return $data;
    }

    public function model(array $row)
    {
        $nombre = trim((string) ($row['nombre'] ?? ''));

        if ($nombre === '') {
            return null;
        }

        $nombreSucursal = trim((string) ($row['sucursal'] ?? '')) ?: 'Matriz';
        $companyId = (int) $row['id_company'];
        $sucursal = Sucursal::firstOrCreate(
            ['nombre' => $nombreSucursal, 'id_company' => $companyId]
        );

        $almacen = Almacen::firstOrNew([
            'nombre' => $nombre,
            'id_sucursal' => $sucursal->id,
        ]);
        $principal = (int) ($row['principal'] ?? 0);

        if ($principal === 1) {
            $hasOtherPrincipal = Almacen::query()
                ->where('id_sucursal', $sucursal->id)
                ->where('principal', true)
                ->when($almacen->exists, fn ($query) => $query->where('clave', '<>', $almacen->getKey()))
                ->exists();

            if ($hasOtherPrincipal) {
                throw new CatalogImportException(
                    "La sucursal '{$nombreSucursal}' ya tiene un almacén principal."
                );
            }
        }

        $almacen->id_company = $sucursal->id_company;
        $almacen->principal = $principal;
        $almacen->save();

        return $almacen;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50'],
            'sucursal' => ['nullable', 'string', 'max:50'],
            'id_company' => ['required', 'integer', 'exists:company,id'],
            'principal' => ['nullable', 'boolean'],
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return trim((string) ($row['nombre'] ?? '')) === '';
    }
}
