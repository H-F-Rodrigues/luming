<?php
/**
 * Listagem de membros da equipe (GET /admin/membros).
 *
 * @var array $membros models\Membro[]
 */
$membros ??= [];
$idLogado = (int) ($_SESSION['membro'] ?? 0);
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content" data-filter>
        <div class="adm-section-row">
            <div>
                <span class="adm-eyebrow"><i></i> pessoas</span>
                <h2 class="adm-section-title">Quem faz acontecer.</h2>
            </div>
            <a class="adm-btn adm-btn-primary" href="/admin/membros/novo"><?= iconAdmin('plus', 15) ?> Novo membro</a>
        </div>

        <?php if (empty($membros)): ?>
            <div class="adm-empty"><p>Nenhum membro cadastrado. <a href="/admin/membros/novo">Cadastrar o primeiro</a></p></div>
        <?php else: ?>
            <div class="adm-toolbar">
                <label class="adm-search">
                    <?= iconAdmin('search', 16) ?>
                    <input type="search" placeholder="Buscar por nome, e-mail ou função" aria-label="Buscar membro" data-filter-input>
                </label>
                <span><strong data-filter-count><?= count($membros) ?></strong> membro(s)</span>
            </div>

            <div class="adm-list">
                <?php foreach ($membros as $membro): ?>
                    <?php
                    $nomeMembro = adminNome($membro->nome);
                    $foto = adminFotoUrl($membro);
                    $ehVoce = (int) $membro->id === $idLogado;
                    ?>
                    <article class="adm-row" data-filter-item data-filter-text="<?= esc($nomeMembro . ' ' . $membro->email . ' ' . $membro->funcao) ?>">
                        <?php if ($foto !== ''): ?>
                            <img class="adm-avatar lg adm-avatar-photo" src="<?= esc($foto) ?>" alt="" loading="lazy">
                        <?php else: ?>
                            <span class="adm-avatar lg purple"><?= esc(adminIniciais($nomeMembro)) ?></span>
                        <?php endif ?>
                        <div class="adm-row-info">
                            <span class="adm-pill<?= $ehVoce ? ' ok' : '' ?>"><?= esc($membro->funcao) ?><?= $ehVoce ? ' · você' : '' ?></span>
                            <h3><a href="/admin/membros/<?= (int) $membro->id ?>"><?= esc($nomeMembro) ?></a></h3>
                            <p><?= esc($membro->email) ?></p>
                        </div>
                        <?php if ($ehVoce): ?>
                            <?= adminAcoes('/admin/membros', (int) $membro->id, $nomeMembro, 'membro') ?>
                        <?php else: ?>
                            <?php // Só é possível editar/excluir a própria conta: nos demais, apenas visualizar. ?>
                            <div class="adm-actions">
                                <a class="adm-icon-btn" href="/admin/membros/<?= (int) $membro->id ?>" aria-label="Visualizar membro <?= esc($nomeMembro) ?>" title="Visualizar"><?= iconAdmin('eye', 15) ?></a>
                            </div>
                        <?php endif ?>
                    </article>
                <?php endforeach ?>
            </div>
            <div class="adm-empty" data-filter-empty hidden><p>Nenhum membro encontrado para essa busca.</p></div>
        <?php endif ?>
    </div>
</main>
<?php require COMPONENTS . 'confirm.php'; ?>
