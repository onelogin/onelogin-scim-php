<?php

namespace App\Controllers;

use \Firebase\JWT\JWT;

class HomeController extends Controller
{
    public function index($request, $response, $args)
    {
        return $this->renderer->render($response, 'index.phtml', $args);
    }
}
