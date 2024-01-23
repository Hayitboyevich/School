<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('home');
    Route::get('/quizzes', [\App\Http\Controllers\QuizzesController::class, 'index'])->name('quizzes.index');
    Route::get('/books', [\App\Http\Controllers\BooksController::class, 'index'])->name('books.index');
    Route::middleware('studentAccess')->group(function () {
        Route::get('/quizzes/{id}', [\App\Http\Controllers\QuizzesController::class, 'show'])->name('quizzes.show');
        Route::post('/quizzes/{id}/attempt', [\App\Http\Controllers\QuizzesController::class, 'attempt'])->name('quizzes.attempt');
//        Route::get('/quizzes/{id}/result', [\App\Http\Controllers\QuizzesController::class, 'result'])->name('quizzes.result');
        Route::get('/books/{id}', [\App\Http\Controllers\BooksController::class, 'show'])->name('books.show');
        Route::get('/books/change-status/{id}/{status}', [\App\Http\Controllers\BooksController::class, 'change_status'])->name('books.change-status');
        Route::get('/book-chapters/change-status/{id}/{status}', [\App\Http\Controllers\BookChapterController::class, 'change_status'])->name('book-chapter.change-status');
    });
    Route::get('/dashboard', function () {return redirect()->route('books.index');})->name('dashboard');
    Route::get('/user/profile/change-password', [\App\Http\Controllers\Auth\PasswordController::class, 'change'])->name('password.change');
    Route::post('/user/profile/change-password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');
});
Route::middleware('auth.filament')->group(function () {
    Route::get('/sehriyo/sync-subjects', [\App\Http\Controllers\SehriyoSyncController::class, 'sync_subjects'])->name('sehriyo.sync-subjects');
    Route::get('/sehriyo/sync-academic-years', [\App\Http\Controllers\SehriyoSyncController::class, 'sync_academic_years'])->name('sehriyo.sync-academic-years');
});
