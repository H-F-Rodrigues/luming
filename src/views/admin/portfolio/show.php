<?php
/**
 * Detalhe de um projeto do portfólio.
 *
 * @var array $projeto ['id','titulo','categoria','imagem','intro','descricao','resultados','midias','url','github']
 *                     resultados: [[rotulo, texto], ...]
 *                     midias: [['tipo' => 'image'|'video', 'src', 'alt'], ...]
 */
?>
<main class="project-page">
    <div class="container">
        <a class="back-link" href="/portfolio">← voltar para o portfólio</a>

        <header class="project-heading">
            <span class="eyebrow"><i></i> <?= esc($projeto['categoria']) ?></span>
            <h1><?= esc($projeto['titulo']) ?></h1>
        </header>

        <div class="project-hero">
            <p class="inner-lead"><?= esc($projeto['intro']) ?></p>
            <div class="project-cover"><img src="<?= esc($projeto['imagem']) ?>" alt="Capa do projeto <?= esc($projeto['titulo']) ?>"></div>
        </div>

        <div class="project-gallery">
            <span class="eyebrow"><i></i> galeria do projeto</span>
            <section class="project-carousel" aria-label="Galeria do projeto <?= esc($projeto['titulo']) ?>" data-carousel>
                <div class="carousel-stage" data-carousel-stage></div>
                <div class="carousel-thumbnails" role="tablist" aria-label="Selecionar mídia">
                    <?php foreach ($projeto['midias'] as $i => $midia): ?>
                        <button type="button" role="tab" class="<?= $i === 0 ? 'active' : '' ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="Ver mídia <?= $i + 1 ?>"
                            data-tipo="<?= esc($midia['tipo']) ?>" data-src="<?= esc($midia['src']) ?>" data-alt="<?= esc($midia['alt']) ?>">
                            <?php if ($midia['tipo'] === 'video'): ?>
                                <video src="<?= esc($midia['src']) ?>#t=0.5" preload="metadata" muted playsinline></video>
                                <?= icon('play', 12) ?>
                            <?php else: ?>
                                <img src="<?= esc($midia['src']) ?>" alt="" loading="lazy">
                            <?php endif ?>
                        </button>
                    <?php endforeach ?>
                </div>
            </section>
        </div>

        <div class="project-body">
            <div>
                <span class="eyebrow"><i></i> sobre o projeto</span>
                <h2>Ideia, intenção<br><em>e impacto.</em></h2>
            </div>
            <div>
                <p><?= nl2br(esc($projeto['descricao'])) ?></p>
                <div class="result-list">
                    <?php foreach ($projeto['resultados'] as $i => [$rotulo, $texto]): ?>
                        <div><strong>0<?= $i + 1 ?></strong><span><b><?= esc($rotulo) ?>:</b> <?= esc($texto) ?></span></div>
                    <?php endforeach ?>
                </div>
                <a class="button button-primary" href="/contato">Quero um projeto assim <?= icon('arrow-up-right', 17) ?></a>
                <?php if ($projeto['url'] !== '' || $projeto['github'] !== ''): ?>
                    <div class="project-links">
                        <?php if ($projeto['url'] !== ''): ?>
                            <a class="text-link" href="<?= esc($projeto['url']) ?>" target="_blank" rel="noopener noreferrer">Ver projeto online <?= icon('arrow-up-right', 14) ?></a>
                        <?php endif ?>
                        <?php if ($projeto['github'] !== ''): ?>
                            <a class="text-link" href="<?= esc($projeto['github']) ?>" target="_blank" rel="noopener noreferrer">Ver no GitHub <?= icon('arrow-up-right', 14) ?></a>
                        <?php endif ?>
                    </div>
                <?php endif ?>
            </div>
        </div>

        <div class="project-footer">
            <?= icon('lightbulb', 20) ?>
            <span>Tem uma ideia parecida?</span>
            <a href="/contato">Fale com a LUMING <?= icon('arrow-up-right', 16) ?></a>
        </div>
    </div>
</main>