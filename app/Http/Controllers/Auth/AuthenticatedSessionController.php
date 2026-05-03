<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();


        /* * Cambiado la redireccion cuando loguee vaya a el dashboard de docente, no del index
        ! habria que comprobar si es necesario ver el usuario para que la ruta hacia el dashboard
        ! sea dinamica en fucnion del usuario logueda */

 /*        $usuarioLogueado = $request->user()->name;
        return redirect()->intended(route('$usuarioLogueado.dashboard', absolute: false)); */
        return redirect()->intended(route(/* $request->user()->rol(). */'dashboard', absolute: false)); 
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
