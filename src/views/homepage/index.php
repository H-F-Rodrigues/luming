<?php
/**
 * Homepage (site público).
 *
 * @var array  $servicos  [slug => ['numero','icone','titulo','texto', ...]]
 * @var array  $projetos  [['titulo','categoria','imagem','url'], ...] (até 4)
 * @var array  $equipe    [['nome','funcao','foto'], ...] (até 4)
 * @var array  $processo  [[numero, titulo, texto], ...]
 * @var array  $motivos   string[]
 * @var array  $depoimento
 * @var array  $faqs      [[pergunta, resposta], ...]
 * @var string $imagemSobre
 */

$secao = static function (string $eyebrow, string $titulo, string $texto = ''): void {
    echo '<div class="section-heading"><span class="eyebrow"><i></i> ' . esc($eyebrow) . '</span>'
        . '<h2>' . esc($titulo) . '</h2>'
        . ($texto !== '' ? '<p>' . esc($texto) . '</p>' : '')
        . '</div>';
};
?>
<main>
    <section id="inicio" class="hero">
        <div class="hero-orb orb-one"></div>
        <div class="hero-orb orb-two"></div>
        <div class="container hero-content">
            <div class="hero-copy">
                <span class="eyebrow"><i></i> tecnologia &amp; criatividade</span>
                <h1>Dê luz<br><em>à sua ideia.</em></h1>
                <p>Tecnologia + criatividade para transformar negócios em experiências que conectam, inspiram e geram movimento.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="/contato">Fale com a LUMING <?= icon('arrow-up-right', 17) ?></a>
                    <a class="button button-ghost" href="#portfolio"><span class="play-icon"><?= icon('play', 11, ['fill' => 'currentColor']) ?></span> Ver portfólio</a>
                </div>
            </div>
            <div class="hero-art" aria-label="Ilustração abstrata de luz dourada">
                <div class="art-ring ring-large"></div>
                <div class="art-ring ring-small"></div>
                <div class="art-star">✦</div>
                <div class="art-line"></div>
                <div class="art-dot dot-a"></div>
                <div class="art-dot dot-b"></div>
                <div class="art-label">IDEIAS<br><strong>EM MOVIMENTO</strong></div>
            </div>
        </div>
        <div class="scroll-note"><span></span> role para explorar</div>
    </section>

    <section id="sobre" class="section about-section">
        <div class="container about-grid">
            <div class="about-image">
                <img src="<?= esc($imagemSobre) ?>" alt="Composição visual da identidade LUMING" loading="lazy">
                <div class="image-tag">LUMING<br><small>tecnologia &amp; criatividade</small></div>
            </div>
            <div class="about-copy">
                <?php $secao('01 — sobre nós', 'Ideias brilhantes pedem espaço para acontecer.', 'Somos um estúdio de tecnologia e criação que transforma estratégia em experiências digitais com personalidade.') ?>
                <p>Acreditamos que o melhor trabalho nasce do encontro entre curiosidade, clareza e coragem para fazer diferente. Por isso, entramos de verdade em cada projeto — para entender o que move sua empresa e traduzir isso em algo que as pessoas sintam.</p>
                <a class="text-link" href="/contato">Conheça nossa história <?= icon('arrow-up-right', 16) ?></a>
            </div>
        </div>
    </section>

    <section id="servicos" class="section services-section">
        <div class="container">
            <?php $secao('02 — o que fazemos', 'Tudo começa com uma boa ideia.', 'E continua com as pessoas certas para colocá-la no mundo.') ?>
            <div class="services-grid">
                <?php foreach ($servicos as $slug => $servico): ?>
                    <?php if ($servico['id'] <= 5): ?>
                        <article class="service-card">
                            <div class="service-top"><span><?= esc($servico['numero']) ?></span><?= icon($servico['icone'], 22, ['stroke-width' => 1.5]) ?></div>
                            <h3><?= esc($servico['titulo']) ?></h3>
                            <p><?= esc($servico['texto']) ?></p>
                            <a href="/servicos/<?= esc($slug) ?>" aria-label="Saiba mais sobre <?= esc($servico['titulo']) ?>"><?= icon('arrow-up-right', 17) ?></a>
                        </article>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <section id="portfolio" class="section portfolio-section">
        <div class="container">
            <div class="section-row">
                <?php $secao('03 — portfólio', 'Projetos em destaque.') ?>
                <a class="text-link desktop-link" href="/portfolio">Ver todos os projetos <?= icon('arrow-up-right', 16) ?></a>
            </div>
            <div class="projects-grid">
                <?php foreach (array_values($projetos) as $i => $projeto): ?>
                    <?php if ($i < 3): ?>
                        <a href="<?= esc($projeto['url']) ?>" class="project-card project-<?= $i + 1 ?>">
                            <img src="<?= esc($projeto['imagem']) ?>" alt="<?= esc($projeto['titulo']) ?>" loading="lazy">
                            <div class="project-overlay">
                                <span><?= esc($projeto['categoria']) ?></span>
                                <h3><?= esc($projeto['titulo']) ?></h3>
                                <?= icon('arrow-up-right', 21) ?>
                            </div>
                        </a>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <section id="equipe" class="section team-section">
        <div class="container">
            <?php $secao('04 — quem faz acontecer', 'Pessoas por trás das ideias.', 'Uma equipe multidisciplinar, curiosa e próxima para transformar cada desafio em movimento.') ?>
            <div class="team-grid">
                <?php foreach ($equipe as $membro): ?>
                    <article class="team-card">
                        <div class="team-photo">
                            <?php if ($membro['foto'] !== ''): ?>
                                <img src="<?= esc($membro['foto']) ?>" alt="Foto de <?= esc($membro['nome']) ?>" loading="lazy">
                            <?php else: ?>
                                <span class="team-initials" aria-hidden="true"><?= esc(mb_strtoupper(mb_substr($membro['nome'], 0, 1))) ?></span>
                            <?php endif ?>
                        </div>
                        <span><?= esc($membro['funcao']) ?></span>
                        <h3><?= esc($membro['nome']) ?></h3>
                    </article>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <section id="processo" class="section process-section">
        <div class="container process-grid">
            <div class="process-intro">
                <?php $secao('04 — nosso processo', 'Do primeiro papo ao próximo nível.', 'Um processo claro, colaborativo e feito para tirar a complexidade do caminho.') ?>
                <div class="process-quote"><?= icon('sparkles', 17) ?><span>Seu negócio<br><strong>no centro.</strong></span></div>
            </div>
            <div class="timeline">
                <?php foreach ($processo as $i => [$numero, $titulo, $texto]): ?>
                    <div class="timeline-item<?= $i === 0 ? ' active' : '' ?>">
                        <div class="timeline-number"><?= esc($numero) ?></div>
                        <div>
                            <h3><?= esc($titulo) ?></h3>
                            <p><?= esc($texto) ?></p>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <section class="section reasons-section">
        <div class="container">
            <?php $secao('05 — por que a LUMING', 'Criatividade com direção.', 'Não entregamos só coisas bonitas. Entregamos soluções que fazem sentido para o seu negócio.') ?>
            <div class="reasons-grid">
                <?php foreach ($motivos as $i => $motivo): ?>
                    <div class="reason">
                        <span>0<?= $i + 1 ?></span>
                        <div><?= icon('lightbulb', 17) ?><h3><?= esc($motivo) ?></h3></div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <section class="section testimonial-section">
        <div class="container testimonial-wrap">
            <?= icon('quote', 36, ['class' => 'quote-mark']) ?>
            <blockquote>“<?= esc($depoimento['texto']) ?>”</blockquote>
            <div class="testimonial-author">
                <div class="author-avatar"><?= esc($depoimento['iniciais']) ?></div>
                <div><strong><?= esc($depoimento['nome']) ?></strong><span><?= esc($depoimento['cargo']) ?></span></div>
            </div>
        </div>
    </section>

    <section id="faq" class="section faq-section">
        <div class="container faq-grid">
            <?php $secao('06 — dúvidas frequentes', 'Vamos deixar tudo claro.', 'Se ainda ficou alguma pergunta, é só chamar. A conversa começa sem compromisso.') ?>
            <div class="faq-list" data-faq>
                <?php foreach ($faqs as $i => [$pergunta, $resposta]): ?>
                    <div class="faq-item<?= $i === 0 ? ' open' : '' ?>">
                        <button type="button" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>"><span><?= esc($pergunta) ?></span><?= icon('chevron-down', 18) ?></button>
                        <p><?= esc($resposta) ?></p>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <section id="contato" class="contact-section">
        <div class="contact-glow"></div>
        <div class="container contact-inner">
            <span class="eyebrow"><i></i> pronto para começar?</span>
            <h2>Sua ideia tem<br><em>muito a iluminar.</em></h2>
            <p>Vamos transformar seu próximo passo em algo extraordinário.</p>
            <a class="button button-primary" href="mailto:oi@luming.com.br">Falar com a LUMING <?= icon('arrow-up-right', 17) ?></a>
        </div>
    </section>
</main>
