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

/*
|--------------------------------------------------------------------------
| RUTAS WEB DEL SISTEMA DIPII
|--------------------------------------------------------------------------
| Este archivo contiene todas las rutas principales del sistema DIPII,
| incluyendo los módulos administrativos y dashboard.
| En esta versión ya no se usan rutas /storage/ ni storage:link,
| porque los PDFs se guardan directamente en /public.
|--------------------------------------------------------------------------
*/

// Redirección inicial
Route::get('/', fn () => redirect()->route('dashboard'));

// ===================== RUTAS PROTEGIDAS =====================
Route::middleware(['auth'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardDipiiController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard/data', [DashboardDipiiController::class, 'data'])
        ->name('dashboard.data');

    // USUARIOS
    Route::resource('usuarios', UsuarioController::class)->except(['show']);

    // PROVEEDORES
    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor'])
        ->except(['show']);

    // INGRESOS
    Route::resource('ingresos', IngresoController::class)->except(['show']);

    // CERTIFICADOS
    Route::resource('certificados', CertificadoController::class)->except(['show']);
    Route::get('certificados/{certificado}/descargar', [CertificadoController::class, 'descargar'])
        ->name('certificados.descargar');

    // ETIQUETAS
    Route::resource('etiquetas', EtiquetaController::class)
        ->only(['index', 'create', 'store', 'destroy'])
        ->parameters(['etiquetas' => 'lote']);

    Route::get('etiquetas/{lote}/descargar', [EtiquetaController::class, 'descargar'])
        ->name('etiquetas.descargar');
});

// ===================== AUTENTICACIÓN Y PERFIL =====================
require __DIR__ . '/auth.php';
require __DIR__ . '/profile.php';
