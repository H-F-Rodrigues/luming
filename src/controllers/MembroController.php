<?php 

namespace controllers;

use ErrorException;
use models\Membro;
use models\Funcao;

class MembroController {
    static public function makeMembros() {
        $membros = Membro::load();
        $funcoes = Funcao::load();
        makePage('membro/membros', [
            'title' => 'Membros',
            'erros' => [],
            'membros' => $membros,
            'funcoes' => $funcoes
        ]);
    }
    
    static public function makeMembro(array $route, string $uri) {
        $routeItems = explode('/', $uri);

        $membroId = end($routeItems);
        $membro = Membro::find($membroId);
        makePage('membro/membro', [
            'title' => "Membro: {$membro->nome}",
            'erros' => [],
            'membro' => $membro
        ]);
    }

    static public function saveMembro() {
        $validacao = Membro::validar($_POST);

        $erros = $validacao['erros'];
        if (!empty($erros)) {
            $membros = Membro::load();
            $funcoes = Funcao::load();
            makePage('membro/membros', [
                'title' => 'Membros',
                'erros' => $erros,
                'membros' => $membros,
                'funcoes' => $funcoes
            ]);
            return;
        }
        $membro = $validacao['membro'];
        $membro->save();
        header('Location: /membros');
    }

    static public function makeEdit() {
        $membro = Membro::find($_POST['id_membro']);
        $funcoes = Funcao::load();
        makePage('membro/edit', [
            'title' => "Edit: {$membro->nome}",
            'erros' => [],
            'membro' => $membro,
            'funcoes' => $funcoes
        ]);
    }

    static public function updateMembro() {
        $validacao = Membro::validar($_POST);
        $erros = $validacao['erros'];
        if (!empty($erros)) {
            $membros = Membro::load();
            $funcoes = Funcao::load();
            makePage('membro/membros/edit', [
                'title' => 'Membros',
                'erros' => $erros,
                'membros' => $membros,
                'funcoes' => $funcoes
            ]);
            return;
        }
        $membro = $validacao['membro'];
        $membro->update();
        header('Location: /membros');
    }

    static public function deleteMembro() {
        $membro = Membro::find($_POST['id_membro']);
        $membro->delete();
        header('Location: /membros');
    }
}