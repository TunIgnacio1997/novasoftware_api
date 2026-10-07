<?php

namespace App\Imports;

use App\Models\Role;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
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
    use RemembersRowNumber;

    private ?Role $sellerRole = null;

    public function prepareForValidation(array $data, int $index): array
    {
        $data['tipo'] = $data['tipo'] ?? $data['tiposucursal'] ?? null;
        $data['telef'] = $data['telef'] ?? $data['telefono'] ?? null;
        $data['telef'] = $data['telef'] === null ? null : trim((string) $data['telef']);

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
        $vendedor = Vendedor::where('clave', $clave)->first();
        $user = $vendedor?->user;
        $nombre = trim((string) ($row['nombre'] ?? ''));
        $username = self::usernameFromName($nombre);

        if ($username === '') {
            throw new CatalogImportException(
                "Fila {$this->getRowNumber()}: el nombre no contiene caracteres válidos para generar el usuario."
            );
        }

        if (User::where('user', $username)
            ->where('id', '!=', $user?->id ?? 0)
            ->exists()) {
            throw new CatalogImportException(
                "Fila {$this->getRowNumber()}: el usuario normalizado \"{$username}\" ya pertenece a otra cuenta."
            );
        }

        if (! $user) {
            $email = trim((string) ($row['email'] ?? ''));
            if ($email !== '' && User::where('email', $email)->exists()) {
                throw new CatalogImportException(
                    "Fila {$this->getRowNumber()}: el correo \"{$email}\" ya pertenece a otro usuario."
                );
            }

            $this->sellerRole ??= Role::where('nombre', 'Vendedor')->first();
            if (! $this->sellerRole) {
                throw new CatalogImportException(
                    'No está configurado el rol Vendedor; no se pueden importar vendedores.'
                );
            }

            $user = User::create([
                'name' => $nombre,
                'user' => $username,
                'email' => $email !== '' ? $email : 'vendedor-' . Str::uuid() . '@usuarios.invalid',
                'password' => Hash::make($row['telef']),
                'rol_id' => $this->sellerRole->id,
                'sucursal_id' => $sucursal->id,
            ]);
        }
        elseif ($user->user !== $username) {
            $user->user = $username;
            $user->save();
        }

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
                'id_users' => $user->id,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'clave' => ['required', 'string', 'max:20'],
            'nombre' => ['required', 'string', 'max:90'],
            'telef' => ['required', 'string', 'max:25', 'regex:/\d/'],
            'email' => ['nullable', 'email', 'max:80'],
            'sucursal' => ['nullable', 'string', 'max:50'],
            'comision' => ['nullable', 'numeric'],
            'tipo' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return trim((string) ($row['clave'] ?? '')) === '';
    }

    public static function usernameFromName(string $name): string
    {
        $firstName = preg_split('/\s+/u', trim($name), 2)[0] ?? '';
        $asciiFirstName = strtolower(Str::ascii($firstName));

        return (string) preg_replace('/[^a-z0-9]/', '', $asciiFirstName);
    }

    private function toDecimal(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }
}
