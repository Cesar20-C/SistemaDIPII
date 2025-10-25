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
    // Dashboard principal
    Route::get('/dashboard', [DashboardDipiiController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard/data', [DashboardDipiiController::class, 'data'])
        ->name('dashboard.data');

    // Módulo de usuarios
    Route::resource('usuarios', UsuarioController::class)->except(['show']);

    // Módulo de proveedores
    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor'])
        ->except(['show']);

    // Módulo de ingresos
    Route::resource('ingresos', IngresoController::class)->except(['show']);

    // Módulo de certificados
    Route::resource('certificados', CertificadoController::class)->except(['show']);
    Route::get('certificados/{certificado}/descargar', [CertificadoController::class, 'descargar'])
        ->name('certificados.descargar');

    // Módulo de etiquetas
    Route::resource('etiquetas', EtiquetaController::class)
        ->only(['index', 'create', 'store', 'destroy'])
        ->parameters(['etiquetas' => 'lote']);
    Route::get('etiquetas/{lote}/descargar', [EtiquetaController::class, 'descargar'])
        ->name('etiquetas.descargar');
});


// 🔹 Servir archivos desde storage (para Railway o producción sin storage:link)
Route::get('/storage/certificados/{filename}', function ($filename) {
    $path = storage_path('app/public/certificados/' . $filename);

    if (!file_exists($path)) {
        abort(404, 'Archivo no encontrado');
    }

    return response()->file($path);
})->where('filename', '.*');

Route::get('/storage/etiquetas/{filename}', function ($filename) {
    $path = storage_path('app/public/etiquetas/' . $filename);

    if (!file_exists($path)) {
        abort(404, 'Archivo no encontrado');
    }

    return response()->file($path);
})->where('filename', '.*');


require __DIR__ . '/auth.php';
require __DIR__ . '/profile.php';
