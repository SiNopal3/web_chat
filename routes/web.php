<?php

use App\Models\User;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // Ambil semua data user KECUALI akun yang sedang login saat ini
    $users = User::where('id', '!=', auth()->id())->get();
    
    // Kirim data user tersebut ke halaman dashboard
    return view('dashboard', compact('users'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
use App\Http\Controllers\MessageController;

// Jalur untuk ambil dan kirim pesan
Route::get('/messages/{user}', [MessageController::class, 'fetchMessages'])->middleware('auth');
Route::post('/messages/{user}', [MessageController::class, 'sendMessage'])->middleware('auth');