<?php
// DIC configuration
$container = $app->getContainer();
// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};
// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    //$logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
// Service factory for the ORM
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container->get('settings')['db']);

$capsule->setAsGlobal();
$capsule->bootEloquent();
$container['db'] = function ($c) use ($capsule) {
    return $capsule;
};

// Authentication Bearer config
$container['JwtAuthentication'] = function ($c) {
    $settings = $c->get('settings')['jwt'];
    $settings["path"] = ["/Users", "/Groups"];
    $settings["logger"] = $c->get('logger');
    $settings["attribute"] = "jwt";
    if (!isset($settings['error'])) {
        $settings["error"] = function ($request, $response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            return $response
                ->withHeader("Content-Type", "application/json")
                ->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        };
    }
    return new \Slim\Middleware\JwtAuthentication($settings);
};

// Controllers
$container['HomeController'] = function ($container){
    return new \App\Controllers\HomeController($container);
};

$container['JWTController'] = function ($container){
    return new \App\Controllers\JWTController($container);
};

$container['UserController'] = function ($container){
    return new \App\Controllers\UserController($container);
};

$container['GroupController'] = function ($container){
    return new \App\Controllers\GroupController($container);
};
