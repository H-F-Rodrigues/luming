<?php

namespace models;

class Ramo {
    public $id = 0;
    public $nome = '';

    public function __construct($nome, $id) {
        $this->nome = $nome;
        $this->id = $id;
    }

    static public function find(int $id_ramo): ?self {
        $sql = "SELECT * FROM tb_ramo WHERE id_ramo = :id_ramo";

        $query = PDO->prepare($sql);
        $query->execute(['id_ramo' => $id_ramo]);
        $dados = $query->fetch(\PDO::FETCH_ASSOC);
        if (!$dados) {
            return null;
        }
        return new self (
            $dados['nm_ramo'],
            $dados['id_ramo']
        );
    }

    static public function load(): ?array {
        $sql = "SELECT * FROM tb_ramo ORDER BY id_ramo";

        $query = PDO->prepare($sql);
        $query->execute();
        $dados = $query->fetchAll(\PDO::FETCH_ASSOC);
        $funcoes = [];
        foreach($dados as $ramo) {
            $funcoes[] = new self(
                $ramo['nm_ramo'],
                $ramo['id_ramo']
            );
        }

        return $funcoes;
    }
}