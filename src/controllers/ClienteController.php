<?php 

namespace controllers;

use ErrorException;
use models\Cliente;
use models\Mensagem;
use models\Ramo;

class ClienteController {
    /* ------------------------------------------------------------------
     * Telas do dashboard
     * ------------------------------------------------------------------ */

    // GET /admin/clientes
    static public function makeList(): void {
        makePage('admin/clientes/index', [
            'layout'   => 'dashboard',
            'title'    => 'Clientes',
            'secao'    => 'Clientes',
            'clientes' => array_reverse(Cliente::load() ?? []),
        ]);
    }

    // GET /admin/clientes/{id}
    static public function makeShow(array $route, string $uri): void {
        $cliente = self::buscar($uri);

        // Mensagens enviadas por este cliente.
        $mensagens = array_values(array_filter(
            Mensagem::load() ?? [],
            static fn ($mensagem) => (int) $mensagem->clienteId === (int) $cliente->id
        ));

        makePage('admin/clientes/show', [
            'layout'    => 'dashboard',
            'title'     => adminNome($cliente->nome),
            'secao'     => 'Clientes',
            'cliente'   => $cliente,
            'mensagens' => array_reverse($mensagens),
        ]);
    }

    // GET /admin/clientes/novo
    static public function makeCreate(): void {
        self::renderForm('create');
    }

    // GET /admin/clientes/{id}/editar
    static public function makeEdit(array $route, string $uri): void {
        self::renderForm('edit', self::buscar($uri));
    }

    /* ------------------------------------------------------------------
     * Ações
     * ------------------------------------------------------------------ */

    // POST /admin/clientes
    static public function saveCliente(): void {
        $_POST['action'] = 'save'; // definido no servidor (não confia no hidden do form)

        $validacao = Cliente::validar($_POST);
        $erros = $validacao['erros'];

        if (!empty($erros)) {
            self::renderForm('create', null, $erros, $_POST);
            return;
        }

        $cliente = $validacao['cliente'];
        $cliente->save();

        setFlash('success', 'Cliente cadastrado com sucesso.');
        header('Location: /admin/clientes');
        exit;
    }

    // PUT /admin/clientes/{id}
    static public function updateCliente(array $route, string $uri): void {
        $cliente = self::buscar($uri);

        $_POST['action'] = 'update';
        $_POST['id_cliente'] = $cliente->id; // o id vem da URL, não do formulário

        $validacao = Cliente::validar($_POST);
        $erros = $validacao['erros'];

        if (!empty($erros)) {
            self::renderForm('edit', $cliente, $erros, $_POST);
            return;
        }

        $novoCliente = $validacao['cliente'];

        try {
            $novoCliente->update();
        } catch (\PDOException $e) {
            self::renderForm('edit', $cliente, ['Não foi possível salvar. Verifique se o e-mail ou o telefone já estão em uso.'], $_POST);
            return;
        }

        setFlash('success', 'Cliente atualizado com sucesso.');
        header('Location: /admin/clientes/' . $cliente->id);
        exit;
    }

    // DELETE /admin/clientes/{id}
    static public function deleteCliente(array $route, string $uri): void {
        if (!csrfVerify()) {
            throw new ErrorException('Token CSRF inválido', 403);
        }

        $cliente = self::buscar($uri);

        try {
            $cliente->delete();
        } catch (\PDOException $e) {
            // Normalmente: o cliente ainda tem mensagens vinculadas.
            setFlash('error', 'Não foi possível excluir este cliente. Exclua primeiro as mensagens vinculadas a ele.');
            header('Location: /admin/clientes/' . $cliente->id);
            exit;
        }

        setFlash('success', 'Cliente excluído com sucesso.');
        header('Location: /admin/clientes');
        exit;
    }

    /* ------------------------------------------------------------------
     * Auxiliares
     * ------------------------------------------------------------------ */

    static private function buscar(string $uri): Cliente {
        $cliente = Cliente::find(adminIdDaUri($uri));

        if (!$cliente) {
            http_response_code(404);
            throw new ErrorException('Cliente não encontrado.', 404);
        }

        return $cliente;
    }

    /**
     * Monta o formulário de criação ('create') ou edição ('edit').
     * Em caso de erro de validação, $old (o $_POST enviado) repovoa os campos.
     */
    static private function renderForm(string $modo, ?Cliente $cliente = null, array $erros = [], array $old = []): void {
        $editando = $modo === 'edit';

        makePage('admin/clientes/' . $modo, [
            'layout'  => 'dashboard',
            'title'   => $editando ? 'Editar cliente' : 'Novo cliente',
            'secao'   => 'Clientes',
            'cliente' => $cliente,
            'ramos'   => Ramo::load() ?? [],
            'erros'   => $erros,
            'valores' => [
                'nome'     => $old['nome']     ?? ($cliente ? adminNome($cliente->nome) : ''),
                'email'    => $old['email']    ?? ($cliente->email ?? ''),
                'telefone' => $old['telefone'] ?? ($cliente->telefone ?? ''),
                'ramo'     => $old['ramo']     ?? ($cliente->ramoId ?? ''),
            ],
        ]);
    }
}
