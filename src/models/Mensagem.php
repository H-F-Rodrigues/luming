<?php

namespace models;

use models\Servico;

use ErrorException;

class Mensagem {
    public $id = 0;
    public $projeto = '';
    public $descricao = '';
    public $servicoId = 0;
    public $servico = '';
    public $clienteId = 0;
    public $nomeCliente = '';
    public $emailCliente = '';
    public $telefoneCliente = '';

    static public function find(int $id_mensagem): ?self {
        $sql = "SELECT
            m.id_mensagem as id,
            m.nm_projeto as projeto,
            m.ds_projeto as descricao,
            s.id_servico as servicoId,
            s.nm_servico as servico,
            c.id_cliente as clienteId,
            c.nm_cliente as nomeCliente,
            c.ds_email as emailCliente,
            c.cd_telefone as telefoneCliente
        from tb_mensagem as m
            join tb_servico as s
            on s.id_servico = m.id_servico
                join tb_cliente as c
                on c.id_cliente = m.id_cliente
                    where m.id_mensagem = :id_mensagem";
        $query = PDO->prepare($sql);
        $query->execute(['id_mensagem' => $id_mensagem]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $cliente = $query->fetch();

        return $cliente ?: null;
    }

    static public function findByCliente(int $id_cliente) {
        $sql = "SELECT
            m.id_mensagem as id,
            m.nm_projeto as projeto,
            m.ds_projeto as descricao,
            s.id_servico as servicoId,
            s.nm_servico as servico,
            c.id_cliente as clienteId,
            c.nm_cliente as nomeCliente,
            c.ds_email as emailCliente,
            c.cd_telefone as telefoneCliente
        from tb_mensagem as m
            join tb_servico as s
            on s.id_servico = m.id_servico
                join tb_cliente as c
                on c.id_cliente = m.id_cliente
                    where c.id_cliente = :id_cliente";
        $query = PDO->prepare($sql);
        $query->execute(['id_cliente' => $id_cliente]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $cliente = $query->fetch();

        return $cliente ?: null;
    }

    static public function load() {
        $sql = "SELECT
            m.id_mensagem as id,
            m.nm_projeto as projeto,
            m.ds_projeto as descricao,
            s.id_servico as servicoId,
            s.nm_servico as servico,
            c.id_cliente as clienteId,
            c.nm_cliente as nomeCliente,
            c.ds_email as emailCliente,
            c.cd_telefone as telefoneCliente
        from tb_mensagem as m
            join tb_servico as s
            on s.id_servico = m.id_servico
                join tb_cliente as c
                on c.id_cliente = m.id_cliente ORDER BY id_mensagem";
        $query = PDO->prepare($sql);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        return $query->fetchAll();
    }

    static public function validar($method) {
        $mensagem = new self;
        $erros = [];
        if (isset($method)) {
            if (!isset($method['csrf_token']) || !csrfVerify($method['csrf_token'])) {
                throw new ErrorException('Token CSRF inválido', 403);
            }

            if (empty($method['id_mensagem']) && $method['action'] === 'update') {
                throw new ErrorException('Id inválido', 403);
            }

            if (empty($method['id_cliente']) && $method['action'] === 'update') {
                throw new ErrorException('Id inválido', 403);
            }

            if (empty($method['projeto'])  && $method['action'] === 'save') {
                $erros[] = 'O nome do projeto é obrigatório';
            }

            if (empty($method['servico'])  && $method['action'] === 'save') {
                $erros[] = 'O tipo de serviço é obrigatório';
            } elseif (!Servico::find($method['servico'])) {
                throw new ErrorException('Servico inválido', 403);
            }

            if (empty($erros)) {
                $id = $method['id_mensagem'] ?? 0;
                $projeto = mb_strtoupper($method['projeto']);
                $descricao = $method['descricao'];
                $servicoId = $method['servico'];
                $clienteId = $method['id_cliente'] ?? 0;

                $mensagem->id = $id;
                $mensagem->projeto = $projeto;
                $mensagem->descricao = $descricao;
                $mensagem->servicoId = $servicoId;
                $mensagem->clienteId = $clienteId;
            }
            return [
                'mensagem' => $mensagem,
                'erros' => $erros
            ];
        }
    }
    
    public function save() {
        $sql = "INSERT into tb_mensagem
        (nm_projeto, ds_projeto, id_servico, id_cliente)
        values
        (:projeto, :descricao, :servico, :cliente)";
        
        $query = PDO->prepare($sql);
        $query->execute([
            'projeto' => $this->projeto,
            'descricao' => $this->descricao,
            'servico' => $this->servicoId,
            'cliente' => $this->clienteId
        ]);

        return PDO->lastInsertId();
    }

    public function update() {
        $sql = "UPDATE tb_mensagem set
            nm_projeto = :projeto,
            ds_projeto = :descricao,
            id_servico = :servico,
            id_cliente = :cliente
        where id_mensagem = :id";

        $query = PDO->prepare($sql);
        $query->execute([
            'projeto' => $this->projeto,
            'descricao' => $this->descricao,
            'servico' => $this->servicoId,
            'cliente' => $this->clienteId,
            'id' => $this->id
        ]);

        return true;
    }

    public function delete() {
        $sql = "DELETE from tb_mensagem where id_mensagem = :id";

        $query = PDO->prepare($sql);
        $query->execute(['id' => $this->id]);

        return true;
    }
}