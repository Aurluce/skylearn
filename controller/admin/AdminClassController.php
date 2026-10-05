<?php
declare(strict_types=1);

class AdminClassController extends Controller
{
    public function index(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : AdminClassController::index';
    }

    public function store(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : AdminClassController::store';
    }

    public function update(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : AdminClassController::update';
    }

    public function destroy(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : AdminClassController::destroy';
    }

}
