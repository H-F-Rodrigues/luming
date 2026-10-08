<?php
/**
 * Formulário de edição de mensagem (GET /admin/mensagens/{id}/editar).
 * Envia PUT /admin/mensagens/{id} (MensagemController::updateMensagem).
 *
 * @var models\Mensagem $mensagem
 * @var models\Cliente  $cliente
 * @var array           $servicos models\Servico[]
 * @var array           $ramos    models\Ramo[]
 * @var array           $valores  preenchidos com os dados atuais
 * @var array           $erros    string[]
 */
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <div class="adm-section-row">
            <a class="adm-back" href="/admin/mensagens/<?= (int) $mensagem->id ?>">← voltar para a mensagem</a>
        </div>
        <?php
        $formAcao = '/admin/mensagens/' . (int) $mensagem->id;
        $formEdicao = true;
        require VIEWS . 'admin/mensagens/_form.php';
        ?>
    </div>
</main>
