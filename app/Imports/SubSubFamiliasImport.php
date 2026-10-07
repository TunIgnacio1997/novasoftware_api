<?php

namespace App\Imports;

use App\Models\SubFamilia;
use App\Models\SubSubFamilia;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Sheet "Sub-subfamilias". Expected headers: nombre, sub_familia (nombre de la subfamilia padre).
 */
class SubSubFamiliasImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function prepareForValidation(array $data, int $index): array
    {
        $data['nombre'] = $data['nombre'] ?? $data['sub_subfamilia'] ?? null;
        $data['sub_familia'] = $data['sub_familia'] ?? $data['subfamilia'] ?? null;

        return $data;
    }

    public function model(array $row)
    {
        $nombre = trim((string) ($row['nombre'] ?? ''));

        if ($nombre === '') {
            return null;
        }

        $nombreSubFamilia = trim((string) ($row['sub_familia'] ?? ''));
        $subFamilia = SubFamilia::firstOrCreate(
            ['nombre' => $nombreSubFamilia],
            ['id_familia' => null]
        );

        return SubSubFamilia::updateOrCreate(
            ['nombre' => $nombre],
            ['id_sub_familia' => $subFamilia->id]
        );
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50'],
            'sub_familia' => ['required', 'string', 'max:50'],
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return trim((string) ($row['nombre'] ?? $row['sub_subfamilia'] ?? '')) === '';
    }
}
