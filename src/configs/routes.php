<?php

/**
 * @psalm-import-type Route from types
 */

require_once CONTROLLERS . 'Notfound.php';

/**
 * @var Route[] $routes
 */
$routes = [
    // Homepage
    [
        'id' => 'homepage',
        'value' => '/',
        'controller' => 'controllers\\HomepageController',
        'call' => 'makeHome',
        'isRegex' => false,
        'method' => 'GET'
    ],
    // Membros
    [
        'id' => 'membros',
        'value' => '/membros',
        'controller' => 'controllers\\MembroController',
        'call' => 'makeMembros',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'membro',
        'value' => '/^\/membros\/[0-9]+$/',
        'controller' => 'controllers\\MembroController',
        'call' => 'makeMembro',
        'isRegex' => true,
        'method' => 'GET'
    ],
    [
        'id' => 'membroSave',
        'value' => '/membros/save',
        'controller' => 'controllers\\MembroController',
        'call' => 'saveMembro',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'membroEdit',
        'value' => '/membros/edit',
        'controller' => 'controllers\\MembroController',
        'call' => 'makeEdit',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'membroUpdate',
        'value' => '/membros/update',
        'controller' => 'controllers\\MembroController',
        'call' => 'updateMembro',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'membroDelete',
        'value' => '/membros/delete',
        'controller' => 'controllers\\MembroController',
        'call' => 'deleteMembro',
        'isRegex' => false,
        'method' => 'POST'
    ],
    // Cliente
    [
        'id' => 'clientes',
        'value' => '/clientes',
        'controller' => 'controllers\\ClienteController',
        'call' => 'makeClientes',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'cliente',
        'value' => '/^\/clientes\/[0-9]+$/',
        'controller' => 'controllers\\ClienteController',
        'call' => 'makeCliente',
        'isRegex' => true,
        'method' => 'GET'
    ],
    [
        'id' => 'clienteSave',
        'value' => '/clientes/save',
        'controller' => 'controllers\\ClienteController',
        'call' => 'saveCliente',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'clienteEdit',
        'value' => '/clientes/edit',
        'controller' => 'controllers\\ClienteController',
        'call' => 'makeEdit',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'clienteUpdate',
        'value' => '/clientes/update',
        'controller' => 'controllers\\ClienteController',
        'call' => 'updateCliente',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'clienteDelete',
        'value' => '/clientes/delete',
        'controller' => 'controllers\\ClienteController',
        'call' => 'deleteCliente',
        'isRegex' => false,
        'method' => 'POST'
    ],
];
