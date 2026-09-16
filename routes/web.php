<?php

use App\Http\Controllers\SteamController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::post('/search', [SteamController::class, 'search']);

Route::get('/{id}', [SteamController::class, 'show'])
    ->where('id', '[a-zA-Z0-9:_\.\-\[\]]+');
