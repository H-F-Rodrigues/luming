<?php
/**
 * Página de um serviço.
 *
 * @var array $servico ['numero','icone','titulo','lead','descricao','projetos','membros']
 *                     projetos: [['nome','categoria','imagem','url'], ...]  (tb_portifolio_servico)
 *                     membros:  [['nome','funcao','imagem'], ...]          (tb_funcao_servico)
 */
?>
<main class="service-page">
    <div class="container">
        <a class="back-link" href="/#servicos"><?= icon('arrow-left', 15) ?> Voltar para serviços</a>

        <header class="service-hero">
            <div>
                <span class="eyebrow"><i></i> <?= esc($servico['numero']) ?> — área de atuação</span>
                <h1><?= esc($servico['titulo']) ?></h1>
                <p class="service-lead"><?= esc($servico['lead']) ?></p>
            </div>
            <div class="service-icon"><?= icon($servico['icone'], 54, ['stroke-width' => 1]) ?></div>
        </header>

        <div class="service-description">
            <span class="eyebrow"><i></i> como fazemos</span>
            <p><?= esc($servico['descricao']) ?></p>
            <div class="service-points">
                <span><?= icon('check', 15) ?> Estratégia antes da execução</span>
                <span><?= icon('check', 15) ?> Processo próximo e transparente</span>
                <span><?= icon('check', 15) ?> Resultado com personalidade</span>
            </div>
        </div>

        <section class="service-block">
            <div class="section-row">
                <div>
                    <span class="eyebrow"><i></i> projetos da área</span>
                    <h2 class="service-section-title">Trabalhos que colocam<br><em>ideias em movimento.</em></h2>
                </div>
                <a class="text-link" href="/portfolio">Ver portfólio <?= icon('arrow-up-right', 16) ?></a>
            </div>
            <?php if (empty($servico['projetos'])): ?>
                <p class="portfolio-empty">Em breve novos projetos nesta área.</p>
            <?php else: ?>
                <div class="service-projects">
                    <?php foreach ($servico['projetos'] as $projeto): ?>
                        <a class="service-project" href="<?= esc($projeto['url']) ?>">
                            <img src="<?= esc($projeto['imagem']) ?>" alt="<?= esc($projeto['nome']) ?>" loading="lazy">
                            <div><span><?= esc($projeto['categoria']) ?></span><h3><?= esc($projeto['nome']) ?></h3></div>
                        </a>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </section>

        <section class="service-block">
            <span class="eyebrow"><i></i> pessoas da área</span>
            <h2 class="service-section-title">Quem faz <em>acontecer.</em></h2>
            <?php if (empty($servico['membros'])): ?>
                <p class="portfolio-empty">Nossa equipe para esta área será apresentada em breve.</p>
            <?php else: ?>
                <div class="service-members">
                    <?php foreach ($servico['membros'] as $membro): ?>
                        <article class="service-member">
                            <?php if ($membro['imagem'] !== ''): ?>
                                <img src="<?= esc($membro['imagem']) ?>" alt="Foto de <?= esc($membro['nome']) ?>" loading="lazy">
                            <?php else: ?>
                                <span class="member-initials" aria-hidden="true"><?= esc(mb_strtoupper(mb_substr($membro['nome'], 0, 1))) ?></span>
                            <?php endif ?>
                            <div><h3><?= esc($membro['nome']) ?></h3><p><?= esc($membro['funcao']) ?></p></div>
                        </article>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </section>

        <div class="service-cta">
            <div>
                <span class="eyebrow"><i></i> vamos criar juntos?</span>
                <h2>Tem um desafio<br><em>para a gente?</em></h2>
            </div>
            <a class="button button-primary" href="/contato">Falar com a LUMING <?= icon('arrow-up-right', 17) ?></a>
        </div>
    </div>
</main>
