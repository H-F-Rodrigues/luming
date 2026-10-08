<?php
/**
 * Navbar do SITE PÚBLICO (fixa no topo, com menu mobile).
 * Independente do header: o dashboard usa uma sidebar própria.
 *
 * Os links usam "/#secao" para funcionarem em qualquer página do site.
 */
$links = [
    ['Início',    '/#inicio'],
    ['Sobre',     '/#sobre'],
    ['Serviços',  '/#servicos'],
    ['Portfólio', '/#portfolio'],
    ['Equipe',    '/#equipe'],
    ['Processo',  '/#processo'],
    ['FAQ',       '/#faq'],
];
?>
<header class="navbar" data-navbar>
    <div class="container nav-inner">
        <a href="/#inicio" class="logo" aria-label="LUMING início"><span class="logo-mark"><?= icon('lightbulb', 15) ?></span> LUMING</a>
        <nav class="nav-links" id="menu-principal" aria-label="Navegação principal">
            <?php foreach ($links as [$rotulo, $href]): ?>
                <a href="<?= esc($href) ?>"><?= esc($rotulo) ?></a>
            <?php endforeach ?>
            <a class="nav-cta" href="/contato">Fale conosco <?= icon('arrow-up-right', 15) ?></a>
        </nav>
        <button class="menu-button" type="button" aria-label="Abrir menu" aria-controls="menu-principal" aria-expanded="false" data-menu-toggle>
            <span data-icon-open><?= icon('menu', 24) ?></span>
            <span data-icon-close hidden><?= icon('x', 24) ?></span>
        </button>
    </div>
</header>
