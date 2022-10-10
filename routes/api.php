<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\WeatherRequestController;
use App\Models\WeatherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::get('token', [AuthController::class, 'getToken'])->name('token');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::group(
        ['prefix' => 'weather-request/'],
        function() {
            //Weather Request routes
            Route::get('/', [WeatherRequestController::class, 'index'])->name('weather-requests');
            Route::get('/{id}', [WeatherRequestController::class, 'show'])->name('weather-requests.show');
            Route::delete('/{id}', [WeatherRequestController::class, 'destroy'])->name('weather-requests.delete');
            Route::post('/', [WeatherRequestController::class, 'store'])->name('weather-requests.store');
            //Comments routes
            Route::get('/{weather_request}/comments', [CommentsController::class, 'index'])->name('weather-request.comment.index');
            Route::post('/{weather_request}/comment', [CommentsController::class, 'store'])->name('weather-request.comment.store');
            Route::delete('/{weather_request}/comment/{comment}', [CommentsController::class, 'destroy'])->name('weather-requests.comment.delete');
        }
    );
});
