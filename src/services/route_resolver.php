<?php

/**
 * Summary of resolveRoute
 * @psalm-import-type Route from types
 */

/**
 * Summary of resolveRoute
 * @param string $uri
 * @param Route[] $routes
 * @return Route 
 */
function resolveRoute(string $uri, $routes): ?array {
    foreach($routes as $route) {
        if(empty($route['value'])) {
            continue;
        }

        if(empty($route['isRegex']) && $uri === $route['value']) {
            return $route;
        }

        if($route['isRegex'] && preg_match($route['value'], $uri)) {
            return $route;
        }
    }

    return null;
}