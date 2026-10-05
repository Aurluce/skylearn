<?php
/** @var Router $router */
declare(strict_types=1);

$router->get('/api/search', 'SearchController@index');

$router->post('/api/quizzes/{id}/start', 'QuizController@start', ['auth']);
$router->post('/api/quizzes/{id}/submit', 'QuizController@submit', ['auth']);

$router->post('/api/payments/initiate', 'PaymentController@initiate', ['auth']);
$router->get('/api/payments/{id}/status', 'PaymentController@status', ['auth']);
$router->post('/api/payments/webhook', 'PaymentController@webhook');   // exempté de CSRF

$router->get('/api/notifications', 'NotificationController@list', ['auth']);
$router->post('/api/notifications/read-all', 'NotificationController@markAllRead', ['auth']);
$router->post('/api/notifications/{id}/read', 'NotificationController@markRead', ['auth']);
$router->get('/api/prices', 'SubscriptionController@pricesForClass');