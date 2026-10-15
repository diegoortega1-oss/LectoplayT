<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentActivityController;
use App\Http\Controllers\StudentDashboardController;
use App\Models\Usuario;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1')->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/estudiante', StudentDashboardController::class)
        ->middleware('role:'.Usuario::ROL_ESTUDIANTE)->name('panel.estudiante');
    Route::middleware('role:'.Usuario::ROL_ESTUDIANTE)->group(function () {
        Route::get('/estudiante/actividades/historial', [StudentActivityController::class, 'history'])->name('student.activities.history');
        Route::get('/estudiante/actividades/{actividad}', [StudentActivityController::class, 'show'])->name('student.activities.show');
        Route::post('/estudiante/actividades/{actividad}/intentos', [StudentActivityController::class, 'start'])->name('student.activities.start');
        Route::get('/estudiante/intentos/{intento}', [StudentActivityController::class, 'edit'])->name('student.attempts.edit');
        Route::post('/estudiante/intentos/{intento}/respuestas', [StudentActivityController::class, 'save'])->name('student.attempts.save');
        Route::post('/estudiante/intentos/{intento}/entregar', [StudentActivityController::class, 'finish'])->name('student.attempts.finish');
        Route::get('/estudiante/intentos/{intento}/resultado', [StudentActivityController::class, 'result'])->name('student.attempts.result');
    });
    Route::view('/docente', 'auth.panel', ['titulo' => 'Docente'])
        ->middleware('role:'.Usuario::ROL_DOCENTE)->name('panel.docente');
    Route::view('/administrador', 'auth.panel', ['titulo' => 'Administrador'])
        ->middleware('role:'.Usuario::ROL_ADMINISTRADOR)->name('panel.administrador');
});
