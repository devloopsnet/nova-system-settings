<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Devloops\NovaSystemSettings\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::name('nova.api.nova-system-settings.')
     ->controller(ApiController::class)
     ->group(function () {
         Route::post('save-settings', 'saveSettings')
              ->name('save-settings');

         Route::get('load-settings', 'loadSettings')
              ->name('load-settings');
     });
