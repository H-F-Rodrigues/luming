<?php

/**
 * Funções auxiliares da ÁREA ADMINISTRATIVA (dashboard).
 * Carregado por app.php logo após functions.php.
 *
 * Tudo aqui é usado apenas pelo painel (/admin/...). O site público não depende deste arquivo.
 */

/* ------------------------------------------------------------------
 * Autenticação
 * ------------------------------------------------------------------ */

/**
 * Membro logado (ou null). Consultado uma única vez por requisição.
 */
function adminUser(): ?\models\Membro
{
    static $membro = false;

    if ($membro === false) {
        $id = (int) ($_SESSION['membro'] ?? 0);
        $membro = $id > 0 ? \models\Membro::find($id) : null;
    }

    return $membro;
}

/**
 * Barra quem não está logado (ou cujo membro foi removido) e manda para /login.
 * Chamada pelo router para toda URL que começa com /admin.
 */
function adminGuard(): void
{
    if (!adminUser()) {
        unset($_SESSION['membro']);
        header('Location: /login');
        exit;
    }
}

/* ------------------------------------------------------------------
 * Mensagens de sucesso/erro (flash)
 * ------------------------------------------------------------------ */

/**
 * Guarda uma mensagem para aparecer na próxima tela (toast do dashboard).
 *
 * @param string $tipo 'success' ou 'error'
 */
function setFlash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

/**
 * Devolve (e apaga) as mensagens guardadas por setFlash().
 *
 * @return array<int, array{tipo: string, mensagem: string}>
 */
function pullFlash(): array
{
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return is_array($flash) ? $flash : [];
}

/* ------------------------------------------------------------------
 * Utilidades de dados e de upload
 * ------------------------------------------------------------------ */

/**
 * Extrai o id numérico de URLs como /admin/clientes/12 ou /admin/clientes/12/editar.
 */
function adminIdDaUri(string $uri): int
{
    return preg_match('#^/admin/[a-z]+/([0-9]+)#', $uri, $achou) ? (int) $achou[1] : 0;
}

/**
 * Os models gravam nomes em MAIÚSCULAS; na tela mostramos "Nome Próprio".
 */
function adminNome(?string $texto): string
{
    return mb_convert_case(mb_strtolower(trim((string) $texto), 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
}

/**
 * Iniciais (até 2 letras) para avatares.
 */
function adminIniciais(?string $nome): string
{
    $partes = preg_split('/\s+/u', trim((string) $nome), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $iniciais = '';

    foreach (array_slice($partes, 0, 2) as $parte) {
        $iniciais .= mb_strtoupper(mb_substr($parte, 0, 1, 'UTF-8'), 'UTF-8');
    }

    return $iniciais !== '' ? $iniciais : '?';
}

/**
 * Data no formato brasileiro ("—" quando vazia ou inválida).
 */
function adminData(?string $valor, string $formato = 'd/m/Y'): string
{
    $time = $valor ? strtotime($valor) : false;

    return $time ? date($formato, $time) : '—';
}

/**
 * Entrada de $_FILES; se o campo nem veio no POST, devolve um "nenhum arquivo enviado"
 * (tratarImg() entende como 'skip').
 */
function adminArquivo(string $campo): array
{
    return $_FILES[$campo] ?? ['error' => UPLOAD_ERR_NO_FILE, 'tmp_name' => '', 'size' => 0, 'name' => ''];
}

/**
 * URL pública da capa de um projeto do portfólio ('' quando não há capa).
 */
function adminCapaUrl(\models\Portifolio $portifolio): string
{
    return trim((string) $portifolio->capa) !== ''
        ? storageUrl((string) $portifolio->caminho, (string) $portifolio->capa)
        : '';
}

/**
 * URL pública da foto de um membro ('' quando não há foto).
 */
function adminFotoUrl(\models\Membro $membro): string
{
    return trim((string) $membro->foto) !== '' ? storageUrl('membros', (string) $membro->foto) : '';
}

/* ------------------------------------------------------------------
 * Pedaços de HTML repetidos nas telas
 * ------------------------------------------------------------------ */

/**
 * Caixa com os erros de validação de um formulário ('' quando não há erros).
 *
 * @param string[] $erros
 */
function adminErros(array $erros): string
{
    if (empty($erros)) {
        return '';
    }

    $html = '<div class="adm-alert" role="alert">';
    foreach ($erros as $erro) {
        $html .= '<p>' . esc($erro) . '</p>';
    }

    return $html . '</div>';
}

/**
 * Botões de ação de uma linha de listagem: visualizar, editar e excluir.
 * O botão de excluir NÃO envia nada: ele abre o modal de confirmação (components/confirm.php),
 * que envia um POST com _method=DELETE.
 *
 * @param string $base    Ex.: /admin/clientes
 * @param string $recurso Ex.: cliente (usado nos rótulos de acessibilidade)
 */
function adminAcoes(string $base, int $id, string $nome, string $recurso): string
{
    $url = esc($base . '/' . $id);
    $rotulo = esc($recurso . ' ' . $nome);

    return '<div class="adm-actions">'
        . '<a class="adm-icon-btn" href="' . $url . '" aria-label="Visualizar ' . $rotulo . '" title="Visualizar">' . iconAdmin('eye', 15) . '</a>'
        . '<a class="adm-btn adm-btn-sm adm-btn-secondary" href="' . $url . '/editar" aria-label="Editar ' . $rotulo . '">' . iconAdmin('pencil', 13) . ' Editar</a>'
        . '<button type="button" class="adm-btn adm-btn-sm adm-btn-danger" data-delete data-action="' . $url . '" data-nome="' . esc($nome) . '" aria-label="Excluir ' . $rotulo . '">' . iconAdmin('trash-2', 13) . ' Excluir</button>'
        . '</div>';
}

/**
 * Ícones do dashboard (traços no estilo Lucide, o mesmo do wireframe).
 * Nomes que não existirem aqui são buscados em icon() (functions.php).
 *
 * @param string $nome    Nome do ícone.
 * @param int    $tamanho Largura/altura em px.
 * @param array  $attrs   Atributos extras do <svg>.
 */
function iconAdmin(string $nome, int $tamanho = 16, array $attrs = []): string
{
    static $paths = [
        'layout-dashboard' => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>',
        'folder-kanban'    => '<path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/><path d="M8 10v4"/><path d="M12 10v2"/><path d="M16 10v6"/>',
        'message-square'   => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'users'            => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'user'             => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'plus'             => '<path d="M5 12h14"/><path d="M12 5v14"/>',
        'pencil'           => '<path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/>',
        'trash-2'          => '<path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/>',
        'eye'              => '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>',
        'log-out'          => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>',
        'search'           => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
        'external-link'    => '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
        'image'            => '<rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>',
        'upload'           => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/>',
        'phone'            => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
        'alert-circle'     => '<circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>',
        'settings'         => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',
        'check-circle'     => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>',
    ];

    // Ícones que o dashboard não redefine (menu, x, mail, film, globe...) vêm do icon() do site.
    if (!isset($paths[$nome])) {
        return icon($nome, $tamanho, $attrs);
    }

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
        $paths[$nome]
    );
}
