<?php
return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,

        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Renderer settings
        'renderer' => [
            'template_path' => dirname(__DIR__) . '/templates/',
        ],
        // Database settings
        'db' => [
            'driver'    => 'sqlite',
            'database'  => dirname(__DIR__) . '/db/scim.sqlite',
            'prefix'    => ''
        ],
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        // Bearer token
        'jwt' => [
            'secure' => false,
            'secret' => 'secret'
        ]
    ],
];
