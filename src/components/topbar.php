<?php
/**
 * Topbar do DASHBOARD: botão do menu (mobile), breadcrumb, título da página,
 * usuário logado e as mensagens de sucesso/erro (toasts).
 *
 * Incluída no começo do <main class="adm-main"> de cada view do painel.
 *
 * @var string $title Título da página (também usado no <title>)
 * @var string $secao Seção do painel, usada no breadcrumb (opcional; padrão: $title)
 *
 * Obs.: roda no escopo da view; por isso as variáveis usam o prefixo $tb.
 */
$tbUsuario = adminUser();
$tbFlash = pullFlash();
$tbFoto = $tbUsuario ? adminFotoUrl($tbUsuario) : '';
?>
<header class="adm-topbar">
    <div class="adm-topbar-main">
        <button type="button" class="adm-menu-button" data-sidebar-open aria-label="Abrir menu" aria-controls="adm-sidebar" aria-expanded="false"><?= iconAdmin('menu', 22) ?></button>
        <div>
            <span class="adm-breadcrumb">ÁREA EXCLUSIVA <span>/</span> <?= esc(mb_strtoupper((string) ($secao ?? $title), 'UTF-8')) ?></span>
            <h1><?= esc($title) ?></h1>
        </div>
    </div>

    <?php if ($tbUsuario): ?>
        <div class="adm-user">
            <?php if ($tbFoto !== ''): ?>
                <img class="adm-avatar adm-avatar-photo" src="<?= esc($tbFoto) ?>" alt="">
            <?php else: ?>
                <span class="adm-avatar" aria-hidden="true"><?= esc(adminIniciais($tbUsuario->nome)) ?></span>
            <?php endif ?>
            <div class="adm-user-meta">
                <strong><?= esc(adminNome($tbUsuario->nome)) ?></strong>
                <span><?= esc($tbUsuario->funcao) ?></span>
            </div>
        </div>
    <?php endif ?>
</header>

<?php if (!empty($tbFlash)): ?>
    <div class="adm-toasts" role="status" aria-live="polite">
        <?php foreach ($tbFlash as $tbMensagem): ?>
            <div class="adm-toast<?= ($tbMensagem['tipo'] ?? '') === 'error' ? ' error' : '' ?>" data-toast>
                <?= iconAdmin(($tbMensagem['tipo'] ?? '') === 'error' ? 'alert-circle' : 'check-circle', 16) ?>
                <span><?= esc($tbMensagem['mensagem'] ?? '') ?></span>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>
