<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\EventController;

Route::get('/', [EventController::class,'index']);

Route::get('/events/create', [EventController::class,'create'])->middleware('auth');

Route::get('/events/{id}', [EventController::class,'show']);

// Cadastrar no banco via POST
Route::post('/events', [EventController::class,'store']);

// Delete
Route::delete('/events/{id}', [EventController::class,'destroy'])->middleware('auth');

// Update
Route::get('/events/edit/{id}', [EventController::class,'edit'])->middleware('auth');
Route::put('/events/update/{id}', [EventController::class,'update'])->middleware('auth');

// Route::get('/', function(){};

Route::get('/dashboard', [EventController::class, 'dashboard'])->middleware('auth');

// many to many
Route::post('/events/join/{id}', [EventController::class,'joinEvent'])->middleware('auth');

// remover presença do usuario
Route::delete('/events/leave/{id}', [EventController::class,'leaveEvent'])->middleware('auth');