<?php

namespace App\Http\Controllers;

use App\Models\ImagenesProducto;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    //
    public function getProductos(Request $request){
        return  Producto::with(['existencias' => function ($q) use ($request) {
        $q->where('id_almacen', $request->almacen);
    }])
    ->orderBy('id', 'desc')
    ->paginate($request->itemPage);
    }

    public function getProductosByFamilia(Request $request){
        return Producto::where('familia', $request->familia)->where('existencia', '>', 0)->with('imagenes')->get();
    }

    public function buscarProducto(Request $request){
        return Producto::query()
        ->where('item_name', 'like', "%{$request->search}%")
        ->orWhere('item_number', 'like', "%{$request->search}%")
        ->with('imagenes')->get();
    }

    public function buscarProductoVenta(Request $request){
        return Producto::query()
        ->where('existencia', '>', 0)
        ->where('item_name', 'like', "%{$request->search}%")
        ->orWhere('item_number', 'like', "%{$request->search}%")
        ->with('imagenes')->get();
    }

    public function addProducto(Request $request){
        $pro = new Producto();
        $pro->item_name = $request->producto['nombreCorto'];
        $pro->estatus = 'P';
        $pro->size = '1';
        $pro->item_number = $request->producto['codigo'];
        $pro->description = $request->producto['nombre'];
        $pro->brand_id = '0';
        $pro->category_id = '0';
        $pro->supplier_id = '0';
        $pro->type_id = 'C';
        $pro->unit_m = $request->producto['unidad'];
        $pro->buy_price = $request->producto['precio_compra'];
        $pro->unit_price = $request->producto['precio_venta'];
        $pro->tax_percent = '0.00';
        $pro->total_cost = '0.00';
        $pro->quantity = '0.00';
        $pro->reorder_level = '0.00';
        $pro->max_level = '0.00';
        $pro->tipo = 'DISTRIBUIDORA';
        $pro->familia = $request->producto['categoria'];
        $pro->sub_familia = $request->producto['subcategoria'];
        $pro->existencia = 0;
        $pro->existencia2 = 0;
        $pro->maximo = $request->producto['maximo'];
        $pro->minimo = $request->producto['minimo'];
        $pro->inventario = $request->producto['inventario'];
        $pro->congelado = 0;
        $pro->subproducto = 0;
        $pro->pertenece = 0;
        $pro->maneja_series = $request->producto['isSerie'];
        $pro->location = $request->producto['ubicacion'];
        $pro->allow_core = 0;
        if ($pro->save()){
            ImagenesProducto::where('id_producto', $pro->id)->delete();
            foreach($request->imagenes as $it){
                $imgP = new ImagenesProducto();
                $imgP->id_producto = $pro->id;
                $imgP->imagen = $it;
                $imgP->save();
            }
            return response(['mensaje'=>'El producto se guardo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El producto no se guardo', 'success'=>false], 402);
        }
    }

    public function updateProducto(Request $request){
        $pro = Producto::find($request->producto['id']);
        $pro->item_name = $request->producto['nombreCorto'];
        $pro->estatus = 'P';
        $pro->size = '1';
        $pro->item_number = $request->producto['codigo'];
        $pro->description = $request->producto['nombre'];
        $pro->brand_id = '0';
        $pro->category_id = '0';
        $pro->supplier_id = '0';
        $pro->type_id = 'C';
        $pro->unit_m = $request->producto['unidad'];
        $pro->buy_price = $request->producto['precio_compra'];
        $pro->unit_price = $request->producto['precio_venta'];
        $pro->tax_percent = '0.00';
        $pro->total_cost = '0.00';
        $pro->quantity = '0.00';
        $pro->reorder_level = '0.00';
        $pro->max_level = '0.00';
        $pro->tipo = 'DISTRIBUIDORA';
        $pro->familia = $request->producto['categoria'];
        $pro->sub_familia = $request->producto['subcategoria'];
        $pro->existencia = 0;
        $pro->existencia2 = 0;
        $pro->maximo = $request->producto['maximo'];
        $pro->minimo = $request->producto['minimo'];
        $pro->inventario = $request->producto['inventario'];
        $pro->congelado = 0;
        $pro->subproducto = 0;
        $pro->pertenece = 0;
        $pro->maneja_series = $request->producto['isSerie'];
        $pro->location = $request->producto['ubicacion'];
        $pro->allow_core = 0;
        if ($pro->update()){
            ImagenesProducto::where('id_producto', $pro->id)->delete();
            foreach($request->imagenes as $it){
                $imgP = new ImagenesProducto();
                $imgP->id_producto = $pro->id;
                $imgP->imagen = $it;
                $imgP->save();
            }
            return response(['mensaje'=>'El producto se actualizo con exito', 'success'=>true], 200);
        } else {
            return response(['mensaje'=>'El producto no se actualizo', 'success'=>false], 404);
        }
    }

    public function getImagenesProducto(Request $request){
        $imagenes = ImagenesProducto::where('id_producto', $request->id_producto)->get();
        return $imagenes;
    }

    public function getProductosbyAlmacen(int $id_almacen, Request $request)
    {
        $productos = Producto::with('imagenes')

            ->when(
                filled($request->search),
                function ($query) use ($request) {

                    $query->where(function ($q) use ($request) {
                        $q->where('item_name', 'LIKE', '%' . $request->search . '%')
                            ->orWhere('item_number', 'LIKE', '%' . $request->search . '%');
                    });

                },
                function ($query) use ($request) {

                    $query->where('familia', $request->familia);

                }
            )

            ->whereHas('existencias', function ($q) use ($id_almacen) {
                $q->where('id_almacen', $id_almacen);
            })

            ->with(['existencias' => function ($q) use ($id_almacen) {
                $q->where('id_almacen', $id_almacen);
            }])

            ->get();

        $productos->transform(function ($producto) {

            $producto->existencia = isset($producto->existencias[0])
                ? (float) $producto->existencias[0]->cantidad
                : 0;

            unset($producto->existencias);

            return $producto;
        });

        return $productos;
    }
}
