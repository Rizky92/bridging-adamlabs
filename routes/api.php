<?php

use App\Http\Controllers\API\BridgingAdamlabsController;
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

Route::group(['middleware' => 'api.key'], function () {
    Route::post('/adam-lis/bridging', [BridgingAdamlabsController::class, 'store']);
    Route::post('/adam-lis/bridging/update-hasil', [BridgingAdamlabsController::class, 'updateHasil']);
});