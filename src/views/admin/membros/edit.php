<?php
/**
 * Formulário de edição de membro (GET /admin/membros/{id}/editar).
 * Envia PUT /admin/membros/{id} (MembroController::updateMembro).
 *
 * @var models\Membro $membro
 * @var array         $funcoes models\Funcao[]
 * @var array         $valores preenchidos com os dados atuais
 * @var array         $erros   string[]
 */
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <div class="adm-section-row">
            <a class="adm-back" href="/admin/membros/<?= (int) $membro->id ?>">← voltar para o membro</a>
        </div>
        <?php
        $formAcao = '/admin/membros/' . (int) $membro->id;
        $formEdicao = true;
        require VIEWS . 'admin/membros/_form.php';
        ?>
    </div>
</main>
