<?php

use App\Models\User;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\GroupController;
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

// Semua rute di dalam grup ini dikunci dengan middleware auth
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // -- Jalur Private Chat --
    Route::get('/messages/{user}', [MessageController::class, 'fetchMessages']);
    Route::post('/messages/{user}', [MessageController::class, 'sendMessage']);

    // -- Jalur Grup Chat --
    Route::get('/groups', [GroupController::class, 'index']);
    Route::post('/groups', [GroupController::class, 'store']);
    Route::post('/groups/{group}/join', [GroupController::class, 'join']);
    Route::get('/groups/{group}/messages', [GroupController::class, 'fetchMessages']);
    Route::post('/groups/{group}/messages', [GroupController::class, 'sendMessage']);
});

require __DIR__.'/auth.php';