<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'recipients'], function () use ($router) {
    $router->get('/', [
        'as' => 'recipients.getAll',
        'uses' => 'RecipientsController@getAll'
    ]);

    $router->put('/', [
        'as' => 'recipients.store',
        'uses' => 'RecipientsController@store'
    ]);

    $router->post('/vouchers', [
        'as' => 'recipients.allVouchers',
        'uses' => 'RecipientsController@allVouchers'
    ]);

    $router->post('/valid-vouchers', [
        'as' => 'recipients.validVouchers',
        'uses' => 'RecipientsController@validVouchers'
    ]);
});

$router->group(['prefix' => 'offers'], function () use ($router) {
    $router->get('/', [
        'as' => 'offers.getAll',
        'uses' => 'OffersController@getAll'
    ]);

    $router->put('/', [
        'as' => 'offers.store',
        'uses' => 'OffersController@store'
    ]);

    $router->get('/{offerId}', [
        'as' => 'offers.getOffer',
        'uses' => 'OffersController@getOffer'
    ]);

    $router->post('/{offerId}/new-vouchers', [
        'as' => 'offers.newVouchers',
        'uses' => 'OffersController@newVouchers'
    ]);
});

$router->post('validate-voucher',[
    'as' => 'validadeVoucher',
    'uses' => 'RecipientsController@validateVoucher'
]);

$router->get('{endpoint}', [
    'as' => 'notValidEndpoint',
    'uses' => 'Controller@notValidEndpoint'
]);
$router->put('{endpoint}', [
    'as' => 'notValidEndpoint',
    'uses' => 'Controller@notValidEndpoint'
]);
$router->post('{endpoint}', [
    'as' => 'notValidEndpoint',
    'uses' => 'Controller@notValidEndpoint'
]);
