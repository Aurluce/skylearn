<?php
declare(strict_types=1);

class NotificationController extends Controller
{
    public function index(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : NotificationController::index';
    }

    public function list(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : NotificationController::list';
    }

    public function markRead(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : NotificationController::markRead';
    }

    public function markAllRead(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : NotificationController::markAllRead';
    }

}
