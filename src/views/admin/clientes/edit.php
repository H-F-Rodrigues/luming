<?php
/**
 * Formulário de edição de cliente (GET /admin/clientes/{id}/editar).
 * Envia PUT /admin/clientes/{id} (ClienteController::updateCliente).
 *
 * @var models\Cliente $cliente
 * @var array          $ramos   models\Ramo[]
 * @var array          $valores campos preenchidos com os dados do cliente
 * @var array          $erros   string[]
 */
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <div class="adm-section-row">
            <a class="adm-back" href="/admin/clientes/<?= (int) $cliente->id ?>">← voltar para o cliente</a>
        </div>
        <?php
        $formAcao = '/admin/clientes/' . (int) $cliente->id;
        $formEdicao = true;
        require VIEWS . 'admin/clientes/_form.php';
        ?>
    </div>
</main>
