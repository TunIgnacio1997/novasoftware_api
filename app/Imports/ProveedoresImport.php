<?php

namespace App\Imports;

use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Sheet "Proveedores". Expected headers: num_proveedor, nombre_comercial, razon_social,
 * calle, cod_post, ciudad, estado, telef1, telef2, email, rfc, credito, dias,
 * tiempo_entrega, id_company, tax.
 */
class ProveedoresImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function prepareForValidation(array $data, int $index): array
    {
        $aliases = [
            'num_proveedor' => ['numero_proveedor'],
            'num_ext' => ['numero_exterior'],
            'num_int' => ['numero_interior'],
            'cod_post' => ['codigo_postal'],
            'telef1' => ['telefono_1'],
            'telef2' => ['telefono_2'],
            'clasif' => ['clasificacion'],
            'dias' => ['dias_de_credito'],
            'tiempo_entrega' => ['tiempo_de_entrega_dias'],
            'comments' => ['comentarios'],
        ];

        foreach ($aliases as $field => $alternatives) {
            if (! isset($data[$field]) || $data[$field] === '') {
                foreach ($alternatives as $alternative) {
                    if (isset($data[$alternative]) && $data[$alternative] !== '') {
                        $data[$field] = $data[$alternative];
                        break;
                    }
                }
            }
        }

        return $data;
    }

    public function model(array $row)
    {
        $numProveedor = trim((string) $this->value($row, ['num_proveedor', 'numero_proveedor']));

        if ($numProveedor === '') {
            return null;
        }

        return Proveedor::updateOrCreate(
            ['num_proveedor' => $numProveedor],
            [
                'razon_social' => $this->value($row, ['razon_social'], ''),
                'nombre_comercial' => $this->value($row, ['nombre_comercial'], ''),
                'clasif' => $this->value($row, ['clasif'], ''),
                'calle' => $this->value($row, ['calle'], ''),
                'num_ext' => $this->value($row, ['num_ext'], ''),
                'num_int' => $this->value($row, ['num_int'], ''),
                'colonia' => $this->value($row, ['colonia'], ''),
                'cod_post' => $this->value($row, ['cod_post'], ''),
                'ciudad' => $this->value($row, ['ciudad'], ''),
                'municipio' => $this->value($row, ['municipio'], ''),
                'estado' => $this->value($row, ['estado'], ''),
                'telef1' => $this->value($row, ['telef1'], ''),
                'telef2' => $this->value($row, ['telef2'], ''),
                'email' => $this->value($row, ['email'], ''),
                'contacto' => $this->value($row, ['contacto'], ''),
                'asesor' => $this->value($row, ['asesor'], ''),
                'comments' => $this->value($row, ['comments'], ''),
                'rfc' => $this->value($row, ['rfc'], ''),
                'curp' => $this->value($row, ['curp'], ''),
                'credito' => $this->toDecimal($this->value($row, ['credito'])),
                'dias' => $this->toInt($this->value($row, ['dias'])),
                'tiempo_entrega' => $this->toInt($this->value($row, ['tiempo_entrega'])),
                'bloqueo' => 0,
                'id_company' => (int) ($row['id_company'] ?? 1),
                'tax' => $this->toDecimal($this->value($row, ['tax'])) ?? 0,
                'estatus' => 1,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'num_proveedor' => ['required', 'string', 'max:20'],
            'nombre_comercial' => ['required', 'string', 'max:255'],
            'razon_social' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'credito' => ['nullable', 'numeric'],
            'dias' => ['nullable', 'integer'],
            'tiempo_entrega' => ['nullable', 'integer'],
            'tax' => ['nullable', 'numeric'],
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return trim((string) $this->value($row, ['num_proveedor', 'numero_proveedor'])) === '';
    }

    private function value(array $row, array $keys, mixed $default = null): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
                return $row[$key];
            }
        }

        return $default;
    }

    private function toDecimal(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }

    private function toInt(mixed $value): ?int
    {
        return $value === null || $value === '' ? null : (int) $value;
    }
}
