<?php

use App\Http\Controllers\Auth\LoginController;
use App\Models\Usuario;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1')->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::view('/estudiante', 'auth.panel', ['titulo' => 'Estudiante'])
        ->middleware('role:'.Usuario::ROL_ESTUDIANTE)->name('panel.estudiante');
    Route::view('/docente', 'auth.panel', ['titulo' => 'Docente'])
        ->middleware('role:'.Usuario::ROL_DOCENTE)->name('panel.docente');
    Route::view('/administrador', 'auth.panel', ['titulo' => 'Administrador'])
        ->middleware('role:'.Usuario::ROL_ADMINISTRADOR)->name('panel.administrador');
});
