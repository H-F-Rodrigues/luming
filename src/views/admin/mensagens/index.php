<?php
/**
 * Listagem de mensagens (GET /admin/mensagens).
 *
 * @var array $mensagens models\Mensagem[]
 */
$mensagens ??= [];
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content" data-filter>
        <div class="adm-section-row">
            <div>
                <span class="adm-eyebrow"><i></i> inbox da equipe</span>
                <h2 class="adm-section-title">Mensagens recebidas.</h2>
            </div>
            <a class="adm-btn adm-btn-primary" href="/admin/mensagens/novo"><?= iconAdmin('plus', 15) ?> Nova mensagem</a>
        </div>

        <?php if (empty($mensagens)): ?>
            <div class="adm-empty"><p>Nenhuma mensagem recebida ainda.</p></div>
        <?php else: ?>
            <div class="adm-toolbar">
                <label class="adm-search">
                    <?= iconAdmin('search', 16) ?>
                    <input type="search" placeholder="Buscar por projeto, cliente ou serviço" aria-label="Buscar mensagem" data-filter-input>
                </label>
                <span><strong data-filter-count><?= count($mensagens) ?></strong> mensagem(ns)</span>
            </div>

            <div class="adm-list">
                <?php foreach ($mensagens as $mensagem): ?>
                    <?php $projeto = adminNome($mensagem->projeto); $remetente = adminNome($mensagem->nomeCliente); ?>
                    <article class="adm-row" data-filter-item data-filter-text="<?= esc($projeto . ' ' . $remetente . ' ' . $mensagem->servico . ' ' . $mensagem->emailCliente . ' ' . $mensagem->descricao) ?>">
                        <span class="adm-row-icon"><?= iconAdmin('message-square', 17) ?></span>
                        <div class="adm-row-info">
                            <span class="adm-pill"><?= esc($mensagem->servico) ?></span>
                            <h3><a href="/admin/mensagens/<?= (int) $mensagem->id ?>"><?= esc($projeto) ?></a></h3>
                            <p><strong><?= esc($remetente) ?></strong> · <?= esc($mensagem->descricao) ?></p>
                        </div>
                        <?= adminAcoes('/admin/mensagens', (int) $mensagem->id, $projeto, 'mensagem') ?>
                    </article>
                <?php endforeach ?>
            </div>
            <div class="adm-empty" data-filter-empty hidden><p>Nenhuma mensagem encontrada para essa busca.</p></div>
        <?php endif ?>
    </div>
</main>
<?php require COMPONENTS . 'confirm.php'; ?>
