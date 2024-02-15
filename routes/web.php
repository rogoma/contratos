<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UsersController;

use App\Http\Controllers\Admin\PositionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\DependencyTypesController;
use App\Http\Controllers\Admin\ModalitiesController;
use App\Http\Controllers\Admin\OrderStatesController;
use App\Http\Controllers\Admin\UocTypesController;
use App\Http\Controllers\Admin\FundingSourcesController;
use App\Http\Controllers\Admin\FinancialOrganismsController;
use App\Http\Controllers\Admin\FinancialLevelsController;
use App\Http\Controllers\Admin\DependenciesController;
use App\Http\Controllers\Admin\ProvidersController;
use App\Http\Controllers\Admin\RegionsController;
use App\Http\Controllers\Admin\DepartmentsController;
use App\Http\Controllers\Admin\DistrictsController;
use App\Http\Controllers\Admin\ExpenditureObjectsController;
use App\Http\Controllers\Admin\FilesController;
use App\Http\Controllers\Admin\OrderMeasurementUnitsController;
use App\Http\Controllers\Admin\OrderPresentationsController;
use App\Http\Controllers\Admin\ProgramMeasurementUnitsController;
use App\Http\Controllers\Admin\ProgramTypesController;
use App\Http\Controllers\Admin\ProgramsController;
use App\Http\Controllers\Admin\SubProgramsController;
use App\Http\Controllers\Admin\Level1CatalogCodeController;
use App\Http\Controllers\Admin\Level5CatalogCodeController;

use App\Http\Controllers\Order\OrdersController;
use App\Http\Controllers\Order\ItemsController;
use App\Http\Controllers\Order\ItemsAdjudicaController;
use App\Http\Controllers\Order\ItemAwardHistoriesController;
use App\Http\Controllers\Order\BudgetRequestProvidersController;
use App\Http\Controllers\DeriveOrder\DeriveOrdersController;
use App\Http\Controllers\Order\SimeseOrdersController;
use App\Http\Controllers\Order\OrdersFilesController;

use App\Http\Controllers\Planning\PlanningsController;
use App\Http\Controllers\Planning\ObjectionsController;
use App\Http\Controllers\Planning\ObjectionsResponsesController;

use App\Http\Controllers\Tender\TendersController;
use App\Http\Controllers\MinorPurchase\MinorPurchasesController;
use App\Http\Controllers\Award\AwardsController;
use App\Http\Controllers\Contract\ContractsController;
use App\Http\Controllers\Exception\ExceptionsController;

use App\Http\Controllers\Uta\UtasController;
use App\Http\Controllers\Comite\ComitesController;
use App\Http\Controllers\LegalAdvice\LegalAdvicesController;
use App\Http\Controllers\Coordination\CoordinationsController;
use App\Http\Controllers\Dgaf\DgafsController;
use App\Http\Controllers\Documental\DocumentalsController;

// use App\Http\Controllers\Report\PdfsController;
use App\Http\Controllers\Report\ReportsController;
// use PHPJasper\PHPJasper;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::post('/login', [HomeController::class, 'checkLogin'])->name('checkLogin');
Route::post('/logout', [HomeController::class, 'logout'])->name('logout');
Route::get('/', [HomeController::class, 'index'])->name('home');
// obtener el token csrf
Route::get('/token', function (Request $request) {
    $token = csrf_token();
    return response()->json(['status' => 'success', 'token' => $token], 200);
});

Route::middleware('auth')->group(function () {  // Las siguientes funcionalidades son accedidas solamente estando el usuario autenticado
    /********** MODULO ADMINISTRADOR **********/

    //USUARIOS A EXCEL - //PARA GENERAR ARCHIVOS EXCEL PRIMERO SE COLOCA EL GET ANTES DEL RESOURCE
    Route::get('/users/exportarexcel', [UsersController::class, 'exportarExcel']);
    Route::get('/users/create2', [UsersController::class, 'create2'])->name('users.create2');
    Route::get('/users/change_pass', [UsersController::class, 'change_pass'])->name('users.change_pass');

    Route::resource('users', UsersController::class);   // index, create, update, delete


    //ORDER_PRESENTATIONS A EXCEL - //PARA GENERAR ARCHIVOS EXCEL PRIMERO SE COLOCA EL GET ANTES DEL RESOURCE
    Route::get('/order_presentations/exportarexcel', [OrderPresentationsController::class, 'exportarExcel']);
    Route::resource('order_presentations', OrderPresentationsController::class);


    //PARA CAMBIAR PASSWORD
    Route::put('users/{id}/update_pass', [UsersController::class, 'update_pass'])->name('users.update_pass');

    //PARA RESETEAR PASSWORD
    Route::get('users/{id}/reset_pass', [UsersController::class, 'reset_pass'])->name('users.reset_pass');



    Route::resource('positions', PositionsController::class);
    Route::resource('roles', RolesController::class);
    Route::resource('permissions', PermissionsController::class);
    Route::resource('modalities', ModalitiesController::class);
    Route::resource('dependency_types', DependencyTypesController::class);
    Route::resource('uoc_types', UocTypesController::class);

    //MOSTRAR EN EXCEL DEPENDENCIAS
    Route::get('/dependencies/exportarexcel', [DependenciesController::class, 'exportarExcel']);
    Route::resource('dependencies', DependenciesController::class);

    Route::resource('order_states', OrderStatesController::class);
    Route::resource('funding_sources', FundingSourcesController::class);
    Route::resource('financial_organisms', FinancialOrganismsController::class);
    Route::resource('financial_levels', FinancialLevelsController::class);
    Route::resource('providers', ProvidersController::class);
    Route::resource('regions', RegionsController::class);
    Route::resource('departments', DepartmentsController::class);
    Route::resource('districts', DistrictsController::class);
    Route::resource('expenditure_objects', ExpenditureObjectsController::class);
    Route::resource('files', FilesController::class);
    Route::resource('order_measurement_units', OrderMeasurementUnitsController::class);

    Route::resource('program_measurement_units', ProgramMeasurementUnitsController::class);
    Route::resource('program_types', ProgramTypesController::class);
    Route::resource('programs', ProgramsController::class);
    Route::resource('sub_programs', SubProgramsController::class);

    Route::resource('catalog_level1s', Level1CatalogCodeController::class);

    Route::resource('catalog_level5s', Level5CatalogCodeController::class);
    Route::get('catalog_level5s/{id}/edit_c5', [Level5CatalogCodeController::class, 'edit_c5'])->name('catalog_level5s.edit_c5');

    // Route::delete('catalog_level5s/{id}/delete', [Level5CatalogCodeController::class, 'destroy'])->name('catalog_level5s.delete');

    // Route::delete('orders/files/{file_id}/delete', [OrdersFilesController::class, 'destroy'])->name('orders.files.delete');


    //RECURSOS ANIDADOS
    Route::resource('orders.items', ItemsController::class); //Recurso anidado, es igual a /orders/{order_id}/items/{item_id}
    Route::resource('orders.items_adjudica', ItemsAdjudicaController::class); //Recurso anidado, es igual a /orders/{order_id}/items/{item_id}
    Route::resource('orders.items_budget', BudgetRequestProvidersController::class); //Recurso anidado, es igual a /orders/{order_id}/items/{item_id}

    //DESDE ACA PARA AGREGAR EMPRESAS ADJUDICADAS
    //Route::resource('orders.budget_request_providers', BudgetRequestProvidersController::class); //Recurso anidado, es igual a /orders/{order_id}/items/{item_id}

    Route::get('orders/{id}/budget_request_providers', [BudgetRequestProvidersController::class, 'index'])->name('orders.budget_request_providers.index');
    //EMPRESAS COTIZANTES DE REQUIRIENTE
    Route::get('orders/{id}/budget_request_providers/create', [BudgetRequestProvidersController::class, 'create'])->name('orders.budget_request_providers.create');
    Route::post('orders/{id}/budget_request_providers/create', [BudgetRequestProvidersController::class, 'store'])->name('orders.budget_request_providers.store');
    //EMPRESAS COTIZANTES DE PAC
    Route::get('orders/{id}/budget_request_providers/create_PAC', [BudgetRequestProvidersController::class, 'create_PAC'])->name('orders.budget_request_providers.create_PAC');
    Route::post('orders/{id}/budget_request_providers/create_PAC', [BudgetRequestProvidersController::class, 'store_PAC'])->name('orders.budget_request_providers.store_PAC');


    //PARA EDITAR EMPRESAS COTIZANTES, INVITADAS O PARTICIPANTES
    Route::get('orders/{id}/budget_request_providers/editar', [BudgetRequestProvidersController::class, 'editar'])->name('orders.budget_request_providers.editar');
    Route::put('orders/{id}/budget_request_providers/editar', [BudgetRequestProvidersController::class, 'updatetype1'])->name('orders.budget_request_providers.updatetype1');

    //PARA EDITAR EMPRESAS COTIZANTES REQUIRIENTE
    Route::get('orders/{id}/budget_request_providers/edit_providers', [BudgetRequestProvidersController::class, 'edit_providers'])->name('orders.budget_request_providers.edit_providers');
    Route::put('orders/{id}/budget_request_providers/edit_providers', [BudgetRequestProvidersController::class, 'update_providers'])->name('orders.budget_request_providers.update_providers');

    //PARA EDITAR EMPRESAS COTIZANTES PAC
    Route::get('orders/{id}/budget_request_providers/edit_providersPAC', [BudgetRequestProvidersController::class, 'edit_providersPAC'])->name('orders.budget_request_providers.edit_providersPAC');
    Route::put('orders/{id}/budget_request_providers/edit_providersPAC', [BudgetRequestProvidersController::class, 'update_providersPAC'])->name('orders.budget_request_providers.update_providersPAC');


    Route::delete('orders/{id}/budget_request_providers', [BudgetRequestProvidersController::class, 'destroy'])->name('orders.budget_request_providers.delete');

    Route::get('orders/{id}/budget_request_providers/create_providers_guests', [BudgetRequestProvidersController::class, 'create_providers_guests'])->name('orders.budget_request_providers.create_providers_guests');
    Route::post('orders/{id}/budget_request_providers/create_providers_guests', [BudgetRequestProvidersController::class, 'store_providers_guests'])->name('orders.budget_request_providers.store_providers_guests');
    Route::get('orders/{id}/budget_request_providers/edit_providers_guests', [BudgetRequestProvidersController::class, 'edit_providers_guests'])->name('orders.budget_request_providers.edit_providers_guests');
    Route::put('orders/{id}/budget_request_providers/edit_providers_guests', [BudgetRequestProvidersController::class, 'update_providers_guests'])->name('orders.budget_request_providers.update_providers_guests');

    Route::delete('orders/{id}/budget_request_providers_providers_guests', [BudgetRequestProvidersController::class, 'destroy_providers_guests'])->name('orders.budget_request_providers.delete_providers_guests');

    Route::get('orders/{id}/budget_request_providers/create_providers_participants', [BudgetRequestProvidersController::class, 'create_providers_participants'])->name('orders.budget_request_providers.create_providers_participants');
    Route::post('orders/{id}/budget_request_providers/create_providers_participants', [BudgetRequestProvidersController::class, 'store_providers_participants'])->name('orders.budget_request_providers.store_providers_participants');
    Route::get('orders/{id}/budget_request_providers/edit_providers_participants', [BudgetRequestProvidersController::class, 'edit_providers_participants'])->name('orders.budget_request_providers.edit_providers_participants');
    Route::put('orders/{id}/budget_request_providers/edit_providers_participants', [BudgetRequestProvidersController::class, 'update_providers_participants'])->name('orders.budget_request_providers.update_providers_participants');
    Route::delete('orders/{id}/budget_request_providers_providers_participants', [BudgetRequestProvidersController::class, 'destroy_providers_participants'])->name('orders.budget_request_providers.delete_providers_participants');

    Route::get('orders/{id}/budget_request_providers/create_providers_awarded', [BudgetRequestProvidersController::class, 'create_providers_awarded'])->name('orders.budget_request_providers.create_providers_awarded');
    Route::post('orders/{id}/budget_request_providers/create_providers_awarded', [BudgetRequestProvidersController::class, 'store_providers_awarded'])->name('orders.budget_request_providers.store_providers_awarded');
    Route::get('orders/{id}/budget_request_providers/edit_providers_awarded', [BudgetRequestProvidersController::class, 'edit_providers_awarded'])->name('orders.budget_request_providers.edit_providers_awarded');
    Route::put('orders/{id}/budget_request_providers/edit_providers_awarded', [BudgetRequestProvidersController::class, 'update_providers_awarded'])->name('orders.budget_request_providers.update_providers_awarded');
    Route::delete('orders/{id}/budget_request_providers_providers_awarded', [BudgetRequestProvidersController::class, 'destroy_providers_awarded'])->name('orders.budget_request_providers.delete_providers_awarded');


    // SE AGREGA PARA EDITAR PROVEEDORES EN CONTRATOS
    Route::get('orders/{id}/budget_request_providers/{budget}edit_providers_contracts', [BudgetRequestProvidersController::class, 'edit_providers_contracts'])->name('orders.budget_request_providers.edit_providers_contracts');
    Route::put('orders/{id}/budget_request_providers/{budget}edit_providers_contracts', [BudgetRequestProvidersController::class, 'update_providers_contracts'])->name('orders.budget_request_providers.update_providers_contracts');

    // /orders/{order_id}/items/{item_id}


    /********** MODULO PEDIDOS **********/
    Route::get('/orders/uploadExcel', [OrdersController::class, 'uploadExcel'])->name('orders.uploadExcel');
    Route::post('/orders/uploadExcel', [OrdersController::class, 'storeExcel'])->name('orders.storeExcel');

    //PEDIDOS A EXCEL
    Route::get('/orders/exportarexcel', [OrdersController::class, 'exportarExcel']); // PONER DEBAJO DE LAS RUTAS /orders/uploadExcel para que funcione
    Route::get('/orders/exportarexcel2', [OrdersController::class, 'exportarExcel2']); // PONER DEBAJO DE LAS RUTAS /orders/uploadExcel para que funcione

    Route::resource('orders', OrdersController::class);    // PONER DEBAJO DE LAS RUTAS /orders/uploadExcel para que funcione

    Route::post('orders/derive/{order_id}', [OrdersController::class, 'derive'])->name('orders.derive');
    Route::post('orders/anuleOrder/{order_id}', [OrdersController::class, 'anuleOrder'])->name('orders.anuleOrder');

    Route::post('orders/anuleDerive/{order_id}', [OrdersController::class, 'anuleDerive'])->name('orders.anuleDerive');


    Route::get('/orders/{id}/uploadExcelItem', [ItemsController::class, 'uploadExcel'])->name('orders.items.uploadExcel');
    // Contrato Abierto
    Route::post('/orders/{id}/uploadExcel', [ItemsController::class, 'storeExcel'])->name('orders.items.storeExcel');
    // Contrato Cerrado
    Route::post('/orders/{id}/uploadExcel2', [ItemsController::class, 'storeExcel2'])->name('orders.items.storeExcel2');
    // Contrato Abierto con Mmin y Mmax
    Route::post('/orders/{id}/uploadExcel3', [ItemsController::class, 'storeExcel3'])->name('orders.items.storeExcel3');


    Route::get('/orders/{id}/uploadExcelAw', [ItemsAdjudicaController::class, 'uploadExcelAw'])->name('orders.items_adjudica.uploadExcelAw');
    // Contrato Abierto
    Route::post('/orders/{id}/uploadExcelAw', [ItemsAdjudicaController::class, 'storeExcelAw'])->name('orders.items_adjudica.storeExcelAw');
    // Contrato Cerrado
    // Route::post('/orders/{id}/uploadExcel2', [ItemsAdjudicaController::class, 'storeExcel2'])->name('orders.items_adjudica.storeExcel2');
    // Contrato Abierto con Mmin y Mmax
    // Route::post('/orders/{id}/uploadExcel3', [ItemsAdjudicaController::class, 'storeExcel3'])->name('orders.items_adjudica.storeExcel3');

    //Route::resource('orders.items', ItemsController::class); //Recurso anidado, es igual a /orders/{order_id}/items/{item_id}
    //Route::resource('orders.items_adjudica', ItemsAdjudicaController::class); //Recurso anidado, es igual a /orders/{order_id}/items/{item_id}
    //Route::resource('orders.budget_request_providers', BudgetRequestProvidersController::class); //Recurso anidado, es igual a /orders/{order_id}/items/{item_id}

    // Contrato Abierto
    // Route::post('/orders/{id}/uploadExcel', [ItemsController::class, 'storeExcel'])->name('orders.items.storeExcel');
    // // Contrato Cerrado
    // Route::post('/orders/{id}/uploadExcel2', [ItemsController::class, 'storeExcel2'])->name('orders.items.storeExcel2');
    // // Contrato Abierto con Mmin y Mmax
    // Route::post('/orders/{id}/uploadExcel3', [ItemsController::class, 'storeExcel3'])->name('orders.items.storeExcel3');


    Route::get('items/{item_id}/item_award_histories', [ItemAwardHistoriesController::class, 'index'])->name('item_award_histories.index');
    Route::get('items/{item_id}/item_award_histories/create', [ItemAwardHistoriesController::class, 'create'])->name('item_award_histories.create');
    Route::post('items/{item_id}/item_award_histories/create', [ItemAwardHistoriesController::class, 'store'])->name('item_award_histories.store');
    Route::get('items/{item_id}/item_award_histories/edit', [ItemAwardHistoriesController::class, 'edit'])->name('item_award_histories.edit');
    Route::post('items/{item_id}/item_award_histories/edit', [ItemAwardHistoriesController::class, 'update'])->name('item_award_histories.update');
    Route::delete('items/{item_id}/item_award_histories', [ItemAwardHistoriesController::class, 'destroy'])->name('item_award_histories.destroy');

    //BUSCA ITEMS DE CATALOGO 5
    Route::get('items/search', [ItemsController::class, 'search']);
    //BUSCA ITEMS DE CATALOGO 4
    Route::get('items/search4', [ItemsController::class, 'search4']);


    Route::get('orders/simese/{order_id}/index', [SimeseOrdersController::class, 'index'])->name('orders.simese.index');
    Route::get('orders/simese/{order_id}/create', [SimeseOrdersController::class, 'create'])->name('orders.simese.create');
    Route::post('orders/simese/{order_id}/create', [SimeseOrdersController::class, 'store'])->name('orders.simese.store');
    Route::get('orders/simese/{order_id}/edit', [SimeseOrdersController::class, 'edit'])->name('orders.simese.edit');
    Route::put('orders/simese/{order_id}/edit', [SimeseOrdersController::class, 'update'])->name('orders.simese.update');
    Route::get('orders/files/{order_id}/index', [OrdersFilesController::class, 'index'])->name('orders.files.index');
    Route::get('orders/files/{order_id}/create', [OrdersFilesController::class, 'create'])->name('orders.files.create');
    Route::post('orders/files/{order_id}/create', [OrdersFilesController::class, 'store'])->name('orders.files.store');

    Route::get('orders/files/{order_id}/create_filedncp', [OrdersFilesController::class, 'create_filedncp'])->name('orders.files.create_filedncp');
    Route::post('orders/files/{order_id}/create_filedncp', [OrdersFilesController::class, 'store_filedncp'])->name('orders.files.store_filedncp');

    Route::get('orders/files/{order_id}/create_filedncp_con', [OrdersFilesController::class, 'create_filedncp_con'])->name('orders.files.create_filedncp_con');
    Route::post('orders/files/{order_id}/create_filedncp_con', [OrdersFilesController::class, 'store_filedncp_con'])->name('orders.files.store_filedncp_con');


    Route::get('orders/files/{order_id}/create_cuadro_compar', [OrdersFilesController::class, 'create_cuadro_compar'])->name('orders.files.create_cuadro_compar');
    Route::post('orders/files/{order_id}/create_cuadro_compar', [OrdersFilesController::class, 'store_cuadro_compar'])->name('orders.files.store_cuadro_compar');

    Route::delete('orders/files/{file_id}/delete', [OrdersFilesController::class, 'destroy'])->name('orders.files.delete');

    Route::get('orders/files/{file_id}/download', [OrdersFilesController::class, 'download'])->name('orders.files.download');

    Route::get('excel/pedidos', [OrdersController::class, 'ArchivoPedido'])->name('excel.pedidos');
    Route::get('excel/items', [OrdersController::class, 'ArchivoItem'])->name('excel.items');
    Route::get('excel/items2', [OrdersController::class, 'ArchivoItem2'])->name('excel.items2');
    Route::get('excel/items3', [OrdersController::class, 'ArchivoItem3'])->name('excel.items3');


    //excel/itemsAw


    /********** MODULO DERIVAR PEDIDOS **********/
    Route::get('derive_orders/{order_id}/create', [DeriveOrdersController::class, 'create'])->name('derive_orders.create');
    Route::post('derive_orders/{order_id}/create', [DeriveOrdersController::class, 'store'])->name('derive_orders.store');
    Route::get('derive_orders/{order_id}/edit', [DeriveOrdersController::class, 'edit'])->name('derive_orders.edit');
    Route::put('derive_orders/{order_id}/edit', [DeriveOrdersController::class, 'update'])->name('derive_orders.update');

    Route::post('derive_orders/derivePbc/{order_id}', [DeriveOrdersController::class, 'derivePbc'])->name('derive_orders.derivePbc');

    Route::post('derive_orders/deriveDictamen/{order_id}', [DeriveOrdersController::class, 'deriveDictamen'])->name('derive_orders.deriveDictamen');
    Route::post('derive_orders/deriveDictamenEVAL/{order_id}', [DeriveOrdersController::class, 'deriveDictamenEVAL'])->name('derive_orders.deriveDictamenEVAL');
    Route::post('derive_orders/deriveExcepciones/{order_id}', [DeriveOrdersController::class, 'deriveExcepciones'])->name('derive_orders.deriveExcepciones');

    Route::post('derive_orders/deriveInforme/{order_id}', [DeriveOrdersController::class, 'deriveInforme'])->name('derive_orders.deriveInforme');

    Route::post('derive_orders/deriveInformeUTA/{order_id}', [DeriveOrdersController::class, 'deriveInformeUTA'])->name('derive_orders.deriveInformeUTA');


    /********** MODULO PLANIFICACIÓN **********/
    Route::resource('plannings', PlanningsController::class);
    Route::resource('plannings.objections', ObjectionsController::class);
    Route::resource('plannings.objections_responses', ObjectionsResponsesController::class);
    Route::post('plannings/recibe_order/{order_id}', [PlanningsController::class, 'recibeOrder'])->name('plannings.recibeOrder');

    Route::post('plannings/recibe_orderObs/{order_id}', [PlanningsController::class, 'recibeOrderObs'])->name('plannings.recibeOrderObs');
    Route::post('plannings/recibe_orderObsAdju/{order_id}', [PlanningsController::class, 'recibeOrderObsAdju'])->name('plannings.recibeOrderObsAdju');

    Route::get('plannings/orders/{order_id}/edit', [PlanningsController::class, 'edit'])->name('plannings.orders.edit');

    // Route::put('plannings/orders/{order_id}/edit', [PlanningsController::class, 'update'])->name('plannings.orders.update');

    Route::put('plannings/orders/{order_id}/edit', [PlanningsController::class, 'update'])->name('plannings.orders.update');

    Route::post('plannings/derive_order/{order_id}', [PlanningsController::class, 'deriveOrder'])->name('plannings.deriveOrder');
    Route::post('plannings/derive_orderObs/{order_id}', [PlanningsController::class, 'deriveOrderObs'])->name('plannings.deriveOrderObs');

    // Route::post('plannings/derive_adjudica/{order_id}', [PlanningsController::class, 'deriveAdjudica'])->name('plannings.deriveAdjudica');

    //VERIFICAR ESTO CUANDO SE REMITE DESDE ADJUDICACIONES
    // Route::post('plannings/derive_orderObsAdju/{order_id}', [PlanningsController::class, 'deriveOrderObs'])->name('plannings.deriveOrderObs');
    // Route::post('plannings/derive_adjudica/{order_id}', [PlanningsController::class, 'deriveAdjudica'])->name('plannings.deriveAdjudica');



    /**************** REPORTES MPDF **************/
    // Route::get('pdf','PdfsController@getIndex');
    // Route::get('pdf/generar','PdfsController@getGenerar');
    // Route::get('pdf/pdf', [PdfsController::class, 'getIndex'])->name('pdf.pdf');
    // Route::get('pdf/generarPDF', [PdfsController::class, 'generarPDF'])->name('pdf.generarPDF');
    // Route::get('pdf/tipres2', [ReportsController::class, 'pdfMPDF'])->name('pdf.ptipres2');


    /*************** REPORTES DOMPDF **************/
    //REPORTE DE CONTRATOS TODOS
    Route::get('pdf/panel_contracts', [ReportsController::class, 'generarContracts'])->name('pdf.panel_contracts');
    //REPORTE DE CONTRATOS EN CURSO
    Route::get('pdf/panel_contracts1', [ReportsController::class, 'generarContracts1'])->name('pdf.panel_contracts1');
    //REPORTE DE CONTRATOS CERRADOS
    Route::get('pdf/panel_contracts2', [ReportsController::class, 'generarContracts2'])->name('pdf.panel_contracts2');
     //REPORTE DE CONTRATOS RESCINDIDOS
     Route::get('pdf/panel_contracts3', [ReportsController::class, 'generarContracts3'])->name('pdf.panel_contracts3');


    Route::get('pdf/users', [ReportsController::class, 'pdfUsers'])->name('pdf.users');
    Route::get('pdf/users2', [ReportsController::class, 'pdfUsers2'])->name('pdf.users2');

    Route::get('pdf/dependencies', [ReportsController::class, 'dependencies'])->name('pdf.dependencies');


    Route::get('pdf/modalities', [ReportsController::class, 'pdfArchivo'])->name('pdf.modalities');
    Route::get('pdf/modalities2', [ReportsController::class, 'generarModalities'])->name('pdf.modalities');
    Route::get('pdf/reporte1/{order_id}', [ReportsController::class, 'generarForm1'])->name('pdf.form1');
    Route::get('pdf/reporte2/{order_id}', [ReportsController::class, 'generarForm2'])->name('pdf.form2');
    Route::get('pdf/panel', [ReportsController::class, 'generarPanel'])->name('pdf.panel');
    Route::get('pdf/tipres', [ReportsController::class, 'pdfTipres'])->name('pdf.tipres');
    Route::get('pdf/tipresunid', [ReportsController::class, 'pdfTipresUnid'])->name('pdf.tipresunid');
    Route::get('pdf/panel_licit', [ReportsController::class, 'generarPanelLicit'])->name('pdf.panel_licit');
    Route::get('pdf/panel_minor', [ReportsController::class, 'generarPanelMinor'])->name('pdf.panel_minor');
    Route::get('pdf/panel_exception', [ReportsController::class, 'generarPanelException'])->name('pdf.panel_exception');

    Route::get('pdf/panel_uta', [ReportsController::class, 'generarPanelUta'])->name('pdf.panel_uta');
    Route::get('pdf/panel_uta2', [ReportsController::class, 'generarPanelUta2'])->name('pdf.panel_uta2');
    Route::get('pdf/panel_uta3', [ReportsController::class, 'generarPanelUta3'])->name('pdf.panel_uta3');



    Route::get('pdf/panel_pedidos/{dependency}', [ReportsController::class, 'generarPanelPedido'])->name('pdf.panel_pedido');
    Route::get('pdf/panel_pedidos2/{dependency}', [ReportsController::class, 'generarPanelPedido2'])->name('pdf.panel_pedido2');

    Route::get('excel', [OrdersController::class, 'excel']);
    Route::get('pdf/reporte3/{order_id}', [ReportsController::class, 'generarForm3'])->name('pdf.form3');

    Route::get('pdf/reporte31/{order_id}', [ReportsController::class, 'generarForm31'])->name('pdf.form31');

    Route::get('pdf/reporte4/{order_id}', [ReportsController::class, 'generarForm4'])->name('pdf.form4');
    Route::get('pdf/prefs/{order_id}', [ReportsController::class, 'pdfPrefs'])->name('pdf.prefs');

    //SECUENCIA LICITACIONES
    Route::get('pdf/secuencia', [ReportsController::class, 'pdfSecuencia'])->name('pdf.secuencia');
    //SECUENCIA COMPRAS MENORES
    Route::get('pdf/secuencia2', [ReportsController::class, 'pdfSecuencia2'])->name('pdf.secuencia2');
    //SECUENCIA CVE
    Route::get('pdf/secuencia3', [ReportsController::class, 'pdfSecuencia3'])->name('pdf.secuencia3');

    //INSTRUCTIVO CAMBIAR PASSWORD
    Route::get('pdf/change_pass', [ReportsController::class, 'pdfChange_pass'])->name('pdf.change_pass');

    //ITEMS SE PASA A EXCEL PEDIDOS
    // Route::get('pedidos/export-excel2/', [ItemsAdjudicaController::class, 'exportExcel2']);
    // Route::get('pdf/panel_uta', [ReportsController::class, 'generarPanelUta'])->name('pdf.panel_uta');

    /********** MODULO TENDER - LICITACIÓN **********/
    Route::get('tenders/getNotifications', [TendersController::class, 'getNotifications'])->name('tenders.getNotifications');
    Route::resource('tenders', TendersController::class);
    Route::resource('tenders.queries', App\Http\Controllers\Tender\QueriesController::class);
    Route::resource('tenders.queries_responses', App\Http\Controllers\Tender\QueriesResponsesController::class);
    Route::resource('tenders.objections', App\Http\Controllers\Tender\ObjectionsController::class);
    Route::resource('tenders.objections_responses', App\Http\Controllers\Tender\ObjectionsResponsesController::class);
    Route::post('tenders/recibe_order/{order_id}', [TendersController::class, 'recibeOrder'])->name('tenders.recibeOrder');
    Route::post('tenders/recibe_order_uta/{order_id}', [TendersController::class, 'recibeOrderUTA'])->name('tenders.recibeOrderUTA');
    Route::get('tenders/orders/{order_id}/edit', [TendersController::class, 'edit'])->name('tenders.orders.edit');
    Route::put('tenders/orders/{order_id}/edit', [TendersController::class, 'update'])->name('tenders.orders.update');
    Route::post('tenders/derive_order/{order_id}', [TendersController::class, 'deriveOrder'])->name('tenders.deriveOrder');
    Route::post('tenders/derive_comite/{order_id}', [TendersController::class, 'deriveComite'])->name('tenders.deriveComite');
    Route::post('tenders/update_queries_deadline/{order_id}', [TendersController::class, 'updateQueriesDeadline'])->name('tenders.updateQueriesDeadline');

    Route::post('tenders/derivePAC/{order_id}', [TendersController::class, 'derivePAC'])->name('tenders.derivePAC');


    /********** MODULO MINOR_PURCHASES - COMPRAS MENORES **********/
    Route::get('minor_purchases/getNotifications', [MinorPurchasesController::class, 'getNotifications'])->name('minor_purchases.getNotifications');
    Route::resource('minor_purchases', MinorPurchasesController::class);
    Route::resource('minor_purchases.queries', App\Http\Controllers\MinorPurchase\QueriesController::class);
    Route::resource('minor_purchases.queries_responses', App\Http\Controllers\MinorPurchase\QueriesResponsesController::class);
    Route::resource('minor_purchases.objections', App\Http\Controllers\MinorPurchase\ObjectionsController::class);
    Route::resource('minor_purchases.objections_responses', App\Http\Controllers\MinorPurchase\ObjectionsResponsesController::class);
    Route::post('minor_purchases/recibe_order/{order_id}', [MinorPurchasesController::class, 'recibeOrder'])->name('minor_purchases.recibeOrder');
    Route::post('minor_purchases/recibe_order_uta/{order_id}', [MinorPurchasesController::class, 'recibeOrderUTA'])->name('minor_purchases.recibeOrderUTA');
    Route::get('minor_purchases/orders/{order_id}/edit', [MinorPurchasesController::class, 'edit'])->name('minor_purchases.orders.edit');
    Route::put('minor_purchases/orders/{order_id}/edit', [MinorPurchasesController::class, 'update'])->name('minor_purchases.orders.update');
    Route::post('minor_purchases/derive_order/{order_id}', [MinorPurchasesController::class, 'deriveOrder'])->name('minor_purchases.deriveOrder');
    Route::post('minor_purchases/derive_comite/{order_id}', [MinorPurchasesController::class, 'deriveComite'])->name('minor_purchases.deriveComite');
    Route::post('minor_purchases/update_queries_deadline/{order_id}', [MinorPurchasesController::class, 'updateQueriesDeadline'])->name('minor_purchases.updateQueriesDeadline');

    Route::post('minor_purchases/recibe_pbc_uta/{order_id}', [MinorPurchasesController::class, 'recibePBCdeUTA'])->name('minor_purchases.recibePBCdeUTA');
    Route::post('minor_purchases/derive_uta/{order_id}', [MinorPurchasesController::class, 'deriveUTA'])->name('minor_purchases.deriveUTA');
    Route::post('minor_purchases/recibeOrderReparo/{order_id}', [MinorPurchasesController::class, 'recibeOrderReparo'])->name('minor_purchases.recibeOrderReparo');
    Route::post('/minor_purchases/deriveAJ_NoCovid/{order_id}', [MinorPurchasesController::class, 'deriveAJ_NoCovid'])->name('minor_purchases.deriveAJ_NoCovid');

    Route::post('minor_purchases/derivePAC/{order_id}', [MinorPurchasesController::class, 'derivePAC'])->name('minor_purchases.derivePAC');



    /********** MODULO EXCEPTIONS - PROCESOS COMPLEMENTARIOS Y EXCEPCIONES **********/
    Route::get('exceptions/getNotifications', [ExceptionsController::class, 'getNotifications'])->name('exceptions.getNotifications');
    Route::resource('exceptions', ExceptionsController::class);
    Route::resource('exceptions.queries', App\Http\Controllers\Exception\QueriesController::class);
    Route::resource('exceptions.queries_responses', App\Http\Controllers\Exception\QueriesResponsesController::class);
    Route::resource('exceptions.objections', App\Http\Controllers\Exception\ObjectionsController::class);
    Route::resource('exceptions.objections_responses', App\Http\Controllers\Exception\ObjectionsResponsesController::class);
    Route::post('exceptions/recibe_order/{order_id}', [ExceptionsController::class, 'recibeOrder'])->name('exceptions.recibeOrder');
    Route::post('exceptions/recibe_order_uta/{order_id}', [ExceptionsController::class, 'recibeOrderUTA'])->name('exceptions.recibeOrderUTA');
    Route::get('exceptions/orders/{order_id}/edit', [ExceptionsController::class, 'edit'])->name('exceptions.orders.edit');
    Route::put('exceptions/orders/{order_id}/edit', [ExceptionsController::class, 'update'])->name('exceptions.orders.update');
    Route::post('exceptions/derive_order/{order_id}', [ExceptionsController::class, 'deriveOrder'])->name('exceptions.deriveOrder');
    Route::post('exceptions/derivePAC/{order_id}', [ExceptionsController::class, 'derivePAC'])->name('exceptions.derivePAC');
    Route::post('exceptions/derive_comite/{order_id}', [ExceptionsController::class, 'deriveComite'])->name('exceptions.deriveComite');

    Route::post('exceptions/update_queries_deadline/{order_id}', [ExceptionsController::class, 'updateQueriesDeadline'])->name('exceptions.updateQueriesDeadline');


    /********** MODULO AWARDS - ADJUDICACIONES **********/
    Route::get('awards/getNotifications', [AwardsController::class, 'getNotifications'])->name('awards.getNotifications');
    Route::resource('awards', AwardsController::class);
    Route::resource('awards.objections', App\Http\Controllers\Award\ObjectionsController::class);
    Route::resource('awards.objections_responses', App\Http\Controllers\Award\ObjectionsResponsesController::class);
    Route::resource('awards.queries', App\Http\Controllers\Award\QueriesController::class);
    Route::resource('awards.queries_responses', App\Http\Controllers\Award\QueriesResponsesController::class);
    Route::post('awards/recibe_order/{order_id}', [AwardsController::class, 'recibeOrder'])->name('awards.recibeOrder');
    Route::post('awards/recibe_order2ET/{order_id}', [AwardsController::class, 'recibeOrder2ET'])->name('awards.recibeOrder2ET');
    Route::get('awards/orders/{order_id}/edit', [AwardsController::class, 'edit'])->name('awards.orders.edit');
    Route::put('awards/orders/{order_id}/edit', [AwardsController::class, 'update'])->name('awards.orders.update');
    Route::post('awards/derive_order/{order_id}', [AwardsController::class, 'deriveOrder'])->name('awards.deriveOrder');
    Route::post('awards/derivePAC/{order_id}', [AwardsController::class, 'derivePAC'])->name('awards.derivePAC');
    Route::post('awards/derive_comite/{order_id}', [AwardsController::class, 'deriveComite'])->name('awards.deriveComite');

    Route::post('awards/update_queries_deadline/{order_id}', [AwardsController::class, 'updateQueriesDeadline'])->name('awards.updateQueriesDeadline');
    // Route::post('awards/update_queries_deadline_adj/{order_id}', [AwardsController::class, 'updateQueriesDeadlineAdj'])->name('awards.updateQueriesDeadlineAdj');

    Route::get('excel/itemsAw', [AwardsController::class, 'ArchivoItemAw'])->name('excel.itemsAw');

    //ITEMS SE PASA A EXCEL EN AWARDS
    Route::get('items/export-excel/{order_id}', [ItemsAdjudicaController::class, 'exportExcel']);

    //ITEMS SE PASA A EXCEL EN CONTRACTS
    Route::get('items/export-excel3/{order_id}', [ItemsAdjudicaController::class, 'exportExcel3']);



    /********** MODULO CONTRACTS - CONTRATOS Y GARANTIAS **********/
    Route::get('contracts/getNotifications', [ContractsController::class, 'getNotifications'])->name('contracts.getNotifications');
    Route::resource('contracts', ContractsController::class);
    Route::resource('contracts.objections', ObjectionsController::class);
    Route::resource('contracts.objections_responses', ObjectionsResponsesController::class);
    Route::post('contracts/recibe_order/{order_id}', [ContractsController::class, 'recibeOrder'])->name('contracts.recibeOrder');
    Route::get('contracts/orders/{order_id}/edit', [ContractsController::class, 'edit'])->name('contracts.orders.edit');
    Route::put('contracts/orders/{order_id}/edit', [ContractsController::class, 'update'])->name('contracts.orders.update');
    Route::post('contracts/derive_order/{order_id}', [ContractsController::class, 'deriveOrder'])->name('contracts.deriveOrder');

    /********** MODULO UTA **********/
    Route::resource('utas', UtasController::class);
    // Route::resource('utas.queries', App\Http\Controllers\Uta\QueriesController::class);
    // Route::resource('utas.queries_responses', App\Http\Controllers\Uta\QueriesResponsesController::class);

    Route::resource('utas.objections', App\Http\Controllers\Uta\ObjectionsController::class);
    Route::resource('utas.objections_responses', App\Http\Controllers\Uta\ObjectionsResponsesController::class);

    Route::post('utas/recibe_order_uta/{order_id}', [UtasController::class, 'recibeOrderUta'])->name('utas.recibeOrderUta');
    Route::post('utas/recibe_info_uta/{order_id}', [UtasController::class, 'recibeInfoUta'])->name('utas.recibeInfoUta');

    Route::get('utas/orders/{order_id}/edit', [UtasController::class, 'edit'])->name('utas.orders.edit');
    Route::put('utas/orders/{order_id}/edit', [UtasController::class, 'update'])->name('utas.orders.update');

    Route::post('utas/verifica_pbc_sr/{order_id}', [UtasController::class, 'verificaPBC_Sr'])->name('utas.verificaPBC_Sr');
    Route::post('utas/verifica_pbc_cr/{order_id}', [UtasController::class, 'verificaPBC_Cr'])->name('utas.verificaPBC_Cr');
    Route::post('utas/verifica_pbc_cr_aj/{order_id}', [UtasController::class, 'verificaPBC_Cr_AJ'])->name('utas.verificaPBC_Cr_AJ');
    Route::post('utas/verifica_pbc_srCovid/{order_id}', [UtasController::class, 'verificaPBC_SrCovid'])->name('utas.verificaPBC_SrCovid');


    Route::post('utas/verifica_info_sr/{order_id}', [UtasController::class, 'verificaINFO_Sr'])->name('utas.verificaINFO_Sr');
    Route::post('utas/verifica_info_cr/{order_id}', [UtasController::class, 'verificaINFO_Cr'])->name('utas.verificaINFO_Cr');

    Route::post('utas/deriveCESC/{order_id}', [UtasController::class, 'deriveCESC'])->name('utas.deriveCESC');
    Route::post('utas/recibeCESC/{order_id}', [UtasController::class, 'recibeCESC'])->name('utas.recibeCESC');


    Route::post('utas/derive_comite/{order_id}', [UtasController::class, 'deriveComite'])->name('utas.deriveComite');

    // *************** REVISAR ESTE PROCESO EN COMPRAS MENORES *********
    Route::post('utas/deriveAJ_PCovid/{order_id}', [UtasController::class, 'deriveAJ_PCovid'])->name('utas.deriveAJ_PCovid');
    // *****************************************************************

    Route::post('utas/update_queries_deadline/{order_id}', [UtasController::class, 'updateQueriesDeadline'])->name('utas.updateQueriesDeadline');

    /********** MODULO ASESORIA JURÍDICA **********/
    Route::resource('legal_advices', LegalAdvicesController::class);

    // Route::resource('legal_advices.queries', App\Http\Controllers\LegalAdvice\QueriesController::class);
    // Route::resource('legal_advices.queries_responses', App\Http\Controllers\LegalAdvice\QueriesResponsesController::class);

    Route::resource('legal_advices.objections', App\Http\Controllers\LegalAdvice\ObjectionsController::class);
    Route::resource('legal_advices.objections_responses', App\Http\Controllers\LegalAdvice\ObjectionsResponsesController::class);

    Route::post('legal_advices/recibeOrder/{order_id}', [LegalAdvicesController::class, 'recibeOrder'])->name('legal_advices.recibeOrder');
    Route::post('legal_advices/deriveDictamen/{order_id}', [LegalAdvicesController::class, 'deriveDictamen'])->name('legal_advices.deriveDictamen');

    Route::post('legal_advices/recibeOrderEVAL/{order_id}', [LegalAdvicesController::class, 'recibeOrderEVAL'])->name('legal_advices.recibeOrderEVAL');
    Route::post('legal_advices/recibeOrderCVE/{order_id}', [LegalAdvicesController::class, 'recibeOrderCVE'])->name('legal_advices.recibeOrderCVE');

    Route::post('legal_advices/deriveDictamenEVAL/{order_id}', [LegalAdvicesController::class, 'deriveDictamenEVAL'])->name('legal_advices.deriveDictamenEVAL');
    Route::post('legal_advices/deriveDictamenCVE/{order_id}', [LegalAdvicesController::class, 'deriveDictamenCVE'])->name('legal_advices.deriveDictamenCVE');

    /********** MODULO COORDINACIÓN **********/
    Route::resource('coordinations', CoordinationsController::class);

    // Route::resource('coordinations.queries', App\Http\Controllers\Coordination\QueriesController::class);
    // Route::resource('coordinations.queries_responses', App\Http\Controllers\Coordination\QueriesResponsesController::class);

    Route::resource('coordinations.objections', App\Http\Controllers\Coordination\ObjectionsController::class);
    Route::resource('coordinations.objections_responses', App\Http\Controllers\Coordination\ObjectionsResponsesController::class);

    Route::post('coordinations/recibeOrder/{order_id}', [CoordinationsController::class, 'recibeOrder'])->name('coordinations.recibeOrder');
    Route::post('coordinations/deriveOrderPedido/{order_id}', [CoordinationsController::class, 'deriveOrderPedido'])->name('coordinations.deriveOrderPedido');
    Route::post('coordinations/deriveOrderPAC/{order_id}', [CoordinationsController::class, 'deriveOrderPAC'])->name('coordinations.deriveOrderPAC');
    Route::post('coordinations/deriveOrderPCYE/{order_id}', [CoordinationsController::class, 'deriveOrderPCYE'])->name('coordinations.deriveOrderPCYE');
    Route::post('coordinations/deriveOrder/{order_id}', [CoordinationsController::class, 'deriveOrder'])->name('coordinations.deriveOrder');
    Route::post('coordinations/deriveAdjudica/{order_id}', [CoordinationsController::class, 'deriveAdjudica'])->name('coordinations.deriveAdjudica');

    Route::post('coordinations/recibeOrderEVAL/{order_id}', [CoordinationsController::class, 'recibeOrderEVAL'])->name('coordinations.recibeOrderEVAL');
    Route::post('coordinations/recibeOrderCVE/{order_id}', [CoordinationsController::class, 'recibeOrderCVE'])->name('coordinations.recibeOrderCVE');

    Route::post('coordinations/deriveDictamen/{order_id}', [CoordinationsController::class, 'deriveDictamen'])->name('coordinations.deriveDictamen');
    Route::post('coordinations/deriveDictamenEVAL/{order_id}', [CoordinationsController::class, 'deriveDictamenEVAL'])->name('coordinations.deriveDictamenEVAL');
    Route::post('coordinations/deriveDictamenCVE/{order_id}', [CoordinationsController::class, 'deriveDictamenCVE'])->name('legal_advices.deriveDictamenCVE');

    /********** MODULO DE DGAF **********/
    Route::resource('dgafs', DgafsController::class);

    // Route::resource('dgafs.queries', App\Http\Controllers\Dgaf\QueriesController::class);
    // Route::resource('dgafs.queries_responses', App\Http\Controllers\Dgaf\QueriesResponsesController::class);

    Route::resource('dgafs.objections', App\Http\Controllers\Dgaf\ObjectionsController::class);
    Route::resource('dgafs.objections_responses', App\Http\Controllers\Dgaf\ObjectionsResponsesController::class);

    Route::post('dgafs/recibeOrder/{order_id}', [DgafsController::class, 'recibeOrder'])->name('dgafs.recibeOrder');
    Route::post('dgafs/recibeOrderDGAF/{order_id}', [DgafsController::class, 'recibeOrderDGAF'])->name('dgafs.recibeOrderDGAF');
    Route::post('dgafs/deriveOrderDGAF/{order_id}', [DgafsController::class, 'deriveOrderDGAF'])->name('dgafs.deriveOrderDGAF');

    Route::post('dgafs/deriveDictamen/{order_id}', [DgafsController::class, 'deriveDictamen'])->name('dgafs.deriveDictamen');

    Route::post('dgafs/recibeOrderEVAL/{order_id}', [DgafsController::class, 'recibeOrderEVAL'])->name('dgafs.recibeOrderEVAL');
    Route::post('dgafs/recibeOrderCVE/{order_id}', [DgafsController::class, 'recibeOrderCVE'])->name('dgafs.recibeOrderCVE');

    Route::post('dgafs/deriveDictamenEVAL/{order_id}', [DgafsController::class, 'deriveDictamenEVAL'])->name('dgafs.deriveDictamenEVAL');
    Route::post('dgafs/deriveDictamenCVE/{order_id}', [DgafsController::class, 'deriveDictamenCVE'])->name('dgafs.deriveDictamenCVE');

    /********** MODULO COMITE **********/
    Route::resource('comites', ComitesController::class);

    // Route::resource('comites.queries', App\Http\Controllers\Comite\QueriesController::class);
    // Route::resource('comites.queries_responses', App\Http\Controllers\Comite\QueriesResponsesController::class);

    // Route::resource('comites.objections', App\Http\Controllers\Comite\ObjectionsController::class);
    // Route::resource('comites.objections_responses', App\Http\Controllers\Comite\ObjectionsResponsesController::class);

    Route::post('comites/recibeOrder/{order_id}', [ComitesController::class, 'recibeOrder'])->name('comites.recibeOrder');
    Route::post('comites/recibeOrderReparo/{order_id}', [ComitesController::class, 'recibeOrderReparo'])->name('comites.recibeOrderReparo');
    Route::post('comites/recibeOrderAdjudica/{order_id}', [ComitesController::class, 'recibeOrderAdjudica'])->name('comites.recibeOrderAdjudica');

    Route::post('comites/deriveInforme/{order_id}', [ComitesController::class, 'deriveInforme'])->name('comites.deriveInforme');
    Route::post('comites/deriveInformeAdjudica/{order_id}', [ComitesController::class, 'deriveInformeAdjudica'])->name('comites.deriveInformeAdjudica');


    /********** MODULO UNIDAD DE GESTIÓN DOCUMENTAL **********/
    Route::resource('documentals', DocumentalsController::class);

    // Route::resource('documentals.queries', App\Http\Controllers\Documental\QueriesController::class);
    // Route::resource('documentals.queries_responses', App\Http\Controllers\Documental\QueriesResponsesController::class);

    // Route::resource('documentals.objections', App\Http\Controllers\Documental\ObjectionsController::class);
    // Route::resource('documentals.objections_responses', App\Http\Controllers\Documental\ObjectionsResponsesController::class);

    Route::post('documentals/recibeOrder/{order_id}', [DocumentalsController::class, 'recibeOrder'])->name('documentals.recibeOrder');
    Route::post('documentals/recibeOrderReparo/{order_id}', [DocumentalsController::class, 'recibeOrderReparo'])->name('documentals.recibeOrderReparo');
    Route::post('documentals/deriveInforme/{order_id}', [DocumentalsController::class, 'deriveInforme'])->name('documentals.deriveInforme');

    Route::get('contracts/orders/{order_id}/edit', [ContractsController::class, 'edit'])->name('contracts.orders.edit');

    // Route::get('/compilarReporte', function () {
    //     $input = base_path() .
    //     '/vendor/geekcom/phpjasper/examples/hello_world.jrxml';

    //     $jasper = new PHPJasper;
    //     $jasper->compile($input)->execute();

    //     return response()->json([
    //         'status' => 'ok',
    //         'msj' => '¡Reporte compilado!'
    //     ]);
    // });
});
