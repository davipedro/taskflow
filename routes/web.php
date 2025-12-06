<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::patch('{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::get('{project}/tasks/create', [TaskController::class, 'createByProject'])->name('projects.tasks.create');
    Route::post('{project}/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');
});

Route::middleware(['auth', 'verified'])->prefix('tasks')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::patch('{task}/priority', [TaskController::class, 'updatePriority'])->name('tasks.updatePriority');
    Route::delete('{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});

require __DIR__.'/settings.php';
