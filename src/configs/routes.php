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

    // Contato / orçamento (formulário da homepage)
    [
        'id' => 'contato',
        'value' => '/contato',
        'controller' => 'controllers\\HomepageController',
        'call' => 'makeContato',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'contato-enviar',
        'value' => '/contato',
        'controller' => 'controllers\\HomepageController',
        'call' => 'saveContato',
        'isRegex' => false,
        'method' => 'POST'
    ],

    // Portfólio (listagem e detalhe por id numérico)
    [
        'id' => 'portfolio',
        'value' => '/portfolio',
        'controller' => 'controllers\\HomepageController',
        'call' => 'makePortfolio',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'portfolio-projeto',
        'value' => '/^\/portfolio\/[0-9]+$/',
        'controller' => 'controllers\\HomepageController',
        'call' => 'makeProjeto',
        'isRegex' => true,
        'method' => 'GET'
    ],

    // Serviços (detalhe por slug)
    [
        'id' => 'servico',
        'value' => '/^\/servicos\/[a-z0-9-]+$/',
        'controller' => 'controllers\\HomepageController',
        'call' => 'makeServico',
        'isRegex' => true,
        'method' => 'GET'
    ],

    // Login (área exclusiva)
    [
        'id' => 'login',
        'value' => '/login',
        'controller' => 'controllers\\LoginController',
        'call' => 'makeLogin',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'login-entrar',
        'value' => '/login',
        'controller' => 'controllers\\LoginController',
        'call' => 'login',
        'isRegex' => false,
        'method' => 'POST'
    ],

    [
        'id' => 'exemplo',
        'value' => '/^\/exemplos\/[0-9]+$/',
        'controller' => 'controllers\\ExemploController',
        'call' => 'exemplo',
        'isRegex' => true,
        'method' => 'GET'
    ],
];

/* ----------------------------------------------------------------------
 * DASHBOARD (área administrativa) — tudo sob o prefixo /admin.
 *
 * O router exige login para qualquer URL /admin (ver services/router.php) e entende
 * PUT/PATCH/DELETE enviados como POST + campo oculto "_method".
 * ---------------------------------------------------------------------- */

/**
 * Gera as 7 rotas de um recurso administrável:
 *
 *   GET    /admin/{slug}               makeList
 *   GET    /admin/{slug}/novo          makeCreate
 *   POST   /admin/{slug}               $calls['save']
 *   GET    /admin/{slug}/{id}          makeShow
 *   GET    /admin/{slug}/{id}/editar   makeEdit
 *   PUT    /admin/{slug}/{id}          $calls['update']
 *   DELETE /admin/{slug}/{id}          $calls['delete']
 *
 * As rotas com {id} usam regex (só números), como a rota de exemplo acima.
 *
 * @param array{save: string, update: string, delete: string} $calls
 * @return Route[]
 */
$adminRecurso = static function (string $slug, string $controller, array $calls): array {
    $base = '/admin/' . $slug;
    $comId = '/^\/admin\/' . $slug . '\/[0-9]+$/';
    $comIdEditar = '/^\/admin\/' . $slug . '\/[0-9]+\/editar$/';
    $classe = 'controllers\\' . $controller;

    return [
        ['id' => "admin-$slug-index",     'value' => $base,           'controller' => $classe, 'call' => 'makeList',       'isRegex' => false, 'method' => 'GET'],
        ['id' => "admin-$slug-novo",      'value' => $base . '/novo', 'controller' => $classe, 'call' => 'makeCreate',     'isRegex' => false, 'method' => 'GET'],
        ['id' => "admin-$slug-criar",     'value' => $base,           'controller' => $classe, 'call' => $calls['save'],   'isRegex' => false, 'method' => 'POST'],
        ['id' => "admin-$slug-show",      'value' => $comId,          'controller' => $classe, 'call' => 'makeShow',       'isRegex' => true,  'method' => 'GET'],
        ['id' => "admin-$slug-editar",    'value' => $comIdEditar,    'controller' => $classe, 'call' => 'makeEdit',       'isRegex' => true,  'method' => 'GET'],
        ['id' => "admin-$slug-atualizar", 'value' => $comId,          'controller' => $classe, 'call' => $calls['update'], 'isRegex' => true,  'method' => 'PUT'],
        ['id' => "admin-$slug-excluir",   'value' => $comId,          'controller' => $classe, 'call' => $calls['delete'], 'isRegex' => true,  'method' => 'DELETE'],
    ];
};

$routes = array_merge(
    $routes,

    [
        // Visão geral
        [
            'id' => 'admin-dashboard',
            'value' => '/admin',
            'controller' => 'controllers\\DashboardController',
            'call' => 'makeDashboard',
            'isRegex' => false,
            'method' => 'GET'
        ],
        // O LoginController redireciona para /dashboard após o login: encaminha para /admin.
        [
            'id' => 'dashboard-atalho',
            'value' => '/dashboard',
            'controller' => 'controllers\\DashboardController',
            'call' => 'redirectAdmin',
            'isRegex' => false,
            'method' => 'GET'
        ],
        // Sair da área exclusiva (POST + CSRF)
        [
            'id' => 'admin-sair',
            'value' => '/admin/sair',
            'controller' => 'controllers\\DashboardController',
            'call' => 'logout',
            'isRegex' => false,
            'method' => 'POST'
        ],
    ],

    $adminRecurso('portfolio', 'PortifolioController', ['save' => 'savePortifolio', 'update' => 'updatePortifolio', 'delete' => 'deletePortifolio']),
    $adminRecurso('mensagens', 'MensagemController', ['save' => 'saveMensagem', 'update' => 'updateMensagem', 'delete' => 'deleteMensagem']),
    $adminRecurso('clientes', 'ClienteController', ['save' => 'saveCliente', 'update' => 'updateCliente', 'delete' => 'deleteCliente']),
    $adminRecurso('membros', 'MembroController', ['save' => 'saveMembro', 'update' => 'updateMembro', 'delete' => 'deleteMembro']),

    // Configurações: editar e excluir a PRÓPRIA conta (o membro logado)
    [
        [
            'id' => 'admin-configuracoes',
            'value' => '/admin/configuracoes',
            'controller' => 'controllers\\MembroController',
            'call' => 'makeConfig',
            'isRegex' => false,
            'method' => 'GET'
        ],
        [
            'id' => 'admin-configuracoes-atualizar',
            'value' => '/admin/configuracoes',
            'controller' => 'controllers\\MembroController',
            'call' => 'updateConta',
            'isRegex' => false,
            'method' => 'PUT'
        ],
        [
            'id' => 'admin-configuracoes-excluir',
            'value' => '/admin/configuracoes',
            'controller' => 'controllers\\MembroController',
            'call' => 'deleteConta',
            'isRegex' => false,
            'method' => 'DELETE'
        ],
    ]
);

unset($adminRecurso);
