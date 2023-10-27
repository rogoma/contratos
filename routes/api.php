<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('catalogs', function () {   
    // Route::get('users', function () {   
        // return App\Models\User::all();
    
        return datatables()                
            //DESDE EL MODELO
            // ->eloquent(App\Models\Level5CatalogCode::query())
            
            //DESDE LA BD O VISTA
            // ->query(DB::table('level5_catalog_codes'))
            ->query(DB::table('vista_catalogs'))
            ->addColumn('btn', 'actions')
            ->rawColumns(['btn'])
            ->toJson();       
});

