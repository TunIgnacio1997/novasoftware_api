<?php

namespace App\Imports;

use App\Models\Familias;
use App\Models\SubFamilia;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Sheet "Subfamilias". Expected headers: nombre, familia (nombre de la familia padre).
 */
class SubFamiliasImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function prepareForValidation(array $data, int $index): array
    {
        $data['nombre'] = $data['nombre'] ?? $data['subfamilia'] ?? null;

        return $data;
    }

    public function model(array $row)
    {
        $nombre = trim((string) ($row['nombre'] ?? ''));

        if ($nombre === '') {
            return null;
        }

        $nombreFamilia = trim((string) ($row['familia'] ?? ''));
        $familia = Familias::firstOrCreate(
            ['nombre' => $nombreFamilia],
            ['comision' => 0]
        );

        return SubFamilia::updateOrCreate(
            ['nombre' => $nombre],
            ['id_familia' => $familia->id]
        );
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50'],
            'familia' => ['required', 'string', 'max:50'],
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return trim((string) ($row['nombre'] ?? $row['subfamilia'] ?? '')) === '';
    }
}
