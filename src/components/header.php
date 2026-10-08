<?php 
/**
 * Cabeçalho do documento (<head> + abertura do <body>).
 *
 * Variáveis recebidas de makePage():
 * @var string $title
 * @var string $layout     'site' (padrão) ou 'dashboard'
 * @var bool   $navbar     inclui components/navbar.php (site público)
 * @var string $bodyClass
 * @var array  $styles     CSS extras (relativos a /assets)
 */

$layout ??= 'site';
$navbar ??= false;
$bodyClass ??= '';
$styles ??= [];

// Cada layout carrega o SEU pacote de CSS. Assim, os estilos do site público
// nunca vazam para o dashboard (e vice-versa).
$pacotes = [
    'site'      => ['css/site/site.css'],
    // Reservado para a Etapa 2 (só é carregado se o arquivo existir).
    'dashboard' => ['css/dashboard/dashboard.css'],
];

$cssDoLayout = array_filter(
    $pacotes[$layout] ?? $pacotes['site'],
    static fn (string $arquivo): bool => $layout !== 'dashboard'
        || is_file(BASE_PATH . DIRECTORY_SEPARATOR . PUB . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $arquivo)
);

$favicon = 'data:image/svg+xml,' . rawurlencode(
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><rect width="32" height="32" rx="16" fill="#0d0a10"/>'
    . '<circle cx="16" cy="16" r="12" fill="none" stroke="#d4af5a" stroke-width="2"/>'
    . '<path d="M16 9v1m0 12v1m-7-7h1m12 0h1" stroke="#d4af5a" stroke-width="2" stroke-linecap="round"/>'
    . '<circle cx="16" cy="16" r="3.5" fill="#d4af5a"/></svg>'
);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tecnologia + criatividade para transformar negócios em experiências que conectam, inspiram e geram movimento.">
    <meta name="theme-color" content="#0d0a10">
    <title><?= esc($title) ?> — LUMING</title>
    <link rel="icon" href="<?= $favicon ?>">
    <?php foreach ($cssDoLayout as $css): ?>
        <link rel="stylesheet" href="<?= esc(asset($css)) ?>">
    <?php endforeach ?>
    <?php foreach ($styles as $css): ?>
        <link rel="stylesheet" href="<?= esc(asset($css)) ?>">
    <?php endforeach ?>
</head>
<body class="layout-<?= esc($layout) ?><?= $bodyClass !== '' ? ' ' . esc($bodyClass) : '' ?>">
<?php if ($layout === 'site'): ?>
    <div class="site-shell">
    <?php if ($navbar): ?>
        <?php require COMPONENTS . 'navbar.php'; ?>
    <?php endif ?>
<?php elseif ($layout === 'dashboard'): ?>
    <?php
    // Gancho para a Etapa 2: o dashboard terá a sua própria sidebar
    // (components/sidebar.php), sem relação com a navbar do site.
    if (is_file(COMPONENTS . 'sidebar.php')) {
        require COMPONENTS . 'sidebar.php';
    }
    ?>
<?php endif ?>
