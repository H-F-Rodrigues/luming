<?php
/**
 * Listagem do portfólio (GET /admin/portfolio).
 *
 * @var array $projetos models\Portifolio[]
 */
$projetos ??= [];
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content" data-filter>
        <div class="adm-section-row">
            <div>
                <span class="adm-eyebrow"><i></i> portfólio</span>
                <h2 class="adm-section-title">Projetos da LUMING.</h2>
            </div>
            <a class="adm-btn adm-btn-primary" href="/admin/portfolio/novo"><?= iconAdmin('plus', 15) ?> Novo projeto</a>
        </div>

        <?php if (empty($projetos)): ?>
            <div class="adm-empty"><p>Nenhum projeto cadastrado. <a href="/admin/portfolio/novo">Cadastrar o primeiro</a></p></div>
        <?php else: ?>
            <div class="adm-toolbar">
                <label class="adm-search">
                    <?= iconAdmin('search', 16) ?>
                    <input type="search" placeholder="Buscar projeto" aria-label="Buscar projeto" data-filter-input>
                </label>
                <span><strong data-filter-count><?= count($projetos) ?></strong> projeto(s)</span>
            </div>

            <div class="adm-list">
                <?php foreach ($projetos as $projeto): ?>
                    <?php $nomeProjeto = adminNome($projeto->projeto); $capa = adminCapaUrl($projeto); ?>
                    <article class="adm-row" data-filter-item data-filter-text="<?= esc($nomeProjeto . ' ' . $projeto->sobre) ?>">
                        <div class="adm-row-thumb<?= $capa === '' ? ' empty' : '' ?>">
                            <?php if ($capa !== ''): ?>
                                <img src="<?= esc($capa) ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <?= iconAdmin('image', 20) ?>
                            <?php endif ?>
                        </div>
                        <div class="adm-row-info">
                            <?php if ($capa !== ''): ?>
                                <span class="adm-pill ok">Publicado · <?= esc(adminData($projeto->criadoEm, 'Y')) ?></span>
                            <?php else: ?>
                                <span class="adm-pill warn">Sem capa · oculto no site</span>
                            <?php endif ?>
                            <h3><a href="/admin/portfolio/<?= (int) $projeto->id ?>"><?= esc($nomeProjeto) ?></a></h3>
                            <p><?= esc($projeto->sobre !== '' ? $projeto->sobre : 'Sem descrição.') ?></p>
                        </div>
                        <?= adminAcoes('/admin/portfolio', (int) $projeto->id, $nomeProjeto, 'projeto') ?>
                    </article>
                <?php endforeach ?>
            </div>
            <div class="adm-empty" data-filter-empty hidden><p>Nenhum projeto encontrado para essa busca.</p></div>
        <?php endif ?>
    </div>
</main>
<?php require COMPONENTS . 'confirm.php'; ?>
