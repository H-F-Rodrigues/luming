<?php
/**
 * Formulário de criação de projeto (GET /admin/portfolio/novo).
 * Envia POST /admin/portfolio (PortifolioController::savePortifolio).
 *
 * @var array $valores
 * @var array $erros   string[]
 */
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <div class="adm-section-row">
            <a class="adm-back" href="/admin/portfolio">← voltar para o portfólio</a>
        </div>
        <?php
        $formAcao = '/admin/portfolio';
        $formEdicao = false;
        require VIEWS . 'admin/portfolio/_form.php';
        ?>
    </div>
</main>
