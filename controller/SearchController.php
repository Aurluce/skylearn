<?php
declare(strict_types=1);

class SearchController extends Controller
{
    public function index(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : SearchController::index';
    }

}
