<?php

namespace App\Imports;

use App\Models\Sucursal;
use App\Models\Vendedor;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Sheet "Vendedores". Expected headers: clave, nombre, direccion, telef, email,
 * comision, tipo, sucursal (nombre).
 */
class VendedoresImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function prepareForValidation(array $data, int $index): array
    {
        $data['tipo'] = $data['tipo'] ?? $data['tiposucursal'] ?? null;
        $data['telef'] = $data['telef'] ?? $data['telefono'] ?? null;

        return $data;
    }

    public function model(array $row)
    {
        $clave = trim((string) ($row['clave'] ?? ''));

        if ($clave === '') {
            return null;
        }

        $nombreSucursal = trim((string) ($row['sucursal'] ?? '')) ?: 'Matriz';
        $sucursal = Sucursal::firstOrCreate(['nombre' => $nombreSucursal]);
        $tipo = trim((string) ($row['tipo'] ?? '')) ?: 'Tienda';

        $tipoExiste = DB::table('tipos_vendedores')
            ->where('clave', $tipo)
            ->orWhere('nombre', $tipo)
            ->exists();

        if (! $tipoExiste) {
            DB::table('tipos_vendedores')->insert([
                'id' => ((int) DB::table('tipos_vendedores')->max('id')) + 1,
                'clave' => strtoupper($tipo),
                'nombre' => $tipo,
                'id_fecha' => now(),
                'id_usuario' => 0,
                'id_sucursal' => $sucursal->id,
            ]);
        }

        return Vendedor::updateOrCreate(
            ['clave' => $clave],
            [
                'nombre' => $row['nombre'] ?? null,
                'direccion' => $row['direccion'] ?? '',
                'telef' => $row['telef'] ?? null,
                'email' => $row['email'] ?? null,
                'comision' => $this->toDecimal($row['comision'] ?? null) ?? 0,
                'tipo' => $tipo,
                'id_sucursal' => $sucursal->id,
                'id_users' => $row['id_users'] ?? $row['id_usuario'] ?? 0,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'clave' => ['required', 'string', 'max:20'],
            'nombre' => ['required', 'string', 'max:255'],
            'sucursal' => ['nullable', 'string', 'max:50'],
            'comision' => ['nullable', 'numeric'],
            'tipo' => ['nullable', 'string', 'max:50'],
            'id_usuario' => ['nullable', 'integer'],
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return trim((string) ($row['clave'] ?? '')) === '';
    }

    private function toDecimal(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }
}
