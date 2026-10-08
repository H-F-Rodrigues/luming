<?php

// Inicia sessão se ainda não iniciou
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// session_destroy();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Formulários HTML só enviam GET e POST. Para PUT, PATCH e DELETE, o formulário envia
// POST com o campo oculto "_method" (ex.: <input type="hidden" name="_method" value="DELETE">).
if ($method === 'POST' && isset($_POST['_method'])) {
    $override = strtoupper((string) $_POST['_method']);

    if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
        $method = $override;
    }
}

// Área administrativa: só entra quem estiver logado (vale para qualquer URL /admin/...).
if ($uri === '/admin' || str_starts_with((string) $uri, '/admin/')) {
    adminGuard();
}

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
    echo $controllerClass;
    echo "Classe tem não";
    makeNotFound();
    return;
}

// Verifica se o método é estático e existe
if (!method_exists($controllerClass, $methodName)) {
    echo 'Metódo errado chefe';
    makeNotFound();
    return;
}

// Chama o método estático da classe, passando os parâmetros
try {
    $controllerClass::$methodName($route, $uri);
} catch (Exception $e) {
    makePage('erro', [
        'title' => "Erro: {$e->getCode()}",
        'erros' => [],
        'msg' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
}
