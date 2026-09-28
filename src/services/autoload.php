<?php

spl_autoload_register(function ($class) {
    $map = [
        'models\\'      => MODELS,
        'controllers\\' => CONTROLLERS,
    ];

    foreach ($map as $prefix => $base_dir) {
        if (strpos($class, $prefix) === 0) {
            $file = $base_dir . substr(str_replace('\\', '/', $class), strlen($prefix)) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});