<?php
namespace App\Actions\CorteCaja;

use App\Models\CorteCaja;
use Illuminate\Support\Facades\Auth;

class GuardarCorteCajaAction
{
    public function execute(array $data): CorteCaja
    {
        $user = Auth::user();
        $fecha = $data['fecha'] ?? now()->toDateString();

        $this->validarDuplicado($fecha, $user->sucursal_id);

        return CorteCaja::create([
            'fecha'       => $fecha,
            'id_usuario'  => $user->id,
            'id_sucursal' => $user->sucursal_id,

            // Calculado
            'efectivo_calculado' => $data['efectivo_calculado'] ?? 0,
            'cheque_calculado'   => $data['cheque_calculado']   ?? 0,
            'vales_calculado'    => $data['vales_calculado']    ?? 0,
            'tarjeta_calculado'  => $data['tarjeta_calculado']  ?? 0,

            // Contado
            'efectivo_contado' => $data['efectivo_contado'] ?? 0,
            'cheque_contado'   => $data['cheque_contado']   ?? 0,
            'vales_contado'    => $data['vales_contado']    ?? 0,
            'tarjeta_contado'  => $data['tarjeta_contado']  ?? 0,

            // Diferencias
            'efectivo_diferencia' => $data['efectivo_diferencia'] ?? 0,
            'cheque_diferencia'   => $data['cheque_diferencia']   ?? 0,
            'vales_diferencia'    => $data['vales_diferencia']    ?? 0,
            'tarjeta_diferencia'  => $data['tarjeta_diferencia']  ?? 0,

            // Retiros
            'retiro_efectivo' => $data['retiro_efectivo'] ?? 0,
            'retiro_cheque'   => $data['retiro_cheque']   ?? 0,
            'retiro_vales'    => $data['retiro_vales']    ?? 0,
            'retiro_tarjeta'  => $data['retiro_tarjeta']  ?? 0,

            // Extras
            'total_transferencias' => $data['total_transferencias'] ?? 0,
            'total_anticipos'      => $data['total_anticipos']      ?? 0,
            'total_diferencia'     => $data['total_diferencia']     ?? 0,
        ]);
    }

    private function validarDuplicado(string $fecha, int $idSucursal): void
    {
        $existe = CorteCaja::where('fecha', $fecha)
            ->where('id_sucursal', $idSucursal)
            ->exists();

        if ($existe) {
            throw new \Exception('Ya existe un corte para hoy en esta sucursal');
        }
    }
}