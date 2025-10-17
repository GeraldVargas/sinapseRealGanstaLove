<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActividadComplementariaController;
use App\Http\Controllers\CanjeController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\GestionPuntoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\InsigniaController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ProgresoCursoController;
use App\Http\Controllers\ProgresoEvaluacionController;
use App\Http\Controllers\ProgresoTemaController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\RecompensaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\RolPermisoController;
use App\Http\Controllers\RolUsuarioController;
use App\Http\Controllers\TemaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UsuarioInsigniaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Generación automática de rutas para cada recurso de la aplicación
Route::resource('actividades-complementarias', ActividadComplementariaController::class);
Route::resource('canjes', CanjeController::class);
Route::resource('cursos', CursoController::class);
Route::resource('evaluaciones', EvaluacionController::class);
Route::resource('gestion-puntos', GestionPuntoController::class);
Route::resource('inscripciones', InscripcionController::class);
Route::resource('insignias', InsigniaController::class);
Route::resource('modulos', ModuloController::class);
Route::resource('pagos', PagoController::class);
Route::resource('permisos', PermisoController::class);
Route::resource('progresos-cursos', ProgresoCursoController::class);
Route::resource('progresos-evaluaciones', ProgresoEvaluacionController::class);
Route::resource('progresos-temas', ProgresoTemaController::class);
Route::resource('rankings', RankingController::class);
Route::resource('recompensas', RecompensaController::class);
Route::resource('roles', RolController::class);
Route::resource('roles-permisos', RolPermisoController::class);
Route::resource('roles-usuarios', RolUsuarioController::class);
Route::resource('temas', TemaController::class);
Route::resource('usuarios', UsuarioController::class);
Route::resource('usuarios-insignias', UsuarioInsigniaController::class);