<?php
/**
 * Listagem de clientes (GET /admin/clientes).
 *
 * @var array $clientes models\Cliente[]
 */
$clientes ??= [];
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content" data-filter>
        <div class="adm-section-row">
            <div>
                <span class="adm-eyebrow"><i></i> relacionamento</span>
                <h2 class="adm-section-title">Clientes da LUMING.</h2>
            </div>
            <a class="adm-btn adm-btn-primary" href="/admin/clientes/novo"><?= iconAdmin('plus', 15) ?> Novo cliente</a>
        </div>

        <?php if (empty($clientes)): ?>
            <div class="adm-empty"><p>Nenhum cliente cadastrado. <a href="/admin/clientes/novo">Cadastrar o primeiro</a></p></div>
        <?php else: ?>
            <div class="adm-toolbar">
                <label class="adm-search">
                    <?= iconAdmin('search', 16) ?>
                    <input type="search" placeholder="Buscar por nome, e-mail, telefone ou ramo" aria-label="Buscar cliente" data-filter-input>
                </label>
                <span><strong data-filter-count><?= count($clientes) ?></strong> cliente(s)</span>
            </div>

            <div class="adm-list">
                <?php foreach ($clientes as $cliente): ?>
                    <?php $nomeCliente = adminNome($cliente->nome); ?>
                    <article class="adm-row" data-filter-item data-filter-text="<?= esc($nomeCliente . ' ' . $cliente->email . ' ' . $cliente->telefone . ' ' . $cliente->ramo) ?>">
                        <span class="adm-avatar lg"><?= esc(adminIniciais($nomeCliente)) ?></span>
                        <div class="adm-row-info">
                            <span class="adm-pill"><?= esc($cliente->ramo) ?></span>
                            <h3><a href="/admin/clientes/<?= (int) $cliente->id ?>"><?= esc($nomeCliente) ?></a></h3>
                            <p><?= esc($cliente->email) ?> · <?= esc($cliente->telefone) ?></p>
                        </div>
                        <?= adminAcoes('/admin/clientes', (int) $cliente->id, $nomeCliente, 'cliente') ?>
                    </article>
                <?php endforeach ?>
            </div>
            <div class="adm-empty" data-filter-empty hidden><p>Nenhum cliente encontrado para essa busca.</p></div>
        <?php endif ?>
    </div>
</main>
<?php require COMPONENTS . 'confirm.php'; ?>
