<?php
/**
 * Formulário de criação de mensagem (GET /admin/mensagens/novo).
 * Envia POST /admin/mensagens (MensagemController::saveMensagem).
 *
 * @var array $servicos models\Servico[]
 * @var array $ramos    models\Ramo[]
 * @var array $valores
 * @var array $erros    string[]
 */
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <div class="adm-section-row">
            <a class="adm-back" href="/admin/mensagens">← voltar para mensagens</a>
        </div>
        <?php
        $formAcao = '/admin/mensagens';
        $formEdicao = false;
        require VIEWS . 'admin/mensagens/_form.php';
        ?>
    </div>
</main>
