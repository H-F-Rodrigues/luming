<?php

// Inicia sessão se ainda não iniciou
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// session_destroy();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Resolve a rota com base na URI e método HTTP
$route = resolveRoute($uri, $routes, $method);

if (!$route || empty($route['controller']) || empty($route['call'])) {
    makeNotFound();
    return;
}

$controllerClass = $route['controller'];
$methodName = $route['call'];

// Verifica se a classe existe
if (!class_exists($controllerClass)) {
    makeNotFound();
    return;
}

// Verifica se o método é estático e existe
if (!method_exists($controllerClass, $methodName)) {
    makeNotFound();
    return;
}

// Chama o método estático da classe, passando os parâmetros
/*try {
    $controllerClass::$methodName($route, $uri);
} catch (Exception $e) {
    makePage('erro', [
        'title' => "Erro: {$e->getCode()}",
        'erros' => [],
        'msg' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
}*/

$controllerClass::$methodName($route, $uri);