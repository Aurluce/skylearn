<?php
declare(strict_types=1);

class ProfileController extends Controller
{
    public function show(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : ProfileController::show';
    }

    public function update(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : ProfileController::update';
    }

    public function updatePassword(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : ProfileController::updatePassword';
    }

    public function settings(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : ProfileController::settings';
    }

}
