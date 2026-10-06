<?php

namespace App\Imports;

use App\Models\Familias;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Sheet "Familias". Expected headers: nombre, comision.
 */
class FamiliasImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        $nombre = trim((string) ($row['nombre'] ?? ''));

        if ($nombre === '') {
            return null;
        }

        return Familias::updateOrCreate(
            ['nombre' => $nombre],
            ['comision' => $this->toDecimal($row['comision'] ?? null) ?? 0]
        );
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50'],
            'comision' => ['nullable', 'numeric'],
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return trim((string) ($row['nombre'] ?? '')) === '';
    }

    private function toDecimal(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }
}
