<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemplateController;

Route::prefix('v1')->middleware('verify.token')->group(function () {
    Route::get('/templates', [TemplateController::class, 'index']);
    Route::get('/templates/{code}', [TemplateController::class, 'show']);
    Route::post('/templates', [TemplateController::class, 'store']);
    Route::put('/templates/{id}', [TemplateController::class, 'update']);
    Route::delete('/templates/{id}', [TemplateController::class, 'destroy']);
});
