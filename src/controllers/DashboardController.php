<?php

namespace controllers;

use ErrorException;
use models\Cliente;
use models\Membro;
use models\Mensagem;
use models\Portifolio;

class DashboardController {
    /* ------------------------------------------------------------------
     * Rotas do dashboard (todas sob /admin; o login é exigido pelo router)
     * ------------------------------------------------------------------ */

    // GET /dashboard  (o LoginController redireciona para cá após o login)
    static public function redirectAdmin(): void {
        header('Location: /admin');
        exit;
    }

    // GET /admin
    static public function makeDashboard(): void {
        $usuario = adminUser();
        $mensagens = Mensagem::load() ?? [];
        $clientes = Cliente::load() ?? [];
        $membros = Membro::load() ?? [];
        $projetos = Portifolio::load() ?? [];

        makePage('admin/dashboard', [
            'layout'             => 'dashboard',
            'title'              => 'Visão geral',
            'secao'              => 'Visão geral',
            'saudacao'           => self::saudacao(),
            'primeiroNome'       => $usuario ? (string) strtok(adminNome($usuario->nome), ' ') : '',
            'stats'              => [
                ['Projetos no portfólio', count($projetos),  'folder-kanban',  '/admin/portfolio'],
                ['Membros da equipe',     count($membros),   'user',           '/admin/membros'],
                ['Mensagens recebidas',   count($mensagens), 'message-square', '/admin/mensagens'],
                ['Clientes',              count($clientes),  'users',          '/admin/clientes'],
            ],
            // Listas vêm em ordem de id; invertemos para mostrar os mais recentes primeiro.
            'mensagensRecentes'  => array_slice(array_reverse($mensagens), 0, 5),
            'projetosRecentes'   => array_slice(array_reverse($projetos), 0, 3),
        ]);
    }

    // POST /admin/sair
    static public function logout(): void {
        if (!csrfVerify()) {
            throw new ErrorException('Token CSRF inválido', 403);
        }

        LoginController::logout();
        header('Location: /');
        exit;
    }

    /* ------------------------------------------------------------------
     * Auxiliares
     * ------------------------------------------------------------------ */

    static private function saudacao(): string {
        $hora = (int) date('G');

        return match (true) {
            $hora < 12 => 'bom dia',
            $hora < 18 => 'boa tarde',
            default    => 'boa noite',
        };
    }
}
