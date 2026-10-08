<?php

/**
 * Monta uma página completa: header + view + footer.
 *
 * Argumentos opcionais (além dos dados da view):
 *  - layout    (string) 'site' (padrão) ou 'dashboard'. Define qual pacote de CSS/JS é carregado.
 *  - navbar    (bool)   inclui components/navbar.php (navbar do site público). Padrão: false.
 *  - footer    (bool)   inclui o rodapé do site público. Padrão: false.
 *  - bodyClass (string) classes extras no <body>.
 *  - styles    (array)  CSS extras, relativos a /assets (ex.: 'css/site/extra.css').
 *  - scripts   (array)  JS extras, relativos a /assets.
 */
function makePage(string $page, array $args): void {
    $args += [
        'layout'    => 'site',
        'navbar'    => false,
        'footer'    => false,
        'bodyClass' => '',
        'styles'    => [],
        'scripts'   => [],
        'erros'     => [],
    ];

    extract($args);

    require_once COMPONENTS . 'header.php';

    require_once VIEWS . $page . '.php';

    require_once COMPONENTS . 'footer.php';
}

/**
 * Escapa texto para uso seguro em HTML.
 */
function esc(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/**
 * URL de um arquivo em public/assets, com cache-busting pela data de modificação.
 * Ex.: asset('css/site/site.css') => /assets/css/site/site.css?v=1730000000
 */
function asset(string $caminho): string
{
    $caminho = ltrim(str_replace('\\', '/', $caminho), '/');
    $arquivo = BASE_PATH . DIRECTORY_SEPARATOR . PUB . DIRECTORY_SEPARATOR . 'assets'
        . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $caminho);

    return '/assets/' . $caminho . (is_file($arquivo) ? '?v=' . filemtime($arquivo) : '');
}

/**
 * URL pública de um arquivo em public/storage.
 * Ex.: storageUrl('portifolios/1', 'capa.jpg') => /storage/portifolios/1/capa.jpg
 */
function storageUrl(string ...$partes): string
{
    $segmentos = [];

    foreach ($partes as $parte) {
        foreach (explode('/', str_replace('\\', '/', $parte)) as $segmento) {
            if ($segmento !== '') {
                $segmentos[] = rawurlencode($segmento);
            }
        }
    }

    return '/storage/' . implode('/', $segmentos);
}

/**
 * Ícone SVG inline (traços no estilo Lucide, o mesmo do wireframe).
 *
 * @param string $nome   Nome do ícone.
 * @param int    $tamanho Largura/altura em px.
 * @param array  $attrs  Atributos extras do <svg> (ex.: ['fill' => 'currentColor', 'stroke-width' => 1.5]).
 */
function icon(string $nome, int $tamanho = 16, array $attrs = []): string
{
    static $paths = [
        'arrow-up-right' => '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>',
        'arrow-left'     => '<path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>',
        'arrow-right'    => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
        'chevron-down'   => '<path d="m6 9 6 6 6-6"/>',
        'chevron-left'   => '<path d="m15 18-6-6 6-6"/>',
        'chevron-right'  => '<path d="m9 18 6-6-6-6"/>',
        'check'          => '<path d="M20 6 9 17l-5-5"/>',
        'filter'         => '<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>',
        'film'           => '<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 3v18"/><path d="M3 7.5h4"/><path d="M3 12h18"/><path d="M3 16.5h4"/><path d="M17 3v18"/><path d="M17 7.5h4"/><path d="M17 16.5h4"/>',
        'globe'          => '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>',
        'lightbulb'      => '<path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>',
        'lock'           => '<circle cx="12" cy="16" r="1"/><rect x="3" y="10" width="18" height="12" rx="2"/><path d="M7 10V7a5 5 0 0 1 10 0v3"/>',
        'mail'           => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
        'map-pin'        => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
        'menu'           => '<path d="M4 12h16"/><path d="M4 6h16"/><path d="M4 18h16"/>',
        'message-circle' => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>',
        'palette'        => '<circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/>',
        'play'           => '<polygon points="6 3 20 12 6 21 6 3"/>',
        'quote'          => '<path d="M16 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z"/><path d="M5 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z"/>',
        'sparkles'       => '<path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/><path d="M20 3v4"/><path d="M22 5h-4"/><path d="M4 17v2"/><path d="M5 18H3"/>',
        'target'         => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
        'x'              => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
        'zap'            => '<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/>',
    ];

    $attrs += [
        'fill'            => 'none',
        'stroke'          => 'currentColor',
        'stroke-width'    => 2,
        'stroke-linecap'  => 'round',
        'stroke-linejoin' => 'round',
        'aria-hidden'     => 'true',
    ];

    $html = '';
    foreach ($attrs as $chave => $valor) {
        $html .= ' ' . $chave . '="' . esc($valor) . '"';
    }

    return sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="0 0 24 24"%2$s>%3$s</svg>',
        $tamanho,
        $html,
        $paths[$nome] ?? ''
    );
}

function createCsrf(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return sprintf(
        '<input type="hidden" name="csrf_token" value="%s">',
        htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')
    );
}

function csrfVerify(?string $token = null): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token ??= $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    return !empty($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

function tratarImg(array $file, string $destino = ''): ?array
{
    $erro = static function (string $mensagem): array {
        return [
            'res'     => false,
            'message' => $mensagem,
            'file'    => null,
        ];
    };

    if (!isset($file['error'], $file['tmp_name'], $file['size'])) {
        return $erro('Dados do arquivo inválidos.');
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return $erro('skip');
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return $erro('Erro no upload do arquivo.');
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        return $erro('Arquivo não enviado via upload.');
    }

    $tamanhoMaximo = 2 * 1024 * 1024;

    if ($file['size'] > $tamanhoMaximo) {
        return $erro('A imagem excede o tamanho máximo de 2MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $mimesPermitidos = [
        'image/png'  => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp',
    ];

    if (!isset($mimesPermitidos[$mime])) {
        return $erro('O arquivo não é uma imagem válida. Use PNG, JPG ou WEBP.');
    }

    $pasta = STORAGE . $destino;
    $pasta = rtrim($pasta, '/\\') . DIRECTORY_SEPARATOR;

    if (!is_dir($pasta) && !mkdir($pasta, 0755, true) && !is_dir($pasta)) {
        return $erro('Não foi possível criar o diretório de imagens.');
    }

    $extensao = $mimesPermitidos[$mime];
    $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $extensao;
    $caminhoCompleto = $pasta . $nomeArquivo;

    if (!move_uploaded_file($file['tmp_name'], $caminhoCompleto)) {
        return $erro('Falha ao mover o arquivo para o destino.');
    }

    return [
        'res' => true,
        'message' => 'Imagem enviada com sucesso.',
        'file' => $nomeArquivo,
        'path' => $caminhoCompleto,
    ];
}

function tratarMidias(array $files, string $destino = ''): array
{
    // Tabela de regras única, criada uma vez por request (static).
    static $regras = [
        'image/png'       => ['png',  'imagem', 2  * 1024 * 1024],
        'image/jpeg'      => ['jpg',  'imagem', 2  * 1024 * 1024],
        'image/webp'      => ['webp', 'imagem', 2  * 1024 * 1024],
        'image/gif'       => ['gif',  'imagem', 2  * 1024 * 1024],
        'video/mp4'       => ['mp4',  'video',  50 * 1024 * 1024],
        'video/webm'      => ['webm', 'video',  50 * 1024 * 1024],
        'video/ogg'       => ['ogv',  'video',  50 * 1024 * 1024],
        'video/quicktime' => ['mov',  'video',  50 * 1024 * 1024],
    ];

    $vazio = ['res' => false, 'message' => '', 'files' => [], 'errors' => []];

    if (!isset($files['error'], $files['tmp_name'], $files['size'], $files['name'])) {
        return ['message' => 'Dados inválidos.'] + $vazio;
    }

    // 1) Normaliza UMA vez para uma lista plana — evita $files['x'][$i] no loop.
    $entradas = [];
    if (is_array($files['error'])) {
        foreach ($files['error'] as $i => $err) {
            $entradas[] = [
                'error'    => $err,
                'tmp_name' => $files['tmp_name'][$i] ?? '',
                'size'     => (int) ($files['size'][$i] ?? 0),
                'name'     => (string) ($files['name'][$i] ?? ''),
            ];
        }
    } else {
        $entradas[] = [
            'error'    => $files['error'],
            'tmp_name' => $files['tmp_name'],
            'size'     => (int) $files['size'],
            'name'     => (string) $files['name'],
        ];
    }

    if (!$entradas) {
        return ['message' => 'Nenhum arquivo enviado.'] + $vazio;
    }

    // 2) Destino criado uma vez só (sai cedo se falhar).
    $pasta = rtrim(STORAGE . $destino, '/\\') . DIRECTORY_SEPARATOR;
    if (!is_dir($pasta) && !mkdir($pasta, 0755, true) && !is_dir($pasta)) {
        return ['message' => 'Não foi possível criar o diretório.'] + $vazio;
    }

    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $enviados = [];
    $erros    = [];

    // 3) Loop único — sem arrays paralelos, sem reindexar.
    foreach ($entradas as $e) {
        if ($e['error'] === UPLOAD_ERR_NO_FILE) {
            continue; // silencioso
        }

        $nome = $e['name'] ?: 'arquivo';

        if ($e['error'] !== UPLOAD_ERR_OK) {
            $erros[] = "Falha no upload de '{$nome}' (código {$e['error']}).";
            continue;
        }
        if (!is_uploaded_file($e['tmp_name'])) {
            $erros[] = "Arquivo '{$nome}' não enviado via upload.";
            continue;
        }

        $mime  = $finfo->file($e['tmp_name']) ?: '';
        $regra = $regras[$mime] ?? null;
        if (!$regra) {
            $erros[] = "Tipo não permitido: '{$nome}'.";
            continue;
        }

        [$ext, $tipo, $limite] = $regra;

        if ($e['size'] > $limite) {
            $erros[] = "O arquivo '{$nome}' excede " . ($limite / 1048576) . "MB.";
            continue;
        }

        $novoNome = bin2hex(random_bytes(16)) . '.' . $ext;
        $caminho  = $pasta . $novoNome;

        if (!move_uploaded_file($e['tmp_name'], $caminho)) {
            $erros[] = "Falha ao mover '{$nome}'.";
            continue;
        }

        $enviados[] = [
            'tipo'     => $tipo,
            'mime'     => $mime,
            'file'     => $novoNome,
            'path'     => $caminho,
            'original' => $nome,
        ];
    }

    // 4) Mensagem derivada sem if/else aninhado.
    $qtdOk  = count($enviados);
    $qtdErr = count($erros);

    return [
        'res'     => $qtdOk > 0,
        'message' => match (true) {
            $qtdOk > 0 && $qtdErr === 0 => "{$qtdOk} arquivo(s) enviado(s) com sucesso.",
            $qtdOk > 0                  => "Envio parcial: {$qtdOk} ok, {$qtdErr} com erro.",
            default                     => 'Nenhum arquivo foi enviado.',
        },
        'files'  => $enviados,
        'erros' => $erros,
    ];
}

function getTotalFolders(string $path): ?int {
    $caminho = STORAGE . 'portifolios';
    $totalPastas = 1;

    if (is_dir($caminho)) {
        $iterator = new FilesystemIterator($caminho, FilesystemIterator::SKIP_DOTS);

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $totalPastas++;
            }
        }
    }

    return $totalPastas;
}

/**
 * Remove a máscara do telefone, deixando apenas os números.
 * 
 * @param string $telefone Telefone com máscara (ex: "(11) 98765-4321")
 * @return string Telefone apenas com dígitos (ex: "11987654321")
 */
function removerMascaraTelefone(string $telefone): string
{
    // Remove tudo que não for número
    return preg_replace('/\D/', '', $telefone);
}

/**
 * Aplica a máscara no telefone de acordo com a quantidade de dígitos.
 * - 10 dígitos → telefone fixo: (XX) XXXX-XXXX
 * - 11 dígitos → celular:      (XX) XXXXX-XXXX
 * 
 * @param string $telefone Telefone apenas com números
 * @return string Telefone formatado
 */
function aplicarMascaraTelefone(string $telefone): string
{
    // Garante que só tem números
    $telefone = preg_replace('/\D/', '', $telefone);

    $tamanho = strlen($telefone);

    // Telefone fixo: (XX) XXXX-XXXX
    if ($tamanho === 10) {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
    }

    // Celular: (XX) XXXXX-XXXX
    if ($tamanho === 11) {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
    }

    // Caso não seja 10 ou 11 dígitos, retorna como veio (ou você pode lançar exceção)
    return $telefone;
}