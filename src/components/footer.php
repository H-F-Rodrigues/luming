<?php
/**
 * Rodapé do documento.
 *
 * @var string $layout 'site' (padrão) ou 'dashboard'
 * @var bool   $footer exibe o rodapé institucional do site público
 * @var array  $scripts JS extras (relativos a /assets)
 */

$layout ??= 'site';
$footer ??= false;
$scripts ??= [];

$jsDoLayout = [
    'site'      => ['js/site/site.js'],
    // Reservado para a Etapa 2 (só é carregado se o arquivo existir).
    'dashboard' => ['js/dashboard/dashboard.js'],
];

$jsDoLayout = array_filter(
    $jsDoLayout[$layout] ?? $jsDoLayout['site'],
    static fn (string $arquivo): bool => $layout !== 'dashboard'
        || is_file(BASE_PATH . DIRECTORY_SEPARATOR . PUB . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $arquivo)
);
?>
<?php if ($layout === 'site' && $footer): ?>
    <footer class="footer">
        <div class="container footer-top">
            <a href="/#inicio" class="logo" aria-label="LUMING início"><span class="logo-mark"><?= icon('lightbulb', 15) ?></span> LUMING</a>
            <p>Tecnologia + criatividade<br>para transformar negócios.</p>
            <div class="footer-links">
                <a href="/#sobre">Sobre</a>
                <a href="/#servicos">Serviços</a>
                <a href="/#portfolio">Portfólio</a>
                <a href="/contato">Contato</a>
            </div>
            <a class="footer-secret-link" href="/login" aria-label="Área exclusiva">Área exclusiva</a>
            <a class="instagram" href="/contato" aria-label="Instagram">◎</a>
        </div>
        <div class="container footer-bottom">
            <span>© <?= date('Y') ?> LUMING. Todos os direitos reservados.</span>
            <span>feito com intenção <span class="gold">✦</span></span>
        </div>
    </footer>
    <a class="whatsapp" href="https://wa.me/5500000000000" aria-label="Fale conosco pelo WhatsApp" target="_blank" rel="noopener"><?= icon('message-circle', 22) ?></a>
<?php endif ?>
<?php if ($layout === 'site'): ?>
    </div>
<?php endif ?>
<?php foreach ($jsDoLayout as $js): ?>
    <script src="<?= esc(asset($js)) ?>" defer></script>
<?php endforeach ?>
<?php foreach ($scripts as $js): ?>
    <script src="<?= esc(asset($js)) ?>" defer></script>
<?php endforeach ?>
</body>
</html>
