<?php

namespace models;

use ErrorException;
use models\Ramo;

class Cliente{
    public $id = 0;
    public $nome = '';
    public $email = '';
    public $telefone = '';
    public $ramoId = 0;
    public $ramo = '';

    static public function find(int $id_cliente): ?self {
        $sql = "SELECT
            c.id_cliente as id,
            c.nm_cliente as nome,
            c.ds_email as email,
            c.cd_telefone as telefone,
            r.id_ramo as ramoId,
            r.nm_ramo as ramo
        from tb_cliente as c
            join tb_ramo as r
                on c.id_ramo = r.id_ramo
                where c.id_cliente = :id";

        $query = PDO->prepare($sql);
        $query->execute(['id' => $id_cliente]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $cliente = $query->fetch();

        $cliente = self::atualizarMascaraTelefone($cliente);

        return $cliente ?: null;
    }

    static public function load(): ?array {
        $sql = "SELECT
            c.id_cliente as id,
            c.nm_cliente as nome,
            c.ds_email as email,
            c.cd_telefone as telefone,
            r.id_ramo as ramoId,
            r.nm_ramo as ramo
        from tb_cliente as c
            join tb_ramo as r
                on c.id_ramo = r.id_ramo";

        $query = PDO->prepare($sql);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);

        $clientes = self::atualizarMascaraTelefone($query->fetchAll());
        
        return $clientes;
    }

    static public function atualizarMascaraTelefone(array|self|null $clientes) {
        if (is_array($clientes)) {
            foreach ($clientes as $cliente) {
                $cliente->telefone = aplicarMascaraTelefone($cliente->telefone);
            }
        } elseif (!empty($clientes)) {
            $clientes->telefone = aplicarMascaraTelefone($clientes->telefone);
        }

        return $clientes;
    }

    static public function findByEmail(string $email_cliente): ?self {
        $sql = "SELECT * FROM tb_cliente WHERE ds_email = :email";

        $query = PDO->prepare($sql);
        $query->execute(['email' => $email_cliente]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $cliente = $query->fetch();

        return $cliente ?: null;
    }

    static public function findByTelefone(string $telefone_cliente): ?self {
        $sql = "SELECT * FROM tb_cliente WHERE cd_telefone = :telefone";

        $query = PDO->prepare($sql);
        $query->execute(['telefone' => $telefone_cliente]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $cliente = $query->fetch();

        return $cliente ?: null;
    }

    static public function validar($method) {
        $cliente = new self;
        $erros = [];
        if (isset($method)) {
            if (!isset($method['csrf_token']) || !csrfVerify($method['csrf_token'])) {
                throw new ErrorException('Token CSRF inválido', 403);
            }

            if (empty($method['id_cliente']) && $method['action'] === 'update') {
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

            if (empty($method['telefone']) && $method['action'] === 'save') {
                $erros[] = 'O telefone é obrigatório';
            } elseif (self::findByTelefone($method['telefone']) && $method['action'] === 'save') {
                $erros[] = 'Este telefone já está em uso';
            }

            if (empty($method['ramo'])) {
                $erros[] = 'O ramo é obrigatório';
            } elseif (!Ramo::find($method['ramo'])) {
                throw new ErrorException('Ramo inválido', 403);
            }

            if (empty($erros)) {
                $id = $method['id_cliente'] ?? 0;
                $nome = mb_strtoupper($method['nome']);
                $email = mb_strtolower($method['email']);
                $telefone = removerMascaraTelefone($method['telefone']);
                $ramoId = $method['ramo'];

                $cliente->id = $id;
                $cliente->nome = $nome;
                $cliente->email = $email;
                $cliente->telefone = $telefone;
                $cliente->ramoId = $ramoId;
            }
            return [
                'cliente' => $cliente,
                'erros' => $erros
            ];
        }
    }

    public function save() {
        $sql = "INSERT into tb_cliente
        (nm_cliente, ds_email, cd_telefone, id_ramo)
        values
        (:nome, :email, :telefone, :id_ramo)";

        $query = PDO->prepare($sql);
        $query->execute([
            'nome' => $this->nome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'id_ramo' => $this->ramoId
        ]);

        return (int) PDO->lastInsertId();
    }

    public function update() {
        $sql = "UPDATE tb_cliente set
            nm_cliente = :nome,
            ds_email = :email,
            cd_telefone = :telefone,
            id_ramo = :ramo_id
        where id_cliente = :id";

        $query = PDO->prepare($sql);
        $query->execute([
            'nome' => $this->nome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'ramo_id' => $this->ramoId,
            'id' => $this->id
        ]);
        
        return true;
    }

    public function delete() {
        $sql = "DELETE from tb_cliente where id_cliente = :id_cliente";
        $query = PDO->prepare($sql);
        $query->execute(['id_cliente' => $this->id]);

        return true;
    }
}