<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FileController;

Route::middleware('api')->group(function () {
    Route::post('/v1/upload', [FileController::class, 'upload']);
});
