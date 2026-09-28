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
    // Game
    [
        'id' => 'join',
        'value' => '/game/join',
        'controller' => 'controllers\\PartidaController',
        'call' => 'joinMatch',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'left',
        'value' => '/game/left',
        'controller' => 'controllers\\PartidaController',
        'call' => 'leftMatch',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'start',
        'value' => '/game/start',
        'controller' => 'controllers\\JogadorController',
        'call' => 'makeStart',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'start_post',
        'value' => '/game/save',
        'controller' => 'controllers\\JogadorController',
        'call' => 'saveJogador',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'game',
        'value' => '/game',
        'controller' => 'controllers\\PartidaController',
        'call' => 'makeGame',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'game_wait',
        'value' => '/game/wait',
        'controller' => 'controllers\\PartidaController',
        'call' => 'makeWait',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'game_question',
        'value' => '/game/question',
        'controller' => 'controllers\\PartidaController',
        'call' => 'makeQuestion',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'game_answer',
        'value' => '/game/question',
        'controller' => 'controllers\\PartidaController',
        'call' => 'saveAnswer',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'game_show_answer',
        'value' => '/game/answer',
        'controller' => 'controllers\\PartidaController',
        'call' => 'makeAnswer',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'game_rank',
        'value' => '/game/rank',
        'controller' => 'controllers\\PartidaController',
        'call' => 'makeRank',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id'       => 'api_status',
        'value'    => '/api/partida/status',
        'controller' => 'controllers\\PartidaController',
        'call'     => 'getStatus',
        'isRegex'  => false,
        'method'   => 'GET'
    ],
    // HOST
    [
        'id' => 'host_cadastro',
        'value' => '/host/cadastro',
        'controller' => 'controllers\\HostController',
        'call' => 'makeCadastro',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'host_cadastrar',
        'value' => '/host/cadastro',
        'controller' => 'controllers\\HostController',
        'call' => 'saveCadastro',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'host_login',
        'value' => '/host/login',
        'controller' => 'controllers\\HostController',
        'call' => 'makeLogin',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'host_logar',
        'value' => '/host/login',
        'controller' => 'controllers\\HostController',
        'call' => 'login',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'host_central',
        'value' => '/host/central',
        'controller' => 'controllers\\HostController',
        'call' => 'makeCentral',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'host_logout',
        'value' => '/host/logout',
        'controller' => 'controllers\\HostController',
        'call' => 'logout',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'host',
        'value' => '/host',
        'controller' => 'controllers\\HostController',
        'call' => 'makeGame',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'host_post',
        'value' => '/host',
        'controller' => 'controllers\\HostController',
        'call' => 'makeGame',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'host_return',
        'value' => '/host/return',
        'controller' => 'controllers\\HostController',
        'call' => 'return',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'host_remove',
        'value' => '/host/remove',
        'controller' => 'controllers\\HostController',
        'call' => 'removeMatch',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'host_create',
        'value' => '/host/create',
        'controller' => 'controllers\\HostController',
        'call' => 'makeCreate',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'host_save',
        'value' => '/host/create',
        'controller' => 'controllers\\HostController',
        'call' => 'saveMatch',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'host_hub',
        'value' => '/host/hub',
        'controller' => 'controllers\\HostController',
        'call' => 'makeHub',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'host_add',
        'value' => '/host/add',
        'controller' => 'controllers\\HostController',
        'call' => 'saveQuestion',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'host_question_remove',
        'value' => '/host/question/remove',
        'controller' => 'controllers\\HostController',
        'call' => 'removeQuestion',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'host_start',
        'value' => '/host/start',
        'controller' => 'controllers\\HostController',
        'call' => 'startQuiz',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'host_quiz',
        'value' => '/host/quiz',
        'controller' => 'controllers\\HostController',
        'call' => 'makeQuiz',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'host_next',
        'value' => '/host/quiz',
        'controller' => 'controllers\\HostController',
        'call' => 'nextQuestion',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'host_rank',
        'value' => '/host/rank',
        'controller' => 'controllers\\HostController',
        'call' => 'updateRank',
        'isRegex' => false,
        'method' => 'POST'
    ],
    [
        'id' => 'host_ranking',
        'value' => '/host/rank',
        'controller' => 'controllers\\HostController',
        'call' => 'makeRank',
        'isRegex' => false,
        'method' => 'GET'
    ],
    [
        'id' => 'host_ranking',
        'value' => '/host/finish',
        'controller' => 'controllers\\HostController',
        'call' => 'finishMatch',
        'isRegex' => false,
        'method' => 'POST'
    ]
];
