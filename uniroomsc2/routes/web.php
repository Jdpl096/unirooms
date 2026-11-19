<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddroomC;
use Illuminate\Support\Facades\Auth;

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/bienvenida', function () {
    return view('bienvenida');
})->name('bienvenida');

Route::get('/inicio', function () {
    return view('inicio');
})->name('inicio');

Route::get('/addroom', function () {
    $usuario = Auth::user();

    if (!$usuario || $usuario->rol->nombre !== 'propietario') {
        //abort(403, 'No tienes permisos para acceder a esta página');
        return redirect()->route('inicio');
    }

    return view('addroom', compact('usuario'));
})->name('addroom');


Route::get('/buscarroom', function () {
    $usuario = Auth::user();

    // Si no es estudiante, redirige a /inicio
    if (!$usuario || $usuario->rol->nombre !== 'estudiante') {
        return redirect()->route('inicio');
    }

    return view('buscarroom');
})->name('buscarroom');


Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::post('/rooms', [AddroomC::class, 'store'])->name('addroomC');

