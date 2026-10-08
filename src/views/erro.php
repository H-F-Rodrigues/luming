<?php
/**
 * Página de erro (usada pelo router em exceções e por makeNotFound()).
 *
 * @var string $msg
 * @var int    $code
 */
$code ??= 500;
$msg ??= 'Algo deu errado.';
?>
<main class="inner-page error-page">
    <div class="container">
        <span class="error-code"><?= esc($code) ?></span>
        <h1><?= esc($code === 404 ? 'Página não encontrada' : 'Ops, algo deu errado') ?></h1>
        <p><?= esc($msg) ?></p>
        <a class="button button-primary" href="/">Voltar para o início <?= icon('arrow-up-right', 17) ?></a>
    </div>
</main>
