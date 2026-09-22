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
Route::get('consolidados/{consolidado}/importar', 'ConsolidadoController@importForm')->name('consolidados.importar')->middleware('not-client');
Route::post('consolidados/{consolidado}/importar', 'ConsolidadoController@importCsv')->name('consolidados.importar.store')->middleware('not-client');
Route::post('consolidados/{consolidado}/entradas', 'ConsolidadoController@addEntrada')->name('consolidados.entradas.store')->middleware('not-client');
Route::get('consolidados/{consolidado}/entradas/crear', 'ConsolidadoController@entradaForm')->name('consolidados.entradas.create')->middleware('not-client');
Route::resource('consolidados', 'ConsolidadoController')->except(['index', 'show'])->middleware('not-client');
Route::resource('consolidados', 'ConsolidadoController')->only(['index', 'show']);
Route::resource('entradas', 'EntradaController')->only(['index', 'show']);
Route::get('entradas/{entrada}/salida/editar', 'EntradaController@editSalida')->name('entradas.salida.edit')->middleware('not-client');
Route::put('entradas/{entrada}/salida', 'EntradaController@updateSalida')->name('entradas.salida.update')->middleware('not-client');
Route::resource('entradas', 'EntradaController')->except(['index', 'show'])->middleware('not-client');
Route::post('entradas/control-usa', 'EntradaController@controlUsa')
    ->name('entradas.control-usa')
    ->middleware('role:bodega_usa,supervisor,administrador,superadministrador');
Route::post('entradas/control-usa/completar', 'EntradaController@completeControlUsa')
    ->name('entradas.control-usa.complete')
    ->middleware('role:bodega_usa,supervisor,administrador,superadministrador');
Route::get('bodega-usa', 'BodegaController@usa')
    ->name('bodega-usa')
    ->middleware('role:bodega_usa,supervisor,administrador,superadministrador');
Route::get('bodega-usa/cambiar-modo', 'BodegaController@cambiarModo')
    ->name('bodega-usa.cambiar-modo')
    ->middleware('role:bodega_usa,supervisor,administrador,superadministrador');

Route::middleware('not-client')->group(function () {
    Route::resource('clientes', 'ClienteController');
    Route::resource('conductores', 'ConductorController')
        ->parameters(['conductores' => 'conductor']);
    Route::resource('vehiculos', 'VehiculoController');
    Route::resource('transportadoras', 'TransportadoraController');
    Route::resource('bodegas', 'BodegaController');
    Route::resource('reempacadores', 'ReempacadorController');
    Route::resource('mediciones', 'MedicionController')
        ->parameters(['mediciones' => 'medicion']);
    Route::resource('observaciones', 'ObservacionController');
    Route::resource('codigosr', 'CodigorController');
    Route::resource('remitentes', 'RemitenteController');
    Route::resource('destinatarios', 'DestinatarioController');
    Route::resource('oficinas', 'OficinaController');
});
Route::resource('coberturas', 'CoberturaController')->except(['show']);
Route::resource('usuarios', 'UserController')->middleware('role:superadministrador');
