<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/dashboard', [ContactController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/debug-views', function () {
    return response(file_get_contents(resource_path('views/dashboard.blade.php')))->header('Content-Type', 'text/plain');
});

Route::middleware('auth')->group(function () {
    Route::get('/lead/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::post('/lead/manual', [ContactController::class, 'storeManual'])->name('contacts.storeManual');
    Route::patch('/lead/{contact}/details', [ContactController::class, 'updateDetails'])->name('contacts.updateDetails');
    Route::patch('/lead/{contact}/status', [ContactController::class, 'updateStatus'])->name('contacts.updateStatus');
    Route::post('/lead/{contact}/notes', [ContactController::class, 'storeNote'])->name('contacts.storeNote');
    Route::patch('/lead/{contact}/notes/{note}', [ContactController::class, 'updateNote'])->name('contacts.updateNote');
    Route::patch('/lead/{contact}/notes/{note}/pin', [ContactController::class, 'togglePinNote'])->name('contacts.togglePinNote');
    Route::delete('/lead/{contact}/notes/{note}', [ContactController::class, 'destroyNote'])->name('contacts.destroyNote');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin-only: User management
    Route::middleware('can:manage-users')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::patch('/usuarios/{user}/role', [UserController::class, 'updateRole'])->name('usuarios.updateRole');
        Route::patch('/usuarios/{user}/active', [UserController::class, 'updateActive'])->name('usuarios.updateActive');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    });
});

require __DIR__ . '/auth.php';
