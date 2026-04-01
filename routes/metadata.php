<?php

use App\Http\Controllers\MetadataController;
use Illuminate\Support\Facades\Route;

Route::prefix('metadata')
    ->as('metadata.')
    ->controller(MetadataController::class)
    ->group(function () {
        Route::get('/countries', 'countries')->name('countries');
        Route::get('/languages', 'languages')->name('languages');
        Route::get('/phone-countries', 'phoneCountries')->name('phone-countries');
    });
