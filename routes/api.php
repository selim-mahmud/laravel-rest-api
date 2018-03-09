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

    Route::resource('questions', 'QuestionController');
    Route::get('questions/{reference}/answers', 'QuestionController@getAnswers');
    Route::get('questions/{reference}/tags', 'QuestionController@getTags');
    Route::get('questions/{reference}/user', 'QuestionController@getUser');
    Route::resource('answers', 'AnswerController');
    Route::get('answers/{reference}/question', 'AnswerController@getQuestion');
    Route::get('answers/{reference}/user', 'AnswerController@getUser');
    Route::resource('tags', 'TagController');
    Route::get('tags/{reference}/questions', 'TagController@getQuestions');
    Route::resource('users', 'UserController');
    Route::get('users/{reference}/questions', 'UserController@getQuestions');
    Route::get('users/{reference}/answers', 'UserController@getAnswers');
});


// route for /api
Route::any('/', function() {
    throw new NotFoundHttpException();
});

// catch all route for any route under /api/
Route::any('{path}', function($path) {
    throw new NotFoundHttpException();
})->where('path', '.*');
