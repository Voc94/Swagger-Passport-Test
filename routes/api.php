<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function(){
    Route::get('/user', [AuthController::class, 'userDetails']);
    Route::post('/user/send-invitation', [UserController::class, 'send_invitation']);
    Route::get('/user/receive-invitation/{invitation}', [UserController::class, 'receive_invitation'])->name('user.receive-invitation');
});

Route::middleware(['auth:api', 'role:1'])->group(function () {
    Route::get('/conversation/{conversation_id}', function ($conversation_id) {
        return response()->json(['message' => 'You are An Admin']);
    });
});
