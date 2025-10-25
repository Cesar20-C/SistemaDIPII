<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UsuarioController,
    ProveedorController,
    CertificadoController,
    EtiquetaController,
    IngresoController,
    DashboardDipiiController
};

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardDipiiController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard/data', [DashboardDipiiController::class, 'data'])
        ->name('dashboard.data');
    // Tus rutas existentes
    Route::resource('usuarios', UsuarioController::class)->except(['show']);

    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor'])
        ->except(['show']);

    Route::resource('ingresos', IngresoController::class)->except(['show']);

    Route::resource('certificados', CertificadoController::class)->except(['show']);
    Route::get('certificados/{certificado}/descargar', [CertificadoController::class, 'descargar'])
        ->name('certificados.descargar');

    Route::resource('etiquetas', EtiquetaController::class)
        ->only(['index','create','store','destroy'])
        ->parameters(['etiquetas' => 'lote']);
    Route::get('etiquetas/{lote}/descargar', [EtiquetaController::class, 'descargar'])
        ->name('etiquetas.descargar');
});

require __DIR__.'/auth.php';
require __DIR__.'/profile.php';
