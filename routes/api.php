<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CentroEstudiosController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RequestTorneoController;
use App\Http\Controllers\UbigeosController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['cors']], function () {

    Route::controller(RegionController::class)->group(function () {
        Route::post('/add-region', 'add');
        Route::get('/get-region', 'search');
        Route::put('/update-region/{id}', 'update');
        Route::delete('/delete-region/{id}', 'delete');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::post('/add-category', 'add');
        Route::get('/get-category', 'search');
        Route::put('/update-category/{id}', 'update');
        Route::delete('/delete-category/{id}', 'delete');
    });

    Route::controller(CentroEstudiosController::class)->group(function () {
        Route::post('/add-centro-estudios', 'add');
        Route::get('/get-centro-estudios', 'search');
        Route::put('/update-centro-estudios/{id}', 'update');
        Route::delete('/delete-centro-estudios/{id}', 'delete');
    });

    Route::controller(ParticipantController::class)->group(function () {
        Route::post('/add-participant', 'add');
        Route::get('/get-participant', 'search');
        Route::put('/update-participant/{id}', 'update');
        Route::delete('/delete-participant/{id}', 'delete');
    });

    Route::controller(ClubController::class)->group(function () {
        Route::post('/add-club', 'add');
        Route::get('/get-clubs', 'search');
        Route::put('/update-club/{id}', 'update');
        Route::delete('/delete-club/{id}', 'delete');
    });

    Route::controller(UbigeosController::class)->group(function () {
        Route::get('/get-ubigeos', 'search');
    });

    Route::controller(AuthController::class)->group(function () {
        Route::post('/auth_login', 'login');
        Route::get('/token_decriptToken', 'decriptToken');
    });

    Route::controller(UserController::class)->group(function () {
        Route::post('/add-user', 'add');
        Route::put('/update-user/{id}', 'update');
        Route::get('/get-users', 'search');
        Route::delete('/delete-user/{id}', 'delete');
    });

    Route::controller(RequestTorneoController::class)->group(function () {
        Route::post('/add-request-torneo', 'add');
        Route::get('/get-request-torneo', 'search');
        Route::get('/generate-pdf', 'generarPdf');
        Route::get('/generate-excel', 'generarExcel');
    });
});
