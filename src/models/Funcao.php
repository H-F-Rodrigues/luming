<?php

namespace models;

class Funcao {
    public $id = 0;
    public $nome = '';

    public function __construct($nome, $id) {
        $this->nome = $nome;
        $this->id = $id;
    }

    static public function find(int $id_funcao): ?self {
        $sql = "SELECT * FROM tb_funcao WHERE id_funcao = :id_funcao";

        $query = PDO->prepare($sql);
        $query->execute(['id_funcao' => $id_funcao]);
        $dados = $query->fetch(\PDO::FETCH_ASSOC);
        if (!$dados) {
            return null;
        }
        return new self (
            $dados['nm_funcao'],
            $dados['id_funcao']
        );
    }

    static public function load(): ?array {
        $sql = "SELECT * FROM tb_funcao ORDER BY id_funcao";

        $query = PDO->prepare($sql);
        $query->execute();
        $dados = $query->fetchAll(\PDO::FETCH_ASSOC);
        $funcoes = [];
        foreach($dados as $funcao) {
            $funcoes[] = new self(
                $funcao['nm_funcao'],
                $funcao['id_funcao']
            );
        }

        return $funcoes;
    }
}