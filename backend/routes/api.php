<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AdminController;

// Public routes
Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);

// Protected routes
Route::middleware("auth:sanctum")->group(function () {
    // User routes
    Route::get("/user", [AuthController::class, "user"]);
    Route::post("/logout", [AuthController::class, "logout"]);

    // Task routes
    Route::apiResource("tasks", TaskController::class);
    Route::post("/tasks/{task}/toggle-status", [TaskController::class, "toggleStatus"]);
    Route::post("/tasks/reorder", [TaskController::class, "reorder"]);

    // Admin routes
    Route::middleware("admin")->prefix("admin")->group(function () {
        Route::get("/dashboard", [AdminController::class, "dashboard"]);
        Route::get("/users", [AdminController::class, "users"]);
        Route::get("/users/{user}/tasks", [AdminController::class, "userTasks"]);
        Route::delete("/users/{user}", [AdminController::class, "deleteUser"]);
    });
});
