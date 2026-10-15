<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route($this->destination($request->user()));
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route($this->destination($request->user()));
        }

        $credentials = $request->validate([
            'usuario' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([...$credentials, 'estado' => 1])) {
            throw ValidationException::withMessages([
                'usuario' => 'El usuario o la contraseña son incorrectos, o la cuenta está desactivada.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route($this->destination($request->user()));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function destination(Usuario $usuario): string
    {
        return match ($usuario->rol) {
            Usuario::ROL_ESTUDIANTE => 'panel.estudiante',
            Usuario::ROL_DOCENTE => 'panel.docente',
            Usuario::ROL_ADMINISTRADOR => 'panel.administrador',
            default => abort(403),
        };
    }
}
