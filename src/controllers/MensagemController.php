<?php 

namespace controllers;

use ErrorException;
use models\Mensagem;
use models\Cliente;
use models\Servico;
use models\Ramo;

class MensagemController {
    /* ------------------------------------------------------------------
     * Telas do dashboard
     * ------------------------------------------------------------------ */

    // GET /admin/mensagens
    static public function makeList(): void {
        makePage('admin/mensagens/index', [
            'layout'    => 'dashboard',
            'title'     => 'Mensagens',
            'secao'     => 'Mensagens',
            'mensagens' => array_reverse(Mensagem::load() ?? []),
        ]);
    }

    // GET /admin/mensagens/{id}
    static public function makeShow(array $route, string $uri): void {
        $mensagem = self::buscar($uri);

        makePage('admin/mensagens/show', [
            'layout'   => 'dashboard',
            'title'    => adminNome($mensagem->projeto),
            'secao'    => 'Mensagens',
            'mensagem' => $mensagem,
        ]);
    }

    // GET /admin/mensagens/novo
    static public function makeCreate(): void {
        self::renderForm('create');
    }

    // GET /admin/mensagens/{id}/editar
    static public function makeEdit(array $route, string $uri): void {
        $mensagem = self::buscar($uri);

        self::renderForm('edit', $mensagem, Cliente::find((int) $mensagem->clienteId));
    }

    /* ------------------------------------------------------------------
     * Ações
     * ------------------------------------------------------------------ */

    // POST /admin/mensagens
    static public function saveMensagem(): void {
        $_POST['action'] = 'save'; // definido no servidor (não confia no hidden do form)

        $validacaoCliente = Cliente::validar($_POST);
        $validacaoMensagem = Mensagem::validar($_POST);
        $erros = array_merge($validacaoCliente['erros'], $validacaoMensagem['erros']);

        if (!empty($erros)) {
            self::renderForm('create', null, null, $erros, $_POST);
            return;
        }

        $cliente = $validacaoCliente['cliente'];
        $mensagem = $validacaoMensagem['mensagem'];
        $mensagem->clienteId = $cliente->save();
        $mensagem->save();

        setFlash('success', 'Mensagem cadastrada com sucesso.');
        header('Location: /admin/mensagens');
        exit;
    }

    // PUT /admin/mensagens/{id}
    static public function updateMensagem(array $route, string $uri): void {
        $mensagem = self::buscar($uri);
        $clienteAtual = Cliente::find((int) $mensagem->clienteId);

        $_POST['action'] = 'update';
        $_POST['id_mensagem'] = $mensagem->id;        // os ids vêm do banco/URL,
        $_POST['id_cliente'] = $mensagem->clienteId;  // não do formulário

        $validacaoCliente = Cliente::validar($_POST);
        $validacaoMensagem = Mensagem::validar($_POST);
        $erros = array_merge($validacaoCliente['erros'], $validacaoMensagem['erros']);

        if (!empty($erros)) {
            self::renderForm('edit', $mensagem, $clienteAtual, $erros, $_POST);
            return;
        }

        $cliente = $validacaoCliente['cliente'];
        $novaMensagem = $validacaoMensagem['mensagem'];

        try {
            $cliente->update();
        } catch (\PDOException $e) {
            self::renderForm('edit', $mensagem, $clienteAtual, ['Não foi possível salvar. Verifique se o e-mail ou o telefone já estão em uso.'], $_POST);
            return;
        } catch (\Throwable $e) {
            throw new ErrorException('Erro ao atualizar cliente', 500, previous: $e);
        }

        try {
            $novaMensagem->update();
        } catch (\Throwable $e) {
            throw new ErrorException('Erro ao atualizar mensagem', 500, previous: $e);
        }

        setFlash('success', 'Mensagem atualizada com sucesso.');
        header('Location: /admin/mensagens/' . $mensagem->id);
        exit;
    }

    // DELETE /admin/mensagens/{id}
    static public function deleteMensagem(array $route, string $uri): void {
        if (!csrfVerify()) {
            throw new ErrorException('Token CSRF inválido', 403);
        }

        $mensagem = self::buscar($uri);

        try {
            $mensagem->delete();
        } catch (\Throwable $e) {
            throw new ErrorException('Erro ao deletar mensagem', 500, previous: $e);
        }

        setFlash('success', 'Mensagem excluída com sucesso.');
        header('Location: /admin/mensagens');
        exit;
    }

    /* ------------------------------------------------------------------
     * Auxiliares
     * ------------------------------------------------------------------ */

    static private function buscar(string $uri): Mensagem {
        $mensagem = Mensagem::find(adminIdDaUri($uri));

        if (!$mensagem) {
            http_response_code(404);
            throw new ErrorException('Mensagem não encontrada.', 404);
        }

        return $mensagem;
    }

    /**
     * Monta o formulário de criação ('create') ou edição ('edit').
     * Em caso de erro de validação, $old (o $_POST enviado) repovoa os campos.
     */
    static private function renderForm(string $modo, ?Mensagem $mensagem = null, ?Cliente $cliente = null, array $erros = [], array $old = []): void {
        $editando = $modo === 'edit';

        makePage('admin/mensagens/' . $modo, [
            'layout'   => 'dashboard',
            'title'    => $editando ? 'Editar mensagem' : 'Nova mensagem',
            'secao'    => 'Mensagens',
            'mensagem' => $mensagem,
            'cliente'  => $cliente,
            'servicos' => Servico::load() ?? [],
            'ramos'    => Ramo::load() ?? [],
            'erros'    => $erros,
            'valores'  => [
                // dados do cliente
                'nome'      => $old['nome']      ?? ($cliente ? adminNome($cliente->nome) : ''),
                'email'     => $old['email']     ?? ($cliente->email ?? ''),
                'telefone'  => $old['telefone']  ?? ($cliente->telefone ?? ''),
                'ramo'      => $old['ramo']      ?? ($cliente->ramoId ?? ''),
                // dados da mensagem
                'projeto'   => $old['projeto']   ?? ($mensagem ? adminNome($mensagem->projeto) : ''),
                'servico'   => $old['servico']   ?? ($mensagem->servicoId ?? ''),
                'descricao' => $old['descricao'] ?? ($mensagem->descricao ?? ''),
            ],
        ]);
    }
}
