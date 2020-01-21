<?php

return [
    'modules' => [
        'Laminas\\Router',
        'Laminas\\Mvc\\Console',
        'RabbitMqModule',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [],
        'module_paths' => [],
    ],
];
