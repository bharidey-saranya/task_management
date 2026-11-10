<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
Route::post('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');

Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
Route::patch('/projects/{project}/toggle-status', [ProjectController::class, 'toggleStatus'])->name('projects.toggle-status');
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
