<?php
/**
 * Portfólio completo (listagem).
 *
 * @var array $projetos [['id','titulo','categoria','ano','imagem','url'], ...]
 * @var array $anos     string[] (para o filtro)
 */
?>
<main class="portfolio-page">
    <div class="container">
        <a class="back-link" href="/"><?= icon('arrow-left', 15) ?> Voltar para início</a>
        <span class="eyebrow"><i></i> portfólio completo</span>
        <h1>Projetos que<br><em>geram movimento.</em></h1>
        <p class="inner-lead">Uma seleção de trabalhos criados em parceria com marcas que escolheram transformar boas ideias em experiências memoráveis.</p>

        <?php if (count($anos) > 1): ?>
            <div class="portfolio-filters" data-portfolio-filters>
                <span><?= icon('filter', 14) ?> filtrar por</span>
                <button type="button" class="active" data-filtro="todos">Todos</button>
                <?php foreach ($anos as $ano): ?>
                    <button type="button" data-filtro="<?= esc($ano) ?>"><?= esc($ano) ?></button>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <div class="portfolio-grid">
            <?php foreach ($projetos as $projeto): ?>
                <a class="portfolio-card" href="<?= esc($projeto['url']) ?>" data-ano="<?= esc($projeto['ano']) ?>">
                    <div class="portfolio-image">
                        <img src="<?= esc($projeto['imagem']) ?>" alt="<?= esc($projeto['titulo']) ?>" loading="lazy">
                        <span><?= esc($projeto['ano']) ?></span>
                    </div>
                    <div class="portfolio-card-info">
                        <div>
                            <span><?= esc($projeto['categoria']) ?></span>
                            <h2><?= esc($projeto['titulo']) ?></h2>
                        </div>
                        <?= icon('arrow-up-right', 20) ?>
                    </div>
                </a>
            <?php endforeach ?>
        </div>
    </div>
</main>
