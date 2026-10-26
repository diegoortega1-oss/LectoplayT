<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentActivityController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentDiagnosticController;
use App\Http\Middleware\EnsureStudentDiagnosticAccess;
use App\Models\Usuario;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1')->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::middleware('role:'.Usuario::ROL_ESTUDIANTE)->group(function () {
        Route::get('/estudiante/diagnostico', [StudentDiagnosticController::class, 'index'])->name('diagnostico.index');
        Route::post('/estudiante/diagnostico/iniciar', [StudentDiagnosticController::class, 'iniciar'])->name('diagnostico.iniciar');
        Route::get('/estudiante/diagnostico/en-proceso', [StudentDiagnosticController::class, 'enProceso'])->name('diagnostico.en-proceso');
        Route::get('/estudiante/diagnostico/resumen', [StudentDiagnosticController::class, 'resumen'])->name('diagnostico.resumen');
        Route::post('/estudiante/diagnostico/finalizar', [StudentDiagnosticController::class, 'finalizar'])->name('diagnostico.finalizar');
        Route::get('/estudiante/diagnostico/resultado', [StudentDiagnosticController::class, 'resultado'])->name('diagnostico.resultado');
        Route::get('/estudiante/diagnostico/lectura/{orden}', [StudentDiagnosticController::class, 'lectura'])->whereNumber('orden')->name('diagnostico.lectura');
        Route::post('/estudiante/diagnostico/lectura/{orden}/guardar', [StudentDiagnosticController::class, 'guardar'])->whereNumber('orden')->name('diagnostico.guardar');
        Route::middleware(EnsureStudentDiagnosticAccess::class)->group(function () {
            Route::get('/estudiante', StudentDashboardController::class)->name('panel.estudiante');
            Route::get('/estudiante/actividades/historial', [StudentActivityController::class, 'history'])->name('student.activities.history');
            Route::get('/estudiante/actividades/{actividad}', [StudentActivityController::class, 'show'])->name('student.activities.show');
            Route::post('/estudiante/actividades/{actividad}/intentos', [StudentActivityController::class, 'start'])->name('student.activities.start');
            Route::get('/estudiante/intentos/{intento}', [StudentActivityController::class, 'edit'])->name('student.attempts.edit');
            Route::post('/estudiante/intentos/{intento}/respuestas', [StudentActivityController::class, 'save'])->name('student.attempts.save');
            Route::post('/estudiante/intentos/{intento}/entregar', [StudentActivityController::class, 'finish'])->name('student.attempts.finish');
            Route::get('/estudiante/intentos/{intento}/resultado', [StudentActivityController::class, 'result'])->name('student.attempts.result');
        });
    });
    Route::view('/docente', 'auth.panel', ['titulo' => 'Docente'])
        ->middleware('role:'.Usuario::ROL_DOCENTE)->name('panel.docente');
    Route::view('/administrador', 'auth.panel', ['titulo' => 'Administrador'])
        ->middleware('role:'.Usuario::ROL_ADMINISTRADOR)->name('panel.administrador');
});
