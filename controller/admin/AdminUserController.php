<?php
declare(strict_types=1);

class AdminUserController extends Controller
{
    public function index(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : AdminUserController::index';
    }

    public function show(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : AdminUserController::show';
    }

    public function update(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : AdminUserController::update';
    }

    public function suspend(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : AdminUserController::suspend';
    }

}
