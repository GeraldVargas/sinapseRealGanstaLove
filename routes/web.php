<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CanjeController;
use App\Http\Controllers\Admin\TriggerMonitorController;
use App\Http\Controllers\RankingController;

// ==================== RUTAS PÚBLICAS ====================
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== RUTAS DEBUG/TEMPORALES ====================
Route::get('/debug-session', function () {
    echo "<h1>Session Data:</h1>";
    echo "<pre>";
    print_r(session()->all());
    echo "</pre>";
});

// ==================== RUTAS ESTUDIANTE ====================
Route::prefix('estudiante')->group(function () {
    // Dashboard y navegación principal
    Route::get('/dashboard', [EstudianteController::class, 'dashboard'])->name('estudiante.dashboard');
    
    // Explorar cursos e inscripción
    Route::get('/explorar-cursos', [EstudianteController::class, 'explorarCursos'])->name('estudiante.explorar_cursos');
    Route::post('/inscribirse/{idCurso}', [EstudianteController::class, 'inscribirseCurso'])->name('estudiante.inscribirse');
    Route::get('/curso/{idCurso}', [EstudianteController::class, 'verCurso'])->name('estudiante.curso.ver');
    
    // Gestión de progreso en cursos
    Route::post('/curso/{idCurso}/completar-tema/{idTema}', [EstudianteController::class, 'completarTema'])->name('estudiante.curso.completar-tema');
    Route::post('/curso/{idCurso}/comenzar-modulo/{idModulo}', [EstudianteController::class, 'comenzarModulo'])->name('estudiante.curso.comenzar-modulo');
    
    // ✅ SISTEMA DE EVALUACIONES - COMPLETAMENTE CORREGIDO
    Route::get('/evaluacion/{idEvaluacion}', [EstudianteController::class, 'verEvaluacion'])->name('estudiante.evaluacion.ver');
    Route::get('/evaluacion/{idEvaluacion}/comenzar', [EstudianteController::class, 'verEvaluacion'])->name('estudiante.evaluacion.comenzar');
    Route::post('/evaluacion/{idEvaluacion}/procesar', [EstudianteController::class, 'procesarEvaluacion'])->name('estudiante.evaluacion.procesar');
    Route::post('/evaluacion/{idEvaluacion}/enviar', [EstudianteController::class, 'procesarEvaluacion'])->name('estudiante.evaluacion.enviar');
    Route::get('/evaluacion/{idEvaluacion}/resultado', [EstudianteController::class, 'verResultadoEvaluacion'])->name('estudiante.evaluacion.resultado');
    
    // Sistema de entregas
    Route::get('/entrega/{idEvaluacion}', [EstudianteController::class, 'mostrarEntrega'])->name('estudiante.entrega.mostrar');
    Route::post('/entrega/{idEvaluacion}/enviar', [EstudianteController::class, 'procesarEntrega'])->name('estudiante.entrega.enviar');
    
    // Sistema de puntos y triggers
    Route::get('/puntos-actuales', [EstudianteController::class, 'obtenerPuntosActuales'])->name('estudiante.puntos_actuales');
    Route::get('/verificar-triggers', [EstudianteController::class, 'verificarTriggers'])->name('estudiante.verificar_triggers');
    
    // Testing de triggers (opcional)
    Route::get('/probar-triggers', [EstudianteController::class, 'probarTriggers'])->name('estudiante.probar-triggers');
    Route::post('/simular-aprobacion', [EstudianteController::class, 'simularAprobacion']);
    Route::post('/simular-completar-curso', [EstudianteController::class, 'simularCompletarCurso']);
});

// ==================== RUTAS DOCENTE ====================
Route::prefix('docente')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DocenteController::class, 'dashboard'])->name('docente.dashboard');
    
    // Gestión de cursos
    Route::get('/curso/{idCurso}', [DocenteController::class, 'verCurso'])->name('docente.curso.detalle');
    Route::post('/evaluacion/crear/{idCurso}', [DocenteController::class, 'crearEvaluacion'])->name('docente.evaluacion.crear');
    
    // Gestión de módulos y temas
    Route::post('/curso/{idCurso}/modulo/crear', [DocenteController::class, 'crearModulo'])->name('docente.modulo.crear');
    Route::delete('/modulo/{idModulo}/eliminar', [DocenteController::class, 'eliminarModulo'])->name('docente.modulo.eliminar');
    Route::post('/modulo/{idModulo}/tema/crear', [DocenteController::class, 'crearTema'])->name('docente.tema.crear');
    Route::delete('/tema/{idTema}/eliminar', [DocenteController::class, 'eliminarTema'])->name('docente.tema.eliminar');
    
    // Gestión de estudiantes
    Route::post('/curso/{idCurso}/estudiante/agregar', [DocenteController::class, 'agregarEstudiante'])->name('docente.estudiante.agregar');
    Route::delete('/curso/{idCurso}/estudiante/{idEstudiante}', [DocenteController::class, 'eliminarEstudiante'])->name('docente.estudiante.eliminar');
    Route::get('/curso/{idCurso}/estudiante/{idEstudiante}', [DocenteController::class, 'verDetalleEstudiante'])->name('docente.estudiante.detalle');
    
    // Gestión de entregas
    Route::get('/entregas-pendientes', [DocenteController::class, 'entregasPendientes'])->name('docente.entregas.pendientes');
    Route::post('/entrega/{idEntrega}/calificar', [DocenteController::class, 'calificarEntrega'])->name('docente.entrega.calificar');
    
    // APIs para datos
    Route::get('/temas-por-curso/{cursoId}', [DocenteController::class, 'obtenerTemasPorCurso']);
    Route::get('/modulos-por-curso/{cursoId}', [DocenteController::class, 'obtenerModulosPorCurso']);
    
    // Ranking docente
    Route::get('/ranking', [DocenteController::class, 'rankingDocente'])->name('docente.ranking');
});

// ==================== RUTAS ADMINISTRADOR ====================
Route::prefix('admin')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Gestión de cursos
    Route::get('/cursos', [AdminController::class, 'gestionCursos'])->name('admin.cursos');
    Route::post('/curso/crear', [AdminController::class, 'crearCurso'])->name('admin.curso.crear');
    Route::post('/curso/asignar-docente', [AdminController::class, 'asignarDocenteCurso'])->name('admin.asignar.docente');
    Route::get('/curso/{Id_curso}/estudiantes', [AdminController::class, 'estudiantesPorCurso'])->name('admin.estudiantes.curso');
    Route::get('/curso/{Id_curso}/docentes', [AdminController::class, 'docentesPorCurso'])->name('admin.docentes.curso');
    
    // Gestión de usuarios
    Route::get('/usuarios', [AdminController::class, 'gestionUsuarios'])->name('admin.usuarios');
    Route::post('/usuario/asignar-docente', [AdminController::class, 'asignarRolDocente'])->name('admin.asignar.rol.docente');
    
    // Monitoreo de triggers
    Route::get('/triggers', [TriggerMonitorController::class, 'index'])->name('admin.triggers');
    Route::post('/triggers/probar-cupos', [TriggerMonitorController::class, 'probarTriggerCupos'])->name('admin.triggers.probar_cupos');
    Route::post('/triggers/probar-puntos', [TriggerMonitorController::class, 'probarTriggerPuntos'])->name('admin.triggers.probar_puntos');
    Route::post('/triggers/probar-canje', [TriggerMonitorController::class, 'probarTriggerCanje'])->name('admin.triggers.probar_canje');
});

// ==================== RUTAS SISTEMA DE RECOMPENSAS ====================
Route::prefix('canjes')->group(function () {
    Route::get('/', [CanjeController::class, 'index'])->name('canjes.index');
    Route::get('/mis-canjes', [CanjeController::class, 'misCanjes'])->name('canjes.mis_canjes');
    Route::post('/canjear', [CanjeController::class, 'canjearRecompensa'])->name('canjes.canjear');
    Route::get('/verificar-puntos', [CanjeController::class, 'verificarPuntos'])->name('canjes.verificar_puntos');
});

// ==================== RUTAS RANKING ====================
Route::prefix('ranking')->group(function () {
    Route::get('/', [RankingController::class, 'index'])->name('ranking.index');
    Route::get('/docente', [RankingController::class, 'rankingDocente'])->name('ranking.docente');
    Route::get('/admin', [RankingController::class, 'rankingAdmin'])->name('ranking.admin');
    Route::get('/json/{periodo?}', [RankingController::class, 'obtenerRankingJson'])->name('ranking.json');
});

// ==================== RUTAS API/DATOS ====================
Route::get('/estudiante/puntos-actuales', [EstudianteController::class, 'obtenerPuntosActuales'])->name('estudiante.puntos.actuales');

// ==================== RUTAS DEBUG/TESTING ====================
Route::get('/debug-puntos/{idUsuario?}', [EstudianteController::class, 'debugPuntos']);
Route::post('/estudiante/inicializar-puntos', [EstudianteController::class, 'inicializarPuntos']);
Route::get('/inicializar-ranking', [EstudianteController::class, 'inicializarRankingCompleto']);
Route::get('/ver-ranking', [EstudianteController::class, 'verRanking']);
Route::get('/actualizar-puntos-global', [EstudianteController::class, 'actualizarPuntosGlobal']);
Route::get('/fix-todo', [EstudianteController::class, 'fixTodo']);
Route::get('/actualizar-puntos-auto/{idUsuario?}', [EstudianteController::class, 'actualizarPuntosAutomatico']);
Route::get('/actualizar-puntos-todos', [EstudianteController::class, 'actualizarPuntosTodos']);

// ==================== RUTAS ALIAS (PARA COMPATIBILIDAD) ====================
Route::get('/ranking', [EstudianteController::class, 'mostrarRanking'])->name('ranking');