<?php

$api = app('Dingo\Api\Routing\Router');
$api->get('mobile/version', 'ConstructionController@appVersion');

$api->group(['namespace' => 'Asset', 'as' => 'api.asset.'], function ($api) {

    $api->get('test/{id}', 'ConstructionController@getGeometry'); #dùng tạm -----------------------------------------
    $api->post('test', 'ConstructionController@addNewGeometry');

    $api->get('mobile/version', 'ConstructionController@appVersion');
    $api->get('public_projects', 'PublicProjectController@index');
    $api->get('public_projects/{id}', 'PublicProjectController@show');
    $api->post('projects/upload/image', 'ProjectController@uploadImage');
    $api->post('projects/upload/document', 'ProjectController@uploadDoc');
    $api->get('departments', 'DepartmentController@index');

    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('constructions', 'ConstructionController@mobileIndex');
        $api->get('webconstructions', 'ConstructionController@index');
        $api->get('constructions/{id}', 'ConstructionController@show');
        $api->delete('constructions/{id}', 'ConstructionController@delete');
        $api->get('structures', 'StructureController@index');
        $api->get('funding_agencies', 'FundingAgencyController@index');
        $api->post('funding_agencies', 'FundingAgencyController@store');
        $api->delete('funding_agencies/{id}', 'FundingAgencyController@destroy');
        $api->put('funding_agencies/{id}', 'FundingAgencyController@update');
        $api->get('funding_sources', 'FundingAgencyController@getFundingSources');
        $api->get('legends', 'ConstructionController@getLegends');

        $api->get('orders', 'OrderController@index');
        $api->get('orders/{id}', 'OrderController@show');
        $api->delete('orders/{id}', 'OrderController@destroy');
        $api->put('orders/{id}', 'OrderController@update');

        $api->get('reversegeocode', 'ConstructionController@reverseGeocode');
        $api->get('v1/reversegeocode', 'ConstructionController@reverseGeocodeNew');
        $api->get('work_orders', 'OrderController@getOrders');
        $api->get('districts', 'ConstructionController@getDistricts');
        $api->get('circles/{district}', 'ConstructionController@getCircles');
        $api->get('blocks/{district}', 'ConstructionController@getBlocks');

        $api->get('work_orders/report/{id}', 'OrderController@makeReport');
        $api->get('summary/report/{id}', 'OrderController@projectReport');

        $api->get('reports', 'ReportController@index');
        $api->post('reports', 'ReportController@store');

        $api->post('reports/images', 'ImageController@store');

        $api->delete('reports/{id}', 'ReportController@destroy');
        $api->post('reports/{id}', 'ReportController@update');
        $api->get('projects', 'ProjectController@index');
        $api->get('projects/{id}', 'ProjectController@show');
        $api->post('projects', 'ProjectController@store');
        $api->put('projects/{id}', 'ProjectController@update');
        $api->get('checkMISExists', 'ProjectController@checkMISExists');
        $api->get('exports/projects', 'ProjectController@exportExcel');
        $api->delete('projects/{id}', 'ProjectController@delete');
        $api->get('projects/values/executingdepartments', 'ProjectController@getExecutingDepartments');
        $api->get('projects_kpi', 'ProjectController@kpiIndex');

        $api->get('notifications', 'NotificationController@getNotifications');
        $api->get('notifications/{id}', 'NotificationController@show');
        $api->get('notifications/admin/{id}', 'NotificationController@adminShow');
        $api->get('notifications/{id}/read', 'NotificationController@readNotification');
        $api->delete('notifications/{id}/delete', 'NotificationController@deleteNotification');

        $api->post('meetings', 'MeetingController@store');
        $api->post('update_meeting/{id}', 'MeetingController@update');

        $api->get('dashboard', 'DashboardController@index');
        $api->get('dashboard/districts', 'DashboardController@getDistricts');
        $api->get('dashboard/circles', 'DashboardController@getCircles');
    });
});
