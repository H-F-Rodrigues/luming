<?php
/**
 * Formulário de edição de projeto (GET /admin/portfolio/{id}/editar).
 * Envia PUT /admin/portfolio/{id} (PortifolioController::updatePortifolio).
 *
 * @var models\Portifolio $portifolio
 * @var int               $totalMidias quantidade de mídias atuais na galeria
 * @var array             $valores     preenchidos com os dados atuais
 * @var array             $erros       string[]
 */
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <div class="adm-section-row">
            <a class="adm-back" href="/admin/portfolio/<?= (int) $portifolio->id ?>">← voltar para o projeto</a>
        </div>
        <?php
        $formAcao = '/admin/portfolio/' . (int) $portifolio->id;
        $formEdicao = true;
        require VIEWS . 'admin/portfolio/_form.php';
        ?>
    </div>
</main>
