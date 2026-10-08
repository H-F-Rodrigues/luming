<?php
/**
 * Formulário de criação de membro (GET /admin/membros/novo).
 * Envia POST /admin/membros (MembroController::saveMembro).
 *
 * @var array $funcoes models\Funcao[]
 * @var array $valores
 * @var array $erros   string[]
 */
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <div class="adm-section-row">
            <a class="adm-back" href="/admin/membros">← voltar para a equipe</a>
        </div>
        <?php
        $formAcao = '/admin/membros';
        $formEdicao = false;
        require VIEWS . 'admin/membros/_form.php';
        ?>
    </div>
</main>
