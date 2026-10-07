<?php

use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\CatalogImportController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ActivityController;
use App\Http\Controllers\api\ClienteController;
use App\Http\Controllers\api\ContactController;
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
use App\Http\Controllers\AjusteInventarioController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\TrasladosController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\SupplierChargeController;
use App\Http\Controllers\AbonoProveedorController;
use App\Http\Controllers\CargoClienteController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\FondoFijoController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register']);
Route::post('updateUser/{id}', [AuthController::class, 'update']);

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/corte-caja', [VentaController::class, 'getCalculado']);
    Route::post('/corte-caja', [VentaController::class, 'guardar']);

    Route::prefix('credit-notes')->group(function () {
        Route::get('/', [CreditNoteController::class, 'index']);
        Route::post('/', [CreditNoteController::class, 'store']);
        Route::post('/{creditNote}/cancel', [CreditNoteController::class, 'cancel'])->name('credit-notes.cancel');
        Route::get('/{creditNote}', [CreditNoteController::class, 'show']);
    });
});

Route::get('users', [AuthController::class, 'allUsers']);

Route::get('company/onboarding', [CompanyController::class, 'checkCompanyOnboarding']);
Route::post('company/onboarding', [CompanyController::class, 'onboarding']);

Route::middleware(['auth:sanctum'])->prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/{role}', [RoleController::class, 'show']);
    Route::match(['put', 'patch'], '/{role}', [RoleController::class, 'update']);
    Route::delete('/{role}', [RoleController::class, 'destroy']);
});

//----- producto
Route::get('buscarProducto', [ProductsController::class, 'buscarProducto']);
Route::get('buscarProductoVenta', [ProductsController::class, 'buscarProductoVenta']);
Route::get('Products/GetProducts', [ProductsController::class,'getProductos']);
Route::get('getProductosByFamilia', [ProductsController::class,'getProductosByFamilia']);
Route::post('addProducto', [ProductsController::class, 'addProducto']);
Route::post('updateProducto', [ProductsController::class, 'updateProducto']);
Route::get('getImagenesProducto', [ProductsController::class, 'getImagenesProducto']);
//----- cliente
Route::prefix('/clientes')->group(function () {
    Route::get('/buscarCliente', [ClienteController::class, 'searchCliente']);
    Route::get('/getClientes', [ClienteController::class, 'getClientes']);
    Route::post('/addCliente', [ClienteController::class, 'addCliente']);
    Route::post('/updateCliente', [ClienteController::class, 'updateCliente']);
    Route::patch('/delete/{id}', [ClienteController::class, 'deleteCliente']);
});

//----- ventas

Route::middleware(['auth:sanctum'])->group(function () {
Route::get('ventas', [VentaController::class, 'getVentas']);
Route::get('getVentaById', [VentaController::class, 'getVentaById']);
Route::post('createVenta', [VentaController::class, 'store']);
Route::get('/venta/{id}', [VentaController::class, 'getDetalleVenta']);
Route::get('/venta/{id}/pdf', [VentaController::class, 'descargarTicket']);
Route::patch(
    '/ventas/{id}/cancelar',
    [VentaController::class, 'cancelar']
);
});

//---- proveedor
Route::prefix('/proveedores')->group(function () {
    Route::get('/getProveedores', [ProveedorController::class, 'getProveedores']);
    Route::post('/addProveedor', [ProveedorController::class, 'addProveedor']);
    Route::post('/updateProveedor', [ProveedorController::class, 'updateProveedor']);
    Route::patch('/delete/{id}', [ProveedorController::class, 'deleteProveedor']);
});

//---- cobratario
Route::prefix('/cobratarios')->group(function () {
    Route::get('/getCobratarios', [CobratarioController::class, 'getCobratarios']);
    Route::post('/addCobratario', [CobratarioController::class, 'addCobratario']);
    Route::post('/updateCobratario', [CobratarioController::class, 'updateCobratario']);
    Route::patch('/delete/{id}', [CobratarioController::class, 'deleteCobratario']);
});
//---- almacen
Route::get('getAlmacenes', [AlmacenController::class, 'getAlmacenes']);

//---- carga inicial de catalogos
Route::post('catalogos/importar', [CatalogImportController::class, 'import']);

// unidad de medida
Route::get('getUnidadesMedida', [UnidadMedidaController::class, 'getUnidadesMedida']);
Route::get('getUnidadesAll', [UnidadMedidaController::class, 'getUnidadesAll']);
Route::post('addUnidadesMedida', [UnidadMedidaController::class, 'addUnidadesMedida']);
Route::post('updateUnidadesMedida', [UnidadMedidaController::class, 'updateUnidadesMedida']);
Route::delete('deleteUnidadesMedida', [UnidadMedidaController::class, 'deleteUnidadesMedida']);
//familias
Route::get('getFamilias', [FamiliasController::class , 'getFamilias']);
Route::get('getFamiliasAll', [FamiliasController::class , 'getFamiliasAll']);
Route::post('addFamilia', [FamiliasController::class, 'addFamilia']);
Route::post('updateFamilia', [FamiliasController::class, 'updateFamilia']);
Route::delete('deleteFamilia', [FamiliasController::class, 'deleteFamilia']);
//subfamilia
Route::get('getSubFamilias', [SubFamiliasController::class , 'getSubFamilias']);
Route::get('getSubFamiliasAll', [SubFamiliasController::class , 'getSubFamiliasAll']);
Route::post('addSubFamilia', [SubFamiliasController::class, 'addSubFamilia']);
Route::post('updateSubFamilia', [SubFamiliasController::class, 'updateSubFamilia']);
Route::delete('deleteSubFamilia', [SubFamiliasController::class, 'deleteSubFamilia']);
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
Route::delete('deleteTipoPago', [TiposPagoController::class, 'delete']);

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
    Route::get('/ajuste/productos', [AjusteInventarioController::class, 'getProductos']);
    Route::post('/ajuste/guardar', [AjusteInventarioController::class, 'guardar']);
    Route::get('/ajustes', [AjusteInventarioController::class, 'index']);
    Route::get('/ajuste/{id}', [AjusteInventarioController::class, 'show']);
});

Route::prefix('devoluciones')->group(function () {
    Route::get('/', [DevolucionController::class, 'index']);
    Route::post('/', [DevolucionController::class, 'store']);
    Route::get(
        '/orden/{tipo}/{folio}',
        [DevolucionController::class, 'buscarOrden']
    );
    Route::patch(
        '/{id}/cancelar',
        [DevolucionController::class, 'cancelar']
    );
    Route::get(
        '/{id}',
        [DevolucionController::class, 'show']
    );
});

Route::prefix('traslados')->group(function () {
    Route::get('/', [TrasladosController::class, 'index']);
    Route::post('/crear', [TrasladosController::class, 'store']);
    Route::post('/recibir', [TrasladosController::class, 'edit']);
    Route::post('/cancelar', [TrasladosController::class, 'delete']);
});

Route::middleware(['auth:sanctum'])->prefix('customer-payments')->group(function () {
    Route::get('/', [CustomerPaymentController::class, 'index']);
    Route::get('/{customerPayment}', [CustomerPaymentController::class, 'show']);
    Route::post('/', [CustomerPaymentController::class, 'store']);
    Route::post('/{customerPayment}/cancel', [CustomerPaymentController::class, 'cancel'])->name('customer-payments.cancel');
});

Route::middleware(['auth:sanctum'])->prefix('supplier-charges')->group(function () {
    Route::get('/', [SupplierChargeController::class, 'index']);
    Route::post('/', [SupplierChargeController::class, 'store']);
    Route::post('/{supplierCharge}/cancel', [SupplierChargeController::class, 'cancel']);
});

Route::middleware(['auth:sanctum'])->prefix('abonos-proveedores')->group(function () {
    Route::get('/', [AbonoProveedorController::class, 'index']);
    Route::get('/proveedores/search', [AbonoProveedorController::class, 'searchProveedores']);
    Route::get('/tipos-pago', [AbonoProveedorController::class, 'getTiposPago']);
    Route::post('/', [AbonoProveedorController::class, 'store']);
    Route::post('/{id}/cancel', [AbonoProveedorController::class, 'cancel']);
});

Route::middleware(['auth:sanctum'])->prefix('contacts')->group(function () {
    Route::get('/', [ContactController::class, 'index']);
    Route::post('/', [ContactController::class, 'store']);
    Route::get('/{contact}', [ContactController::class, 'show']);
    Route::match(['put', 'patch'], '/{contact}', [ContactController::class, 'update']);
    Route::delete('/{contact}', [ContactController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->prefix('activities')->group(function () {
    Route::get('/', [ActivityController::class, 'index']);
    Route::post('/', [ActivityController::class, 'store']);
    Route::get('/{activity}', [ActivityController::class, 'show']);
    Route::match(['put', 'patch'], '/{activity}', [ActivityController::class, 'update']);
    Route::delete('/{activity}', [ActivityController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->prefix('cargos-clientes')->group(function () {
    Route::get('/', [CargoClienteController::class, 'index']);
    Route::get('/search-customers', [CargoClienteController::class, 'searchCustomers']);
    Route::post('/', [CargoClienteController::class, 'store']);
    Route::post('/{id}/cancel', [CargoClienteController::class, 'cancel']);
});

Route::middleware(['auth:sanctum'])->prefix('otros-gastos')->group(function () {
    Route::get('/', [GastoController::class, 'index']);
    Route::post('/create', [GastoController::class, 'store']);
    Route::post('/{folio}/cancel', [GastoController::class, 'cancel']);
});

Route::middleware(['auth:sanctum'])->prefix('otros-ingresos')->group(function () {
    Route::get('/', [EntradaController::class, 'index']);
    Route::post('/entradas', [EntradaController::class, 'store']);
    Route::put('/entradas/{entrada}', [EntradaController::class, 'update']);
    Route::delete('/{id}/cancel', [EntradaController::class, 'cancel']);
});

Route::middleware(['auth:sanctum'])->prefix('fondo-fijo')->group(function () {
    Route::get('/estado', [FondoFijoController::class, 'estadoCaja']);
    Route::post('/abrir', [FondoFijoController::class, 'abrirCaja']);
    Route::post('/cerrar/{id}', [FondoFijoController::class, 'cerrarCaja']);
    Route::get('/historial', [FondoFijoController::class, 'historial']);
});

Route::middleware(['auth:sanctum'])->prefix('quotations')->group(function () {
    Route::get('/', [QuotationController::class, 'index']);
    Route::post('/', [QuotationController::class, 'store']);
    Route::get('/{quotation}/pdf', [QuotationController::class, 'pdf']);
    Route::get('/{quotation}', [QuotationController::class, 'show']);
    Route::match(['put', 'patch'], '/{quotation}', [QuotationController::class, 'update']);
    Route::delete('/{quotation}', [QuotationController::class, 'destroy']);
});
