<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['middleware' => 'api.auth', 'namespace' => 'App\Http\Controllers\V1'], function ($api) {

    // EDGES
    $api->get('edges/', 'EdgesController@index');

    $api->get('edges/{id}', 'EdgesController@get');

    $api->delete('edges/{id}', 'EdgesController@delete');

    $api->post('edges/', 'EdgesController@create');

    $api->put('edges/{id}', 'EdgesController@update');

    $api->put('edges/{id}/remove-property', 'EdgesController@removeProperty');

    //VERTEX
    $api->get('vertex/', 'VertexController@index');

    $api->get('vertex/{id}', 'VertexController@get');

    $api->delete('vertex/{id}', 'VertexController@delete');

    $api->post('vertex/', 'VertexController@create');

    $api->put('vertex/{id}', 'VertexController@update');

    $api->put('vertex/{id}/remove-property', 'VertexController@removeProperty');

    //GENERIC
    $api->get('gremlin/clear-data', 'GremlinController@clearData');

    $api->post('gremlin/run-query/', 'GremlinController@runQuery');

    $api->post('gremlin/create-index/', 'GremlinController@CreateIndex');

    //PRODUCT RECOMMENDATION
    $api->post('recommendation/view-also-view/', 'RecommendationsController@GetViewAlsoView');
});
