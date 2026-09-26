<?php

use App\Http\Controllers\SteamController;
use Illuminate\Support\Facades\Route;

/* Route::view rather than a closure: a closure route cannot be serialized,
   which is what previously made `php artisan route:cache` impossible. */
Route::view('/', 'welcome');

/*
 * Both Steam-backed routes are rate limited. Every vanity search costs one
 * ResolveVanityURL call and every uncached profile view costs one to three
 * more, all drawn from a single shared API key — so without a throttle any
 * crawler or bored script can exhaust that quota and stall the site.
 */
Route::post('/search', [SteamController::class, 'search'])
    ->middleware('throttle:30,1');

Route::get('/{id}', [SteamController::class, 'show'])
    ->where('id', '[a-zA-Z0-9:_\.\-\[\]]+')
    ->middleware('throttle:60,1');
