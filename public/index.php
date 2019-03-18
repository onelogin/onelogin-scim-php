<?php

require dirname(__DIR__) . '/vendor/autoload.php';
session_start();
// Instantiate the app
$settings = require dirname(__DIR__) . '/src/settings.php';
$app = new \Slim\App($settings);
// Set up dependencies
require dirname(__DIR__) . '/src/dependencies.php';
// Register middleware
require dirname(__DIR__) . '/src/middleware.php';
// Register routes
require dirname(__DIR__) . '/src/routes.php';
// Run app
$app->run();
