<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\BusinessesController;
use App\Http\Controllers\RoleUsersController;
use App\Http\Controllers\BusinessUsersController;
use App\Http\Controllers\RelationLedgerRequestController;



// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('/categories', CategoriesController::class);
Route::put('categories/{id}/restore', [CategoriesController::class, 'restore']);
Route::apiResource('/businesses', BusinessesController::class);
Route::apiResource('/role_users', RoleUsersController::class);
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // User CRUD
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    //Business- User-Role assignments
    Route::apiResource('/business_users', BusinessUsersController::class);

    // Relation Ledger Requests
    Route::apiResource('/relation_ledger_requests', RelationLedgerRequestController::class);
    Route::put('/ledger-request/{id}/accept', [RelationLedgerRequestController::class, 'acceptLedgerRequest']);
    Route::put('/ledger-request/{id}/cancel', [RelationLedgerRequestController::class, 'cancelLedgerRequest']);

});

