<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->get('/user', [AuthController::class, 'userDetails']);
Route::middleware(['auth:api','role:1'])->group(function () {
    Route::get('/conversation/{conversation_id}', function ($conversation_id) {
        return response()->json(['message' => 'You are An Admin']);

    });
});
