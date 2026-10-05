<?php
/** @var Router $router */
declare(strict_types=1);

/** Enregistre index / store / update / destroy pour une ressource d'administration */
function crud(Router $r, string $prefix, string $controller, array $mw): void
{
    $r->get($prefix, "$controller@index", $mw);
    $r->post($prefix, "$controller@store", $mw);
    $r->post("$prefix/{id}/update", "$controller@update", $mw);
    $r->post("$prefix/{id}/delete", "$controller@destroy", $mw);
}

// ----- Public -----
$router->get('/', 'HomeController@index');
$router->get('/about', 'HomeController@about');
$router->get('/subjects', 'HomeController@subjects');
$router->get('/testimonials', 'HomeController@testimonials');
$router->get('/subscriptions', 'SubscriptionController@plans');
$router->get('/courses', 'ContentController@index');
$router->get('/courses/{class}', 'ContentController@classe');
$router->get('/courses/{class}/{subject}', 'ContentController@subject');
$router->get('/content/{id}', 'ContentController@show');
$router->get('/download/{id}', 'DownloadController@file');
$router->get('/contact', 'ContactController@show');
$router->post('/contact', 'ContactController@send');

// ----- Authentification -----
$router->get('/login', 'AuthController@showLogin', ['guest']);
$router->post('/login', 'AuthController@login', ['guest']);
$router->get('/register', 'AuthController@showRegister', ['guest']);
$router->post('/register', 'AuthController@register', ['guest']);
$router->post('/logout', 'AuthController@logout', ['auth']);
$router->get('/forgot-password', 'AuthController@showForgot', ['guest']);
$router->post('/forgot-password', 'AuthController@sendReset', ['guest']);
$router->get('/reset-password/{token}', 'AuthController@showReset', ['guest']);
$router->post('/reset-password', 'AuthController@reset', ['guest']);

// ----- Espace élève -----
$user = ['auth'];
$router->get('/dashboard', 'DashboardController@index', $user);
$router->get('/profile', 'ProfileController@show', $user);
$router->post('/profile', 'ProfileController@update', $user);
$router->post('/profile/password', 'ProfileController@updatePassword', $user);
$router->get('/settings', 'ProfileController@settings', $user);
$router->get('/my-courses', 'ContentController@mine', $user);
$router->get('/subscription', 'SubscriptionController@show', $user);
$router->post('/subscription/checkout', 'SubscriptionController@checkout', $user);
$router->get('/payments', 'PaymentController@history', $user);
$router->get('/payments/{id}/receipt', 'PaymentController@receipt', $user);
$router->get('/notifications', 'NotificationController@index', $user);
$router->get('/quizzes', 'QuizController@index', $user);
$router->get('/quizzes/{id}', 'QuizController@show', $user);
$router->get('/quizzes/result/{id}', 'QuizController@result', $user);

// ----- Administration -----
$admin = ['auth', 'admin'];
$router->get('/admin', 'AdminDashboardController@index', $admin);
$router->get('/admin/users', 'AdminUserController@index', $admin);
$router->get('/admin/users/{id}', 'AdminUserController@show', $admin);
$router->post('/admin/users/{id}/update', 'AdminUserController@update', $admin);
$router->post('/admin/users/{id}/suspend', 'AdminUserController@suspend', $admin);

crud($router, '/admin/classes', 'AdminClassController', $admin);
crud($router, '/admin/subjects', 'AdminSubjectController', $admin);
crud($router, '/admin/chapters', 'AdminChapterController', $admin);

crud($router, '/admin/contents', 'AdminContentController', $admin);
$router->get('/admin/contents/create', 'AdminContentController@create', $admin);
$router->get('/admin/contents/{id}/edit', 'AdminContentController@edit', $admin);

$router->get('/admin/subscriptions', 'AdminSubscriptionController@index', $admin);
$router->post('/admin/subscriptions/prices', 'AdminSubscriptionController@updatePrices', $admin);

$router->get('/admin/payments', 'AdminPaymentController@index', $admin);
$router->get('/admin/payments/{id}', 'AdminPaymentController@show', $admin);

$router->get('/admin/notifications', 'AdminNotificationController@index', $admin);
$router->post('/admin/notifications', 'AdminNotificationController@send', $admin);

crud($router, '/admin/announcements', 'AdminAnnouncementController', $admin);
crud($router, '/admin/testimonials', 'AdminTestimonialController', $admin);
$router->get('/admin/statistics', 'AdminStatisticsController@index', $admin);

crud($router, '/admin/quizzes', 'AdminQuizController', $admin);
$router->get('/admin/quizzes/create', 'AdminQuizController@create', $admin);
$router->get('/admin/quizzes/{id}/edit', 'AdminQuizController@edit', $admin);
$router->post('/admin/quizzes/import', 'AdminQuizController@importCsv', $admin);

$router->get('/exams', 'ExamController@index');
$router->get('/exams/{class}', 'ExamController@classe');
$router->get('/exams/{class}/{subject}', 'ExamController@subject');
