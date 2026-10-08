<?php

namespace models;

class Funcao {
    public $id = 0;
    public $nome = '';

    static public function find(int $id_funcao): ?self {
        $sql = "SELECT 
            id_funcao as id,
            nm_funcao as nome
        FROM tb_funcao WHERE id_funcao = :id_funcao";

        $query = PDO->prepare($sql);
        $query->execute(['id_funcao' => $id_funcao]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $funcao = $query->fetch();

        return $funcao ?: null;
    }

    static public function load(): ?array {
        $sql = "SELECT 
            id_funcao as id,
            nm_funcao as nome
        FROM tb_funcao ORDER BY id_funcao";

        $query = PDO->prepare($sql);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        return $query->fetchAll();
    }
}