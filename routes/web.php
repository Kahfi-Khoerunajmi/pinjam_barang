<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Items
    Route::resource('items', ItemController::class);

    // Loans
    Route::resource('loans', LoanController::class);
    Route::post('loans/{loan}/return', [LoanController::class, 'return'])->name('loans.return');
    Route::get('loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.return-form');
    Route::post('loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::get('my-loans', [LoanController::class, 'myLoans'])->name('loans.my-loans');
    Route::get('loans-history', [LoanController::class, 'history'])->name('loans.history');

    // Reports (Admin only)
    Route::middleware('admin')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('generate-loans', [ReportController::class, 'generateLoanReport'])->name('generate-loans');
        Route::post('generate-users', [ReportController::class, 'generateUserReport'])->name('generate-users');
        Route::post('generate-items', [ReportController::class, 'generateItemReport'])->name('generate-items');
        Route::get('overdue', [ReportController::class, 'overdueSummary'])->name('overdue');
        Route::get('monthly', [ReportController::class, 'monthlyStats'])->name('monthly');
    });

    // Users & Categories (Admin only)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    });
});

require __DIR__.'/auth.php';
