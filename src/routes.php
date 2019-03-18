<?php

use Auth\BearerAuthenticationMiddleweare;
use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/', function (Request $request, Response $response, array $args) {
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/jwt', 'JWTController:generate')->setName('jwt.generate');

// Users
$app->get('/Users', 'UserController:list')->setName('users.list');
$app->get('/Users/{id}', 'UserController:get')->setName('users.get');
$app->post('/Users', 'UserController:create')->setName('users.create');
$app->put('/Users/{id}', 'UserController:update')->setName('users.update');
$app->delete('/Users/{id}', 'UserController:delete')->setName('users.delete');

// Groups

$app->get('/Groups', 'GroupController:list')->setName('groups.list');
$app->post('/Groups', 'GroupController:create')->setName('groups.create');
$app->patch('/Groups/{id}', 'GroupController:update')->setName('groups.update');
