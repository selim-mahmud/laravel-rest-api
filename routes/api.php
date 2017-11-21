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

/*
 * NOTE:
 *
 * ALL ROUTES IN THIS FILE ARE ALREADY PREFIXED WITH '/api/'
 * ALL NAMESPACES ARE PREFIXED WITH \Api for all controllers
 */

Route::group([
    'prefix' => 'v1',
    'namespace' => 'V1',
], function () {

    Route::get('users', 'UsersController@index');

});

// catch all route for any route under /api/
Route::get('{path}', function($path) {
    throw new NotFoundHttpException('Path does not exist:'.$path);
});