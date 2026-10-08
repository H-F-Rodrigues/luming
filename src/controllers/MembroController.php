<?php 

namespace controllers;

use ErrorException;
use models\Membro;
use models\Funcao;

class MembroController {
    /* ------------------------------------------------------------------
     * Telas do dashboard
     * ------------------------------------------------------------------ */

    // GET /admin/membros
    static public function makeList(): void {
        makePage('admin/membros/index', [
            'layout'  => 'dashboard',
            'title'   => 'Equipe',
            'secao'   => 'Equipe',
            'membros' => Membro::load() ?? [],
        ]);
    }

    // GET /admin/membros/{id}
    static public function makeShow(array $route, string $uri): void {
        $membro = self::buscar($uri);

        makePage('admin/membros/show', [
            'layout' => 'dashboard',
            'title'  => adminNome($membro->nome),
            'secao'  => 'Equipe',
            'membro' => $membro,
        ]);
    }

    // GET /admin/membros/novo
    static public function makeCreate(): void {
        self::renderForm('create');
    }

    // GET /admin/membros/{id}/editar  (só a própria conta)
    static public function makeEdit(array $route, string $uri): void {
        $membro = self::buscar($uri);
        self::exigirProprio($membro);

        self::renderForm('edit', $membro);
    }

    // GET /admin/configuracoes  (conta do membro logado)
    static public function makeConfig(): void {
        self::renderForm('edit', adminUser(), [], [], true);
    }

    /* ------------------------------------------------------------------
     * Ações
     * ------------------------------------------------------------------ */

    // POST /admin/membros
    static public function saveMembro(): void {
        $_POST['action'] = 'save'; // definido no servidor (não confia no hidden do form)

        $validacao = Membro::validar($_POST);
        $erros = $validacao['erros'];
        $validacaoImg = ['res' => false, 'message' => 'skip', 'file' => null];

        // A foto só é gravada se o restante do formulário estiver válido.
        if (empty($erros)) {
            $validacaoImg = tratarImg(adminArquivo('foto'), 'membros');

            if (!$validacaoImg['res'] && $validacaoImg['message'] !== 'skip') { // Se for 'skip' ignora o erro
                $erros[] = $validacaoImg['message'];
            }
        }

        if (!empty($erros)) {
            self::renderForm('create', null, $erros, $_POST);
            return;
        }

        $membro = $validacao['membro'];
        if ($validacaoImg['res']) {
            $membro->foto = $validacaoImg['file'];
        }
        $membro->save();

        setFlash('success', 'Membro cadastrado com sucesso.');
        header('Location: /admin/membros');
        exit;
    }

    // PUT /admin/membros/{id}  (só a própria conta)
    static public function updateMembro(array $route, string $uri): void {
        $membro = self::buscar($uri);
        self::exigirProprio($membro);

        self::atualizar($membro, false);
    }

    // PUT /admin/configuracoes  (conta do membro logado)
    static public function updateConta(): void {
        self::atualizar(adminUser(), true);
    }

    // DELETE /admin/membros/{id}  (só a própria conta)
    static public function deleteMembro(array $route, string $uri): void {
        $membro = self::buscar($uri);
        self::exigirProprio($membro);

        self::excluir($membro, false);
    }

    // DELETE /admin/configuracoes  (conta do membro logado)
    static public function deleteConta(): void {
        self::excluir(adminUser(), true);
    }

    /* ------------------------------------------------------------------
     * Auxiliares
     * ------------------------------------------------------------------ */

    static private function buscar(string $uri): Membro {
        $membro = Membro::find(adminIdDaUri($uri));

        if (!$membro) {
            http_response_code(404);
            throw new ErrorException('Membro não encontrado.', 404);
        }

        return $membro;
    }

    /**
     * Cada membro só pode editar e excluir a PRÓPRIA conta.
     * (Visualizar os demais membros continua liberado.)
     */
    static private function exigirProprio(Membro $membro): void {
        if ((int) $membro->id !== (int) ($_SESSION['membro'] ?? 0)) {
            http_response_code(403);
            throw new ErrorException('Você só pode editar ou excluir a sua própria conta.', 403);
        }
    }

    /**
     * Atualiza a conta de $membro. $config = true quando vem da aba Configurações
     * (muda só o destino do redirecionamento e a tela exibida em caso de erro).
     */
    static private function atualizar(Membro $membro, bool $config): void {
        $_POST['action'] = 'update';
        $_POST['id_membro'] = $membro->id; // o id vem da sessão/URL, não do formulário

        // Senha em branco = manter a atual. O validador aceita um hash já existente ($2y$...) como está.
        if (trim((string) ($_POST['senha'] ?? '')) === '') {
            $_POST['senha'] = $membro->senha;
        }

        $validacao = Membro::validar($_POST);
        $erros = $validacao['erros'];
        $validacaoImg = ['res' => false, 'message' => 'skip', 'file' => null];

        if (empty($erros)) {
            $validacaoImg = tratarImg(adminArquivo('foto'), 'membros');

            if (!$validacaoImg['res'] && $validacaoImg['message'] !== 'skip') { // Se for 'skip' ignora o erro
                $erros[] = $validacaoImg['message'];
            }
        }

        if (!empty($erros)) {
            self::renderForm('edit', $membro, $erros, $_POST, $config);
            return;
        }

        $novoMembro = $validacao['membro'];
        $novoMembro->foto = $membro->foto; // sem foto nova, mantém a antiga

        if ($validacaoImg['res']) {
            $novoMembro->foto = $validacaoImg['file'];
        }

        try {
            $novoMembro->update();
        } catch (\PDOException $e) {
            // update falhou: descarta a foto nova (a antiga continua valendo)
            if ($validacaoImg['res'] && is_file($validacaoImg['path'])) {
                unlink($validacaoImg['path']);
            }
            self::renderForm('edit', $membro, ['Não foi possível salvar. Verifique se o e-mail já está em uso.'], $_POST, $config);
            return;
        }

        // Só apaga a foto antiga depois de o banco ter sido atualizado.
        if ($validacaoImg['res']) {
            self::apagarFoto($membro->foto);
        }

        setFlash('success', $config ? 'Configurações salvas com sucesso.' : 'Membro atualizado com sucesso.');
        header('Location: ' . ($config ? '/admin/configuracoes' : '/admin/membros/' . $membro->id));
        exit;
    }

    /**
     * Exclui a conta de $membro (sempre a do usuário logado, ver exigirProprio/adminUser),
     * encerra a sessão e volta para o login.
     */
    static private function excluir(Membro $membro, bool $config): void {
        if (!csrfVerify()) {
            throw new ErrorException('Token CSRF inválido', 403);
        }

        try {
            $membro->delete();
        } catch (\PDOException $e) {
            setFlash('error', 'Não foi possível excluir a conta. Ela pode ter registros vinculados.');
            header('Location: ' . ($config ? '/admin/configuracoes' : '/admin/membros/' . $membro->id));
            exit;
        }

        self::apagarFoto($membro->foto);

        // A conta excluída é a do próprio usuário: encerra a sessão e volta para o login.
        unset($_SESSION['membro'], $_SESSION['flash']);
        header('Location: /login');
        exit;
    }

    static private function apagarFoto(?string $foto): void {
        if (trim((string) $foto) === '') {
            return;
        }

        $caminhoFoto = STORAGE . 'membros' . DIRECTORY_SEPARATOR . $foto;

        if (is_file($caminhoFoto)) {
            unlink($caminhoFoto);
        }
    }

    /**
     * Monta o formulário de criação ('create') ou edição ('edit').
     * $config = true: exibe a tela da aba Configurações em vez da edição em /admin/membros.
     * Em caso de erro de validação, $old (o $_POST enviado) repovoa os campos.
     * A senha nunca volta para o formulário.
     */
    static private function renderForm(string $modo, ?Membro $membro = null, array $erros = [], array $old = [], bool $config = false): void {
        $editando = $modo === 'edit';

        makePage($config ? 'admin/configuracoes' : 'admin/membros/' . $modo, [
            'layout'  => 'dashboard',
            'title'   => $config ? 'Configurações' : ($editando ? 'Editar membro' : 'Novo membro'),
            'secao'   => $config ? 'Configurações' : 'Equipe',
            'membro'  => $membro,
            'funcoes' => Funcao::load() ?? [],
            'erros'   => $erros,
            'valores' => [
                'nome'   => $old['nome']   ?? ($membro ? adminNome($membro->nome) : ''),
                'email'  => $old['email']  ?? ($membro->email ?? ''),
                'funcao' => $old['funcao'] ?? ($membro->funcaoId ?? ''),
            ],
        ]);
    }
}
