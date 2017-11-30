<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

Route::group([
    'prefix' => 'v1',
    'namespace' => 'V1',
], function () {

    Route::resource('users', 'UserController');
    Route::resource('questions', 'QuestionController');
    Route::resource('answers', 'AnswerController');
    Route::resource('tags', 'TagController');
    //Route::get('/companies/{reference}/solar-panels', 'SolarPanelsController@getSiblings');
    //Route::resource('/solar-panels', 'SolarPanelsController');
    //Route::get('/companies/{reference}/batteries', 'BatteriesController@getSiblings');
    //Route::resource('/batteries', 'BatteriesController');
});


// route for /api
Route::any('/', function() {
    throw new NotFoundHttpException('path does not exist:/');
});

// catch all route for any route under /api/
Route::any('{path}', function($path) {
    throw new NotFoundHttpException('Path does not exist:'.$path);
})->where('path', '.*');
