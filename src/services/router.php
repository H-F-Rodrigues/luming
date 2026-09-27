<?php

$uri = $_SERVER['PATH_INFO'] ?? null;

if(empty($uri)) {
    makeHome();

    return;
}

$route = resolveRoute($uri, $routes);

if(!$route || empty($route['call']) || !function_exists($route['call'])) {
    makeNotFound();

    return;
}

$route['call']($route, $uri);
