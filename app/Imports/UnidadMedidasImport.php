<?php

namespace App\Imports;

use App\Models\UnidadMedida;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UnidadMedidasImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        $nombre = trim((string) ($row['nombre'] ?? $row['unidad_medida'] ?? ''));

        if ($nombre === '') {
            return null;
        }

        return UnidadMedida::updateOrCreate(
            ['nombre' => $nombre],
            ['created_at' => now()]
        );
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required_without:unidad_medida', 'nullable', 'string', 'max:100'],
            'unidad_medida' => ['required_without:nombre', 'nullable', 'string', 'max:100'],
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return trim((string) ($row['nombre'] ?? $row['unidad_medida'] ?? '')) === '';
    }
}
