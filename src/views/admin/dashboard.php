<?php
/**
 * Dashboard principal (GET /admin).
 *
 * @var string $saudacao           bom dia / boa tarde / boa noite
 * @var string $primeiroNome
 * @var array  $stats              [rótulo, valor, ícone, link][]
 * @var array  $mensagensRecentes  models\Mensagem[]
 * @var array  $projetosRecentes   models\Portifolio[]
 */
$mensagensRecentes ??= [];
$projetosRecentes ??= [];
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <section class="adm-welcome">
            <div>
                <span class="adm-eyebrow"><i></i> <?= esc($saudacao) ?><?= $primeiroNome !== '' ? ', ' . esc(mb_strtolower($primeiroNome, 'UTF-8')) : '' ?></span>
                <h2>Ideias em<br><em>movimento.</em></h2>
                <p>Um espaço para acompanhar o que está acontecendo na LUMING.</p>
            </div>
            <div class="adm-orbit" aria-hidden="true">✦</div>
        </section>

        <div class="adm-stats">
            <?php foreach ($stats as [$rotulo, $valor, $icone, $link]): ?>
                <a class="adm-stat" href="<?= esc($link) ?>">
                    <span class="adm-stat-icon"><?= iconAdmin($icone, 15) ?></span>
                    <p><?= esc($rotulo) ?></p>
                    <strong><?= sprintf('%02d', (int) $valor) ?></strong>
                </a>
            <?php endforeach ?>
        </div>

        <div class="adm-section-row adm-section-gap">
            <div>
                <span class="adm-eyebrow"><i></i> atividade recente</span>
                <h2 class="adm-section-title">Mensagens recentes.</h2>
            </div>
            <a class="adm-link" href="/admin/mensagens">Ver todas <?= iconAdmin('arrow-right', 14) ?></a>
        </div>

        <?php if (empty($mensagensRecentes)): ?>
            <div class="adm-empty"><p>Nenhuma mensagem recebida ainda.</p></div>
        <?php else: ?>
            <div class="adm-list">
                <?php foreach ($mensagensRecentes as $mensagem): ?>
                    <article class="adm-row">
                        <span class="adm-row-icon"><?= iconAdmin('message-square', 17) ?></span>
                        <div class="adm-row-info">
                            <span class="adm-pill"><?= esc($mensagem->servico) ?></span>
                            <h3><a href="/admin/mensagens/<?= (int) $mensagem->id ?>"><?= esc(adminNome($mensagem->projeto)) ?></a></h3>
                            <p><strong><?= esc(adminNome($mensagem->nomeCliente)) ?></strong> · <?= esc($mensagem->descricao) ?></p>
                        </div>
                        <div class="adm-actions">
                            <a class="adm-icon-btn" href="/admin/mensagens/<?= (int) $mensagem->id ?>" aria-label="Visualizar mensagem" title="Visualizar"><?= iconAdmin('eye', 15) ?></a>
                        </div>
                    </article>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <div class="adm-section-row adm-section-gap">
            <div>
                <span class="adm-eyebrow"><i></i> portfólio</span>
                <h2 class="adm-section-title">Projetos recentes.</h2>
            </div>
            <a class="adm-link" href="/admin/portfolio">Ver todos <?= iconAdmin('arrow-right', 14) ?></a>
        </div>

        <?php if (empty($projetosRecentes)): ?>
            <div class="adm-empty"><p>Nenhum projeto cadastrado. <a href="/admin/portfolio/novo">Cadastrar o primeiro</a></p></div>
        <?php else: ?>
            <div class="adm-mini-grid">
                <?php foreach ($projetosRecentes as $projeto): ?>
                    <?php $capa = adminCapaUrl($projeto); ?>
                    <a class="adm-mini" href="/admin/portfolio/<?= (int) $projeto->id ?>">
                        <div class="adm-mini-image"><?php if ($capa !== ''): ?><img src="<?= esc($capa) ?>" alt="" loading="lazy"><?php endif ?></div>
                        <div>
                            <span class="adm-pill"><?= esc(adminData($projeto->criadoEm, 'Y')) ?></span>
                            <h3><?= esc(adminNome($projeto->projeto)) ?></h3>
                        </div>
                    </a>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <div class="adm-section-row adm-section-gap">
            <div>
                <span class="adm-eyebrow"><i></i> atalhos</span>
                <h2 class="adm-section-title">Central da equipe.</h2>
            </div>
        </div>
        <div class="adm-quick">
            <a href="/admin/portfolio/novo"><?= iconAdmin('folder-kanban', 19) ?><strong>Novo projeto</strong><span>Publicar no portfólio</span></a>
            <a href="/admin/clientes/novo"><?= iconAdmin('users', 19) ?><strong>Novo cliente</strong><span>Cadastrar relacionamento</span></a>
            <a href="/admin/mensagens/novo"><?= iconAdmin('message-square', 19) ?><strong>Nova mensagem</strong><span>Registrar um contato</span></a>
            <a href="/admin/membros/novo"><?= iconAdmin('user', 19) ?><strong>Novo membro</strong><span>Adicionar à equipe</span></a>
        </div>
    </div>
</main>
