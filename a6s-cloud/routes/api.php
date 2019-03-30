<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('analysis-results', 'AnalysisResultsController@index');
Route::get('analysis-results/{id}', 'AnalysisResultController@show');

Route::get('v1/AnalysisResultLists', 'AnalysisResultListsController@index');
Route::post('v1/AnalysisRequests', 'AnalysisRequestsController@create');
