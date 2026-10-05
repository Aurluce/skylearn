<?php
declare(strict_types=1);

class AdminDashboardController extends Controller
{
    public function index(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : AdminDashboardController::index';
    }

}
