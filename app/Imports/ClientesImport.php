<?php

namespace App\Imports;

use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientesImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function prepareForValidation(array $data, int $index): array
    {
        $aliases = [
            'num_cliente' => ['numero_cliente'],
            'tipo' => ['tipo_de_cliente'],
            'credito' => ['limite_de_credito'],
            'plazo' => ['plazo_de_credito_dias', 'dias'],
            'telef1' => ['telefono_1'],
            'telef2' => ['telefono_2'],
            'cod_post' => ['codigo_postal'],
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
        $numCliente = trim((string) ($row['num_cliente'] ?? $row['numero_cliente'] ?? ''));

        if ($numCliente === '') {
            return null;
        }

        $razonSocial = trim((string) ($row['razon_social'] ?? $row['nombre_comercial'] ?? 'Cliente'));
        $nombreComercial = trim((string) ($row['nombre_comercial'] ?? $razonSocial));

        return Cliente::updateOrCreate(
            ['num_cliente' => $numCliente],
            [
                'razon_social' => $razonSocial,
                'nombre_comercial' => $nombreComercial,
                'calle' => $row['calle'] ?? '',
                'cod_post' => $row['cod_post'] ?? null,
                'ciudad' => $row['ciudad'] ?? null,
                'estado' => $row['estado'] ?? null,
                'telef1' => $row['telef1'] ?? null,
                'telef2' => $row['telef2'] ?? '',
                'email' => $row['email'] ?? null,
                'credito' => $this->toDecimal($row['credito'] ?? null) ?? 0,
                'plazo' => (int) ($row['plazo'] ?? $row['dias'] ?? 0),
                'pagos' => $this->toDecimal($row['pagos'] ?? null) ?? 0,
                'tipo' => $row['tipo'] ?? 'Contado',
                'saldo' => $this->toDecimal($row['saldo'] ?? null) ?? 0,
                'tax' => $this->toDecimal($row['tax'] ?? null) ?? 0,
                'id_cobratario' => (int) ($row['id_cobratario'] ?? 0),
                'id_reparticion' => (int) ($row['id_reparticion'] ?? 0),
                'id_company' => (int) ($row['id_company'] ?? 1),
                'contacto' => $row['contacto'] ?? '',
                'asesor' => $row['asesor'] ?? '',
                'rfc' => $row['rfc'] ?? '',
                'curp' => $row['curp'] ?? '',
                'excl_dual' => $this->toExclDual($row['excl_dual'] ?? null),
                'domicilio_residencia' => $row['domicilio_residencia'] ?? '',
                'bloqueo' => (int) ($row['bloqueo'] ?? 0),
                'estatus' => (int) ($row['estatus'] ?? 1),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'num_cliente' => ['required', 'string', 'max:11'],
            'razon_social' => ['required', 'string', 'max:90'],
            'nombre_comercial' => ['required', 'string', 'max:90'],
            'email' => ['nullable', 'email'],
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return trim((string) ($row['num_cliente'] ?? $row['numero_cliente'] ?? '')) === '';
    }

    private function toDecimal(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }

    private function toExclDual(mixed $value): string
    {
        $value = strtoupper(trim((string) $value));

        return in_array($value, ['E', 'D'], true) ? $value : 'E';
    }
}
