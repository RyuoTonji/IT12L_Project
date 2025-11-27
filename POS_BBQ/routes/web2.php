<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    // For demo purposes, just redirect to dashboard
    return redirect()->route('dashboard');
})->name('login.post');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/login-by-number', function () {
    return view('auth.login'); // You can create a separate view later
})->name('login.phone');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');
