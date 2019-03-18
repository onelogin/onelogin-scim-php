<?php

namespace App\Controllers;

use \Firebase\JWT\JWT;

class JWTController extends Controller
{
    public function generate($request, $response)
    {
        // TODO: Authenticate user with user/password before provide the Bearer token

        $settings = $this->__get('settings');
        $token = JWT::encode(["user" => "admin", "password" => "admin"], $settings['jwt']['secret']);
        return $response->withJson(['Bearer' => $token]);
    }
}
