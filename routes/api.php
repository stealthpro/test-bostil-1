<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Api'], function () {
    Route::apiResource('folders', 'FolderController');

    Route::post('pages/{page}/publish', 'PageController@publish')->name('pages.publish');
    Route::apiResource('pages', 'PageController');
});
