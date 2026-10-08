<?php

namespace models;

use ErrorException;
use models\Funcao;

class Membro {
    public $id = 0;
    public $nome = '';
    public $sobre = '';
    public $email = '';
    public $senha = '';
    public $foto = '';
    public $funcaoId = '';
    public $funcao = '';
    public $criadoEm = null;

    static public function find(int $id_membro): ?self {
        $sql = "SELECT 
            m.id_membro as id,
            m.nm_membro as nome,
            m.ds_sobre as sobre,
            m.ds_email as email,
            m.ds_senha as senha,
            m.nm_foto as foto,
            m.id_funcao as funcaoId,
            f.nm_funcao as funcao,
            m.created_at as criadoEm
        FROM tb_membro AS m
            JOIN tb_funcao AS f
            ON f.id_funcao = m.id_funcao 
                WHERE id_membro = :id_membro";

        $query = PDO->prepare($sql);
        $query->execute(['id_membro' => $id_membro]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $membro = $query->fetch();

        return $membro ?: null;
    }

    static public function load(): ?array {
        $sql = "SELECT 
            m.id_membro as id,
            m.nm_membro as nome,
            m.ds_sobre as sobre,
            m.ds_email as email,
            m.ds_senha as senha,
            m.nm_foto as foto,
            m.id_funcao as funcaoId,
            f.nm_funcao as funcao,
            m.created_at as criadoEm
        FROM tb_membro AS m
            JOIN tb_funcao AS f
            ON f.id_funcao = m.id_funcao";

        $query = PDO->prepare($sql);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        return $query->fetchAll();
    }
    
    static public function findByEmail(string $email_cliente): ?self {
        $sql = "SELECT 
            m.id_membro as id,
            m.nm_membro as nome,
            m.ds_sobre as sobre,
            m.ds_email as email,
            m.ds_senha as senha,
            m.nm_foto as foto,
            m.id_funcao as funcaoId,
            f.nm_funcao as funcao,
            m.created_at as criadoEm
        FROM tb_membro AS m
            JOIN tb_funcao AS f
            ON f.id_funcao = m.id_funcao
            WHERE ds_email = :email";

        $query = PDO->prepare($sql);
        $query->execute(['email' => $email_cliente]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $cliente = $query->fetch();

        return $cliente ?: null;
    }

    static public function validar($method): ?array {
        $erros = [];
        $membro = new self;
        if (isset($method)) {
            if (!isset($method['csrf_token']) || !csrfVerify($method['csrf_token'])) {
                throw new ErrorException('Token CSRF inválido', 403);
            }

            if (empty($method['id_membro']) && $method['action'] === 'update') {
                throw new ErrorException('Id inválido', 403);
            }
            
            if (empty($method['nome'])  && $method['action'] === 'save') {
                $erros[] = 'O nome é obrigatório';
            }

            if (empty($method['email']) && $method['action'] === 'save') {
                $erros[] = 'O email é obrigatório';
            } elseif (self::findByEmail($method['email']) && $method['action'] === 'save') {
                $erros[] = 'Este email já está em uso';
            }

            if (empty($method['senha']) && $method['action'] === 'save') {
                $erros[] = 'A senha é obrigatória';
            }

            if (empty($method['funcao'])) {
                $erros[] = 'A função é obrigatória';
            } elseif (!Funcao::find($method['funcao'])) {
                throw new ErrorException('Função inválida', 403);
            }

            if (empty($erros)) {
                $id = $method['id_membro'] ?? 0;
                $nome = mb_strtoupper($method['nome']);
                $email = mb_strtolower($method['email']);
                $senha = strpos($method['senha'], '$2y$') === 0 ? $method['senha'] : password_hash($method['senha'], PASSWORD_BCRYPT);
                $funcaoId = $method['funcao'];

                $membro = new Membro();

                $membro->id = $id;
                $membro->nome = $nome;
                $membro->email = $email;
                $membro->senha = $senha;
                $membro->funcaoId = $funcaoId;
            }
        }
        return [
            'membro' => $membro,
            'erros' => $erros
        ];
    }

    public function save() {
        $sql = "INSERT INTO tb_membro
        (nm_membro, ds_email, ds_senha, nm_foto, id_funcao)
        VALUES
        (:nome, :email, :senha, :foto, :funcao)";

        $query = PDO->prepare($sql);
        $query->execute([
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha,
            'foto' => $this->foto,
            'funcao' => $this->funcaoId
        ]);

        return (int) PDO->lastInsertId();
    }

    public function update() {
        $sql = "UPDATE tb_membro SET
            nm_membro = :nome,
            ds_email = :email,
            ds_senha = :senha,
            nm_foto = :foto,
            id_funcao = :funcao
        WHERE id_membro = :id_membro";

        $query = PDO->prepare($sql);
        $query->execute([
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha,
            'foto' => $this->foto,
            'funcao' => $this->funcaoId,
            'id_membro' => $this->id
        ]);

        return true;
    }

    public function delete() {
        $sql = "DELETE FROM tb_membro WHERE id_membro = :id";
        $query = PDO->prepare($sql);
        $query->execute(['id' => $this->id]);
        return true;
    }
}