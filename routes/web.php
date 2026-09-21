<?php

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

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('clientes', 'ClienteController');
Route::resource('consolidados', 'ConsolidadoController');
Route::resource('entradas', 'EntradaController');
Route::resource('conductores', 'ConductorController')
    ->parameters(['conductores' => 'conductor']);
Route::resource('vehiculos', 'VehiculoController');
Route::resource('transportadoras', 'TransportadoraController');
Route::resource('bodegas', 'BodegaController');
Route::resource('reempacadores', 'ReempacadorController');
Route::resource('mediciones', 'MedicionController');
Route::resource('observaciones', 'ObservacionController');
Route::resource('codigosr', 'CodigorController');
