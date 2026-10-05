<?php
declare(strict_types=1);

class QuizController extends Controller
{
    public function index(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : QuizController::index';
    }

    public function show(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : QuizController::show';
    }

    public function result(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : QuizController::result';
    }

    public function start(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : QuizController::start';
    }

    public function submit(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : QuizController::submit';
    }

}
