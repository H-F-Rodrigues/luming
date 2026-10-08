<?php

/**
 * Chamada pelo router quando nenhuma rota corresponde à URL.
 */
function makeNotFound(): void {
    http_response_code(404);

    makePage('erro', [
        'title' => 'Página não encontrada',
        'erros' => [],
        'msg'   => 'A página que você procura não existe ou foi movida.',
        'code'  => 404,
    ]);
}
