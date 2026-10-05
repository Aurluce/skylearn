<?php
declare(strict_types=1);

class ContactController extends Controller
{
    public function show(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : ContactController::show';
    }

    public function send(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : ContactController::send';
    }

}
