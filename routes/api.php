<?php

use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
Route::middleware(['auth:api','role:1'])->group(function () {
    Route::get('/conversation/{conversation_id}', function ($conversation_id) {
        return response()->json(['message' => 'You are An Admin']);

    });
});
