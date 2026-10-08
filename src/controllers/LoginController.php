<?php

namespace controllers;

use models\Membro;

class LoginController {
    // GET /login
    static public function makeLogin() {
        if (isset($_SESSION['membro'])) {
            header('Location: /dashboard');
            exit;
        }
        makePage('login/index', [
            'title' => 'Login',
            'erros' => [],
            'email' => '',
        ]);
    }

    // POST /login
    static public function login() {
        $membro = Membro::findByEmail(mb_strtolower($_POST['email']));
        $erros = [];
        if ($membro) {
            if (password_verify($_POST['senha'], $membro->senha)) {
                $_SESSION['membro'] = $membro->id;
            } else {
                $erros[] = 'Senha inválida';
            }
        } else {
            $erros[] = 'Membro não encontrado';
        }

        if (!empty($erros)) {
            makePage('login/index', [
                'title' => 'Login',
                'erros' => $erros,
                'email' => $_POST['email'] ?? '',
            ]);
            return;
        }
        header('Location: /dashboard');
        return true;
    }

    static public function logout() {
        if (isset($_SESSION['membro'])) {
            unset($_SESSION['membro']);
            header('Location: /');
        }
        return;
    }
}
