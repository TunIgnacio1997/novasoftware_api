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
        $sucursal = Sucursal::firstOrCreate(
            ['nombre' => $nombreSucursal],
            ['id_company' => (int) ($row['id_company'] ?? 1)]
        );

        return Almacen::updateOrCreate(
            ['nombre' => $nombre, 'id_sucursal' => $sucursal->id],
            [
                'id_company' => (int) ($row['id_company'] ?? 1),
                'principal' => (int) ($row['principal'] ?? 0),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50'],
            'sucursal' => ['nullable', 'string', 'max:50'],
            'id_company' => ['nullable', 'integer'],
            'principal' => ['nullable', 'boolean'],
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return trim((string) ($row['nombre'] ?? '')) === '';
    }
}
