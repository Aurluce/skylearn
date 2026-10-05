<?php
declare(strict_types=1);

class PaymentController extends Controller
{
    public function history(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : PaymentController::history';
    }

    public function receipt(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : PaymentController::receipt';
    }

    public function initiate(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : PaymentController::initiate';
    }

    public function status(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : PaymentController::status';
    }

    public function webhook(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : PaymentController::webhook';
    }

}
