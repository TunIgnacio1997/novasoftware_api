<?php
namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    //
    public function getAlmacenes(Request $request)
    {
        return Almacen::when($request->filled('id'), function ($query) use ($request) {
                $query->where('clave', $request->id);
            })
            ->when($request->filled('nombre'), function ($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->nombre . '%');
            })
            ->orderBy('clave', 'desc')
            ->paginate($request->itemPage);
    }
}
