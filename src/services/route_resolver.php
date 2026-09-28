<?php

/**
 * @param string $uri
 * @param Route[] $routes
 * @param string $method (GET, POST, etc.)
 * @return Route|null
 */
function resolveRoute(string $uri, array $routes, string $method = 'GET'): ?array {
    foreach ($routes as $route) {
        // Verifica se o método da rota coincide (se definido)
        if (isset($route['method']) && strtoupper($route['method']) !== $method) {
            continue;
        }

        if (empty($route['value'])) {
            continue;
        }

        if (empty($route['isRegex']) && $uri === $route['value']) {
            return $route;
        }

        if ($route['isRegex'] && preg_match($route['value'], $uri)) {
            return $route;
        }
    }

    return null;
}