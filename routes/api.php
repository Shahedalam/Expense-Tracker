<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
Route::prefix('v1')->group(function () {
    //basic
    Route::post('auth/register',[\App\Http\Controllers\AuthController::class,'createUser']);
    Route::post('auth/login',[\App\Http\Controllers\AuthController::class,'loginUser']);

    //Auth required
    Route::middleware([
        'auth:sanctum',
    ])->group(function () {
        Route::get('current-user', function (Request $request) {
            return response()->json(collect(auth()->user())->only(['id','name','email']),200);
        });
        Route::apiResource('budget',\App\Http\Controllers\BudgetController::class);
        Route::apiResource('expense',\App\Http\Controllers\ExpenseController::class);
    });
});
