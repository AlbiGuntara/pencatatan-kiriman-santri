<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('shipments/create', [ShipmentController::class, 'create'])->name('shipments.create');
    Route::post('shipments', [ShipmentController::class, 'store'])->name('shipments.store');
    Route::get('shipments/print', [ShipmentController::class, 'print'])->name('shipments.print');
    Route::delete('shipments/destroy-many', [ShipmentController::class, 'destroyMany'])->name('shipments.destroy-many');
    Route::post('shipments/bulk-status', [ShipmentController::class, 'bulkUpdateStatus'])->name('shipments.bulk-status');
    Route::get('shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::post('shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.status');
    Route::delete('shipments/{shipment}', [ShipmentController::class, 'destroy'])->name('shipments.destroy');

    Route::get('students', [StudentController::class, 'index'])->name('students.index');
    Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('students', [StudentController::class, 'store'])->name('students.store');
    Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::patch('students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    Route::get('students/search', [StudentController::class, 'search'])->name('students.search');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('regions', [RegionController::class, 'index'])->name('regions.index');
        Route::get('regions/create', [RegionController::class, 'create'])->name('regions.create');
        Route::post('regions', [RegionController::class, 'store'])->name('regions.store');
        Route::get('regions/{region}/edit', [RegionController::class, 'edit'])->name('regions.edit');
        Route::patch('regions/{region}', [RegionController::class, 'update'])->name('regions.update');
        Route::delete('regions/{region}', [RegionController::class, 'destroy'])->name('regions.destroy');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/settings.php';
