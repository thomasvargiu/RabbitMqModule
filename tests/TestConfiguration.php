<?php

return [
    'modules' => [
        'Zend\\Router',
        'Zend\\Mvc\\Console',
        'RabbitMqModule',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [],
        'module_paths' => [],
    ],
];
