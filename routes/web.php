<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\AdminController;

// Rutas Públicas
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta temporal para debug
Route::get('/debug-session', function () {
    echo "<h1>Session Data:</h1>";
    echo "<pre>";
    print_r(session()->all());
    echo "</pre>";
});
// En routes/web.php - Agregar estas rutas para docente
Route::prefix('docente')->group(function () {
    Route::get('/dashboard', [DocenteController::class, 'dashboard'])->name('docente.dashboard');
    Route::get('/curso/{id}', [DocenteController::class, 'verCurso'])->name('docente.curso.detalle');
    Route::post('/evaluacion/crear', [DocenteController::class, 'crearEvaluacion'])->name('docente.evaluacion.crear');
});
// RUTAS PROTEGIDAS - SIN MIDDLEWARE POR AHORA
Route::get('/estudiante/dashboard', [EstudianteController::class, 'dashboard'])->name('estudiante.dashboard');
Route::get('/docente/dashboard', [DocenteController::class, 'dashboard'])->name('docente.dashboard');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

Route::prefix('docente')->group(function () {
    Route::get('/dashboard', [DocenteController::class, 'dashboard'])->name('docente.dashboard');
    Route::get('/curso/{id}', [DocenteController::class, 'verCurso'])->name('docente.curso.detalle');
    Route::post('/curso/{id}/estudiante/agregar', [DocenteController::class, 'agregarEstudiante'])->name('docente.estudiante.agregar');
    Route::delete('/curso/{idCurso}/estudiante/{idEstudiante}', [DocenteController::class, 'eliminarEstudiante'])->name('docente.estudiante.eliminar');
    Route::get('/curso/{idCurso}/estudiante/{idEstudiante}', [DocenteController::class, 'verDetalleEstudiante'])->name('docente.estudiante.detalle');
    Route::post('/evaluacion/crear/{idCurso}', [DocenteController::class, 'crearEvaluacion'])->name('docente.evaluacion.crear');
});