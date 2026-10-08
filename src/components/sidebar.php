<?php
/**
 * Sidebar do DASHBOARD (substitui a navbar do site público).
 * Incluída por components/header.php quando o layout é 'dashboard'.
 *
 * Em telas pequenas ela vira um menu recolhível: o botão de abrir fica na topbar
 * (components/topbar.php) e o comportamento está em assets/js/dashboard/dashboard.js.
 *
 * Obs.: este arquivo roda no mesmo escopo da view; por isso as variáveis usam o prefixo $sb.
 */
$sbUsuario = adminUser();
$sbCaminho = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/') ?: '/';

$sbMenu = [
    ['Visão geral', '/admin',           'layout-dashboard'],
    ['Portfólio',   '/admin/portfolio', 'folder-kanban'],
    ['Mensagens',   '/admin/mensagens', 'message-square'],
    ['Clientes',    '/admin/clientes',  'users'],
    ['Equipe',      '/admin/membros',   'user'],
    ['Configurações', '/admin/configuracoes', 'settings'],
];

// "Visão geral" só fica ativa em /admin; os demais, em /admin/recurso e abaixo dele.
$sbAtivo = static function (string $href) use ($sbCaminho): bool {
    if ($href === '/admin') {
        return $sbCaminho === '/admin';
    }

    return $sbCaminho === $href || str_starts_with($sbCaminho, $href . '/');
};
?>
<button type="button" class="adm-overlay" data-sidebar-close aria-label="Fechar menu" hidden></button>
<aside class="adm-sidebar" id="adm-sidebar" aria-label="Menu do painel">
    <a href="/admin" class="adm-logo" aria-label="LUMING — visão geral"><span class="adm-brand-mark">✦</span>LUMING</a>
    <span class="adm-label">ESPAÇO INTERNO</span>

    <nav class="adm-nav" aria-label="Seções do painel">
        <?php foreach ($sbMenu as [$sbRotulo, $sbHref, $sbIcone]): ?>
            <?php $sbItemAtivo = $sbAtivo($sbHref); ?>
            <a href="<?= esc($sbHref) ?>" class="adm-nav-link<?= $sbItemAtivo ? ' active' : '' ?>"<?= $sbItemAtivo ? ' aria-current="page"' : '' ?>>
                <?= iconAdmin($sbIcone, 16) ?><?= esc($sbRotulo) ?>
            </a>
        <?php endforeach ?>
    </nav>

    <div class="adm-sidebar-bottom">
        <span class="adm-sidebar-note"><i class="adm-online-dot"></i> Sessão ativa<?= $sbUsuario ? ' · ' . esc(strtok(adminNome($sbUsuario->nome), ' ')) : '' ?></span>
        <a class="adm-nav-link" href="/" target="_blank" rel="noopener"><?= iconAdmin('external-link', 16) ?>Ver site</a>
        <form action="/admin/sair" method="post">
            <?= createCsrf() ?>
            <button type="submit" class="adm-nav-link"><?= iconAdmin('log-out', 16) ?>Sair da área</button>
        </form>
    </div>
</aside>
