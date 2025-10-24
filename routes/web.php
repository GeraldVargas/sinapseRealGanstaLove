<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\InsigniaController;

// Ruta principal - Dashboard general
Route::get('/', [DashboardController::class, 'index'])->name('home');

// Áreas por rol
Route::get('/estudiante', [EstudianteController::class, 'index'])->name('estudiante.dashboard');
Route::get('/docente', [DocenteController::class, 'index'])->name('docente.dashboard');
Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

// Listados
Route::get('/estudiantes', [UsuarioController::class, 'estudiantes'])->name('estudiantes.list');
Route::get('/docentes', [UsuarioController::class, 'docentes'])->name('docentes.list');
Route::get('/cursos', [CursoController::class, 'index'])->name('cursos.list');
Route::get('/insignias', [InsigniaController::class, 'index'])->name('insignias.list');

// Páginas comunes
Route::get('/perfil', [DashboardController::class, 'perfil'])->name('perfil');