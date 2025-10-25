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
| incluyendo los módulos administrativos, dashboard y las rutas especiales
| para servir archivos PDF en Railway sin usar storage:link.
|--------------------------------------------------------------------------
*/

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

// ===================== RUTAS PARA ARCHIVOS STORAGE =====================
// Estas rutas reemplazan la necesidad de ejecutar "php artisan storage:link"
// Funcionan tanto en local como en Railway y permiten servir PDFs e imágenes
// directamente desde storage/app/public/... sin enlaces simbólicos.

Route::get('/storage/certificados/{filename}', function ($filename) {
    $path = storage_path('app/public/certificados/' . $filename);
    abort_unless(file_exists($path), 404, 'Archivo no encontrado');
    return response()->file($path);
})->where('filename', '.*');

Route::get('/storage/etiquetas/{filename}', function ($filename) {
    $path = storage_path('app/public/etiquetas/' . $filename);
    abort_unless(file_exists($path), 404, 'Archivo no encontrado');
    return response()->file($path);
})->where('filename', '.*');

// Si en el futuro guardas PDFs de ingresos o proveedores, puedes copiar este patrón:
// Route::get('/storage/ingresos/{filename}', fn($filename) => response()->file(storage_path('app/public/ingresos/'.$filename)))->where('filename','.*');

// ===================== AUTENTICACIÓN Y PERFIL =====================
require __DIR__ . '/auth.php';
require __DIR__ . '/profile.php';
