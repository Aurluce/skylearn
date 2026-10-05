<?php
declare(strict_types=1);

class AdminNotificationController extends Controller
{
    public function index(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : AdminNotificationController::index';
    }

    public function send(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : AdminNotificationController::send';
    }

}
