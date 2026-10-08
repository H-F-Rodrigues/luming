<?php

namespace controllers;

use ErrorException;
use models\Portifolio;
use models\Galeria;
use models\PortifolioServico;
use models\Servico;

class PortifolioController {
    /* ------------------------------------------------------------------
     * Telas do dashboard
     * ------------------------------------------------------------------ */

    // GET /admin/portfolio
    static public function makeList(): void {
        makePage('admin/portfolio/index', [
            'layout'    => 'dashboard',
            'title'     => 'Portfólio',
            'secao'     => 'Portfólio',
            'projetos'  => array_reverse(Portifolio::load() ?? []),
        ]);
    }

    // GET /admin/portfolio/{id}
    static public function makeShow(array $route, string $uri): void {
        $portifolio = self::buscar($uri);

        $midias = [];
        foreach (Galeria::findByPortifolio((int) $portifolio->id) ?? [] as $item) {
            $midias[] = [
                'tipo' => preg_match('/\.(mp4|webm|ogv|mov)$/i', (string) $item->arquivo) ? 'video' : 'image',
                'src'  => storageUrl((string) $item->caminho, (string) $item->arquivo),
            ];
        }

        makePage('admin/portfolio/show', [
            'layout'     => 'dashboard',
            'title'      => adminNome($portifolio->projeto),
            'secao'      => 'Portfólio',
            'portifolio' => $portifolio,
            'midias'     => $midias,
        ]);
    }

    // GET /admin/portfolio/novo
    static public function makeCreate(): void {
        self::renderForm('create');
    }

    // GET /admin/portfolio/{id}/editar
    static public function makeEdit(array $route, string $uri): void {
        self::renderForm('edit', self::buscar($uri));
    }

    /* ------------------------------------------------------------------
     * Ações
     * ------------------------------------------------------------------ */

    // POST /admin/portfolio
    static public function savePortifolio(): void {
        $_POST['action'] = 'save'; // definido no servidor (não confia no hidden do form)

        $validacao = Portifolio::validar($_POST);
        $erros = $validacao['erros'];

        if (!self::servicoValido()) {
            $erros[] = 'Selecione o tipo de serviço.';
        }

        $caminho = 'portifolios' . DIRECTORY_SEPARATOR . getTotalFolders('portifolios');
        $validacaoImg = ['res' => false, 'message' => 'skip', 'file' => null];
        $validacaoMidias = ['res' => false, 'files' => [], 'erros' => []];

        // Os arquivos só são gravados se o restante do formulário estiver válido.
        if (empty($erros)) {
            $validacaoImg = tratarImg(adminArquivo('capa'), $caminho);

            if (!$validacaoImg['res'] && $validacaoImg['message'] !== 'skip') { // Se for 'skip' ignora o erro
                $erros[] = $validacaoImg['message'];
            }
        }

        if (empty($erros) && self::temMidias()) {
            $validacaoMidias = tratarMidias($_FILES['midias'], $caminho . DIRECTORY_SEPARATOR . 'galeria');
            $erros = array_merge($erros, $validacaoMidias['erros'] ?? []);
        }

        if (!empty($erros)) {
            self::descartarUploads($validacaoImg, $validacaoMidias);
            self::renderForm('create', null, [], $erros, $_POST);
            return;
        }

        $portifolio = $validacao['portifolio'];
        $portifolio->capa = $validacaoImg['res'] ? $validacaoImg['file'] : '';
        $portifolio->caminho = $caminho;
        $idPortifolio = $portifolio->save();

        $pastaGaleria = DIRECTORY_SEPARATOR . $caminho . DIRECTORY_SEPARATOR . 'galeria';
        foreach ($validacaoMidias['files'] as $midia) {
            $galeria = new Galeria(); // um objeto novo por mídia
            $galeria->portifolioId = $idPortifolio;
            $galeria->caminho = $pastaGaleria;
            $galeria->arquivo = $midia['file'];
            $galeria->save();
        }

        self::vincularServico((int) $idPortifolio, (int) $_POST['servico']);

        setFlash('success', 'Projeto cadastrado com sucesso.');
        header('Location: /admin/portfolio');
        exit;
    }

    // PUT /admin/portfolio/{id}
    static public function updatePortifolio(array $route, string $uri): void {
        $portifolio = self::buscar($uri);
        $galeria = Galeria::findByPortifolio((int) $portifolio->id) ?? [];

        $_POST['action'] = 'update';
        $_POST['id_portifolio'] = $portifolio->id; // o id vem da URL, não do formulário

        $validacao = Portifolio::validar($_POST);
        $erros = $validacao['erros'];

        if (!self::servicoValido()) {
            $erros[] = 'Selecione o tipo de serviço.';
        }

        // Reaproveita a pasta existente; só cria nova se por algum motivo não tiver
        $caminho = $portifolio->caminho
            ?: ('portifolios' . DIRECTORY_SEPARATOR . getTotalFolders('portifolios'));

        $validacaoImg = ['res' => false, 'message' => 'skip', 'file' => null];
        $validacaoMidias = ['res' => false, 'files' => [], 'erros' => []];

        if (empty($erros)) {
            $validacaoImg = tratarImg(adminArquivo('capa'), $caminho);

            if (!$validacaoImg['res'] && $validacaoImg['message'] !== 'skip') { // Se for 'skip' ignora o erro
                $erros[] = $validacaoImg['message'];
            }
        }

        if (empty($erros) && self::temMidias()) {
            $validacaoMidias = tratarMidias($_FILES['midias'], $caminho . DIRECTORY_SEPARATOR . 'galeria');
            $erros = array_merge($erros, $validacaoMidias['erros'] ?? []);
        }

        if (!empty($erros)) {
            self::descartarUploads($validacaoImg, $validacaoMidias);
            self::renderForm('edit', $portifolio, $galeria, $erros, $_POST);
            return;
        }

        // ---- Atualiza o portfólio ----
        $novoPortifolio = $validacao['portifolio'];
        $novoPortifolio->id      = $portifolio->id;        // preserva o id!
        $novoPortifolio->caminho = $caminho;
        $novoPortifolio->capa    = $portifolio->capa;      // mantém a capa antiga...

        if ($validacaoImg['res']) {
            $novoPortifolio->capa = $validacaoImg['file']; // ...ou usa a nova, se enviada
        }

        $novoPortifolio->update();

        self::vincularServico((int) $portifolio->id, (int) $_POST['servico']);

        if ($validacaoImg['res'] && trim((string) $portifolio->capa) !== '') {
            $capaAntiga = STORAGE . ltrim((string) $portifolio->caminho, '/\\') . DIRECTORY_SEPARATOR . $portifolio->capa;

            if (is_file($capaAntiga)) {
                unlink($capaAntiga);
            }
        }

        // ---- Só toca na galeria se novas mídias foram enviadas ----
        if ($validacaoMidias['res']) {
            foreach ($galeria as $midia) {
                $arquivo = STORAGE . ltrim($midia->caminho, '/\\')
                        . DIRECTORY_SEPARATOR . $midia->arquivo;
                if (is_file($arquivo)) {
                    unlink($arquivo);
                }
                $midia->delete();
            }

            $pastaGaleria = DIRECTORY_SEPARATOR . $caminho . DIRECTORY_SEPARATOR . 'galeria';
            foreach ($validacaoMidias['files'] as $novaMidia) {
                $novaGaleria = new Galeria();             // <-- novo objeto por iteração
                $novaGaleria->portifolioId = $novoPortifolio->id;
                $novaGaleria->caminho      = $pastaGaleria;
                $novaGaleria->arquivo      = $novaMidia['file'];
                $novaGaleria->save();
            }
        }

        setFlash('success', 'Projeto atualizado com sucesso.');
        header('Location: /admin/portfolio/' . $portifolio->id);
        exit;
    }

    // DELETE /admin/portfolio/{id}
    static public function deletePortifolio(array $route, string $uri): void {
        if (!csrfVerify()) {
            throw new ErrorException('Token CSRF inválido', 403);
        }

        $portifolio = self::buscar($uri);
        $galeria = Galeria::findByPortifolio((int) $portifolio->id) ?? [];

        // Remove primeiro o que depende do projeto (vínculo com o serviço e galeria) e depois o projeto,
        // tudo numa transação. Se o banco tiver ON DELETE CASCADE, estes DELETEs não encontram nada
        // a remover; se alguma chave estrangeira não tiver cascade, a exclusão continua funcionando.
        // $galeria foi carregada acima para sabermos quais arquivos apagar do disco depois.
        try {
            PDO->beginTransaction();

            PortifolioServico::deleteByPortifolio((int) $portifolio->id);
            foreach ($galeria as $midia) {
                $midia->delete();
            }
            $portifolio->delete();

            PDO->commit();
        } catch (\PDOException $e) {
            if (PDO->inTransaction()) {
                PDO->rollBack();
            }

            $detalhe = (string) ($e->errorInfo[2] ?? $e->getMessage());
            error_log('Erro ao excluir projeto ' . $portifolio->id . ': ' . $detalhe);

            setFlash('error', 'Não foi possível excluir este projeto. ' . $detalhe);
            header('Location: /admin/portfolio/' . $portifolio->id);
            exit;
        }

        // Registros removidos: agora apaga os arquivos do disco (galeria e capa).
        foreach ($galeria as $midia) {
            $arquivo = STORAGE . ltrim($midia->caminho, '/\\')
                    . DIRECTORY_SEPARATOR . $midia->arquivo;
            if (is_file($arquivo)) {
                unlink($arquivo);
            }
        }

        if (trim((string) $portifolio->capa) !== '') {
            $capa = STORAGE . ltrim((string) $portifolio->caminho, '/\\') . DIRECTORY_SEPARATOR . $portifolio->capa;

            if (is_file($capa)) {
                unlink($capa);
            }
        }

        setFlash('success', 'Projeto excluído com sucesso.');
        header('Location: /admin/portfolio');
        exit;
    }

    /* ------------------------------------------------------------------
     * Auxiliares
     * ------------------------------------------------------------------ */

    static private function buscar(string $uri): Portifolio {
        $portifolio = Portifolio::find(adminIdDaUri($uri));

        if (!$portifolio) {
            http_response_code(404);
            throw new ErrorException('Projeto não encontrado.', 404);
        }

        return $portifolio;
    }

    // O tipo de serviço enviado existe?
    static private function servicoValido(): bool {
        return Servico::find((int) ($_POST['servico'] ?? 0)) !== null;
    }

    // Deixa o projeto com um único serviço vinculado (troca o anterior, se houver).
    static private function vincularServico(int $idPortifolio, int $idServico): void {
        PDO->beginTransaction();

        try {
            PortifolioServico::deleteByPortifolio($idPortifolio);

            $portifolioServico = new PortifolioServico();
            $portifolioServico->idPortifolio = $idPortifolio;
            $portifolioServico->idServico = $idServico;
            $portifolioServico->save();

            PDO->commit();
        } catch (\PDOException $e) {
            if (PDO->inTransaction()) {
                PDO->rollBack();
            }

            throw new ErrorException('Erro ao vincular o serviço ao projeto', 500, previous: $e);
        }
    }

    // Há ao menos um arquivo no campo midias[]?
    static private function temMidias(): bool {
        return !empty($_FILES['midias']['name'][0] ?? null);
    }

    // Apaga do disco os arquivos já enviados quando o formulário volta com erro.
    static private function descartarUploads(array $validacaoImg, array $validacaoMidias): void {
        if (!empty($validacaoImg['res']) && !empty($validacaoImg['path']) && is_file($validacaoImg['path'])) {
            unlink($validacaoImg['path']);
        }

        foreach ($validacaoMidias['files'] ?? [] as $midia) {
            if (!empty($midia['path']) && is_file($midia['path'])) {
                unlink($midia['path']);
            }
        }
    }

    /**
     * Monta o formulário de criação ('create') ou edição ('edit').
     * Em caso de erro de validação, $old (o $_POST enviado) repovoa os campos.
     *
     * @param Galeria[] $galeria Mídias atuais do projeto (só na edição)
     */
    static private function renderForm(string $modo, ?Portifolio $portifolio = null, array $galeria = [], array $erros = [], array $old = []): void {
        $editando = $modo === 'edit';

        if ($editando && empty($galeria) && $portifolio) {
            $galeria = Galeria::findByPortifolio((int) $portifolio->id) ?? [];
        }

        $servicos = Servico::load();
        $servicoAtual = ($editando && $portifolio) ? PortifolioServico::findByPortifolio((int) $portifolio->id) : null;

        makePage('admin/portfolio/' . $modo, [
            'layout'     => 'dashboard',
            'title'      => $editando ? 'Editar projeto' : 'Novo projeto',
            'secao'      => 'Portfólio',
            'portifolio' => $portifolio,
            'totalMidias' => count($galeria),
            'erros'      => $erros,
            'valores'    => [
                'projeto'  => $old['projeto']  ?? ($portifolio ? adminNome($portifolio->projeto) : ''),
                'sobre'    => $old['sobre']    ?? ($portifolio->sobre ?? ''),
                'ideia'    => $old['ideia']    ?? ($portifolio->ideia ?? ''),
                'intencao' => $old['intencao'] ?? ($portifolio->intencao ?? ''),
                'impacto'  => $old['impacto']  ?? ($portifolio->impacto ?? ''),
                'url'      => $old['url']      ?? ($portifolio->url ?? ''),
                'github'   => $old['github']   ?? ($portifolio->github ?? ''),
                'servico'  => $old['servico']  ?? ($servicoAtual->idServico ?? ''),
            ],
            'servicos' => $servicos
        ]);
    }
}
