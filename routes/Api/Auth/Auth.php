<?php

$api = app('Dingo\Api\Routing\Router');

$api->group(['namespace' => 'Auth', 'as' => 'api.auth.'], function ($api) {
    $api->post('authenticate', 'AuthenticateController@authenticate')->name('authenticate');
    $api->post('webauthenticate', 'AuthenticateController@webAuthenticate')->name('webauthenticate');
    $api->get('token', 'AuthenticateController@getToken')->name('token');

    $api->post('register', 'UsersController@register')->name('register');

    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('me', 'AuthenticateController@authenticatedUser')->name('me');
        $api->get('check', 'AuthenticateController@checkToken')->name('check');
        $api->get('logout', 'AuthenticateController@logout')->name('logout');

        $api->post('changePassword', 'UsersController@changePassword')->name('changePassword');

        $api->group(array('before' => 'role:backend'), function ($api) {
            $api->get('users', 'UsersController@index');
            $api->post('users', 'UsersController@store');
            $api->put('users/{id}', 'UsersController@update');
            $api->get('approve/{cf_code}', 'UsersController@approve');
            $api->delete('users/{id}', 'UsersController@deleteUser');
            $api->put('users/{id}/newpassword', 'UsersController@setPasswordUser');
        });
    });
});