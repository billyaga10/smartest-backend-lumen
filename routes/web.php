<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return response()->json([
        'app' => 'SMARTest API - Smart Madrasah Attendance System',
        'version' => '1.0.0',
        'status' => 'ONLINE',
        'time' => date('Y-m-d H:i:s'),
    ]);
});

$router->group(['prefix' => 'api'], function () use ($router) {
    // Auth Routes
    $router->post('/auth/login', 'AuthController@login');
    $router->post('/auth/logout', 'AuthController@logout');
    $router->get('/me', 'AuthController@me');
    $router->post('/auth/change-password', 'AuthController@changePassword');

    // Student Dashboard & Attendances
    $router->get('/student/dashboard', 'StudentController@dashboard');
    $router->get('/student/attendances', 'StudentController@attendances');
    $router->get('/student/attendances/{id}', 'StudentController@attendanceDetail');

    // Student Leave Requests
    $router->get('/student/leave-requests', 'LeaveController@index');
    $router->post('/student/leave-requests', 'LeaveController@store');
    $router->get('/student/leave-requests/{id}', 'LeaveController@show');
    $router->post('/student/leave-requests/{id}/cancel', 'LeaveController@cancel');

    // Parent Routes
    $router->get('/parent/children', 'ParentController@children');

    // Teacher Routes
    $router->get('/teacher/dashboard', 'TeacherController@dashboard');

    // Notifications & FCM
    $router->get('/notifications', 'NotificationController@index');
    $router->post('/notifications/{id}/read', 'NotificationController@markAsRead');
    $router->post('/notifications/read-all', 'NotificationController@markAllAsRead');
    $router->post('/fcm/token', 'FcmController@updateToken');
    $router->post('/fcm/send', 'FcmController@sendNotification');
});
