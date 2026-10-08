<?php
/**
 * Formulário de criação de cliente (GET /admin/clientes/novo).
 * Envia POST /admin/clientes (ClienteController::saveCliente).
 *
 * @var array $ramos   models\Ramo[]
 * @var array $valores campos preenchidos (vazios, ou o que foi enviado em caso de erro)
 * @var array $erros   string[]
 */
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <div class="adm-section-row">
            <a class="adm-back" href="/admin/clientes">← voltar para clientes</a>
        </div>
        <?php
        $formAcao = '/admin/clientes';
        $formEdicao = false;
        require VIEWS . 'admin/clientes/_form.php';
        ?>
    </div>
</main>
