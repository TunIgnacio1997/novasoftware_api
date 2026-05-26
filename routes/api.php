<?php

use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CobratarioController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FamiliasController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\EstatusController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\ReparticionController;
use App\Http\Controllers\SubFamiliasController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\TiposPagoController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\InventarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('updateUser/{id}', [AuthController::class, 'update']);

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/corte-caja', [VentaController::class, 'getCalculado']);
});

Route::get('users', [AuthController::class, 'allUsers']);

//----- producto
Route::get('buscarProducto', [ProductsController::class, 'buscarProducto']);
Route::get('buscarProductoVenta', [ProductsController::class, 'buscarProductoVenta']);
Route::get('Products/GetProducts', [ProductsController::class,'getProductos']);
Route::get('getProductosByFamilia', [ProductsController::class,'getProductosByFamilia']);
Route::post('addProducto', [ProductsController::class, 'addProducto']);
Route::post('updateProducto', [ProductsController::class, 'updateProducto']);
Route::get('getImagenesProducto', [ProductsController::class, 'getImagenesProducto']);
//----- cliente
Route::get('buscarCliente', [ClienteController::class, 'searchCliente']);
Route::get('getClientes', [ClienteController::class, 'getClientes']);
Route::post('addCliente', [ClienteController::class, 'addCliente']);
Route::post('updateCliente', [ClienteController::class, 'updateCliente']);
//----- ventas
Route::get('ventas', [VentaController::class, 'getVentas']);
Route::get('getVentaById', [VentaController::class, 'getVentaById']);
Route::post('createVenta', [VentaController::class, 'store']);
Route::get('/venta/{id}', [VentaController::class, 'getDetalleVenta']);

//---- proveedor
Route::get('getProveedores', [ProveedorController::class, 'getProveedores']);
Route::post('addProveedor', [ProveedorController::class, 'addProveedor']);
Route::post('updateProveedor', [ProveedorController::class, 'updateProveedor']);
//---- cobratario
Route::get('getCobratarios', [CobratarioController::class, 'getCobratarios']);
Route::post('addCobratario', [CobratarioController::class, 'addCobratario']);
Route::post('updateCobratario', [CobratarioController::class, 'updateCobratario']);
//---- almacen
Route::get('getAlmacenes', [AlmacenController::class, 'getAlmacenes']);

// unidad de medida
Route::get('getUnidadesMedida', [UnidadMedidaController::class, 'getUnidadesMedida']);
Route::get('getUnidadesAll', [UnidadMedidaController::class, 'getUnidadesAll']);
Route::post('addUnidadesMedida', [UnidadMedidaController::class, 'addUnidadesMedida']);
Route::post('updateUnidadesMedida', [UnidadMedidaController::class, 'updateUnidadesMedida']);
//familias
Route::get('getFamilias', [FamiliasController::class , 'getFamilias']);
Route::get('getFamiliasAll', [FamiliasController::class , 'getFamiliasAll']);
Route::post('addFamilia', [FamiliasController::class, 'addFamilia']);
Route::post('updateFamilia', [FamiliasController::class, 'updateFamilia']);
//subfamilia
Route::get('getSubFamilias', [SubFamiliasController::class , 'getSubFamilias']);
Route::get('getSubFamiliasAll', [SubFamiliasController::class , 'getSubFamiliasAll']);
Route::post('addSubFamilia', [SubFamiliasController::class, 'addSubFamilia']);
Route::post('updateSubFamilia', [SubFamiliasController::class, 'updateSubFamilia']);
//estatus
Route::get('getEstatus', [EstatusController::class, 'getEstatus']);
Route::post('addEstatus', [EstatusController::class, 'addEstatus']);
Route::post('updateEstatus', [EstatusController::class, 'updateEstatus']);
//reparticion
Route::get('getReparticion', [ReparticionController::class, 'getReparticion']);

//vendedores
Route::get('getVendedores', [VendedorController::class, 'getVendedores']);
Route::post('addVendedor', [VendedorController::class, 'addVendedor']);
Route::post('updateVendedor', [VendedorController::class, 'updateVendedor']);

//tipos de pago
Route::get('getTiposPagoAll', [TiposPagoController::class, 'getTiposPagoAll']);
Route::post('createTipoPago', [TiposPagoController::class, 'create']);
Route::post('updateTipoPago', [TiposPagoController::class, 'update']);

//orden compra
Route::get('getOrdenesCompra', [OrdenCompraController::class, 'store']);
Route::post('createOrdenCompra', [OrdenCompraController::class, 'create']);
Route::post('updateOrdenCompra', [OrdenCompraController::class, 'update']);
Route::post('deleteOrdenCompra', [OrdenCompraController::class, 'delete']);
Route::get('getOrdenCompra', [OrdenCompraController::class, 'show']);
Route::get('generarFactura', [OrdenCompraController::class, 'generateInvoice']);
Route::post('saveFullReception', [OrdenCompraController::class, 'saveFullReception']);

//company
Route::get('getCompanyInfo', [CompanyController::class, 'getCompanyInfo']);
Route::post('saveInfoCompany', [CompanyController::class, 'create']);

Route::prefix('/sucursales')->group(function () {
    Route::get('/', [SucursalController::class, 'index']);
});

Route::prefix('/productos')->group(function () {
    Route::get('/{id_almacen}/almacen-existencias', [ProductsController::class, 'getProductosbyAlmacen']);
});

Route::prefix('/inventario')->group(function () {
    Route::get('/inv-inicial', [InventarioController::class, 'index']);
    Route::post('/guardar-inv-inicial', [InventarioController::class, 'store']);
});