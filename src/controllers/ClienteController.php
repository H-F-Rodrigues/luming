<?php 

namespace controllers;

use ErrorException;
use models\Cliente;
use models\Ramo;

class ClienteController {
    static public function makeClientes() {
        $clientes = Cliente::load();
        $ramos = Ramo::load();
        makePage('cliente/clientes', [
            'title' => 'clientes',
            'erros' => [],
            'clientes' => $clientes,
            'ramos' => $ramos
        ]);
    }
    
    static public function makeCliente(array $route, string $uri) {
        $routeItems = explode('/', $uri);

        $clienteId = end($routeItems);
        $cliente = Cliente::find($clienteId);
        makePage('cliente/cliente', [
            'title' => "cliente: {$cliente->nome}",
            'erros' => [],
            'cliente' => $cliente
        ]);
    }

    static public function saveCLiente() {
        $clientes = Cliente::load();
        $ramos = Ramo::load();
        $validacao = Cliente::validar($_POST);
        $erros = $validacao['erros'];

        if(!empty($erros)) {
            makePage('cliente/clientes', [
                'title' => 'clientes',
                'erros' => $erros,
                'clientes' => $clientes,
                'ramos' => $ramos
            ]);
            return;
        }

        $cliente = $validacao['cliente'];
        $cliente->save();
        header('Location: /clientes');
    }

    static public function makeEdit() {
        $cliente = Cliente::find($_POST['id_cliente']);
        $ramos = Ramo::load();
        makePage('cliente/edit', [
            'title' => "Edit: {$cliente->nome}",
            'erros' => [],
            'cliente' => $cliente,
            'ramos' => $ramos
        ]);
    }

    static public function updateCliente() {
        $cliente = Cliente::find($_POST['id_cliente']);
        $ramos = Ramo::load();
        $validacao = Cliente::validar($_POST);
        $erros = $validacao['erros'];
        if (!empty($erros)) {
           makePage('cliente/edit', [
                'title' => "Edit: {$cliente->nome}",
                'erros' => $erros,
                'cliente' => $cliente,
                'ramos' => $ramos
            ]); 
            return;
        }
        $novoCliente = $validacao['cliente'];
        $novoCliente->update();
        header('Location: /clientes');
    }

    static public function deleteCliente() {
        $cliente = Cliente::find($_POST['id_cliente']);
        $cliente->delete();
        header('Location: /clientes');
    }
}