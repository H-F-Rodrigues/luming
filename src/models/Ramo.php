<?php

namespace models;

class Ramo {
    public $id = 0;
    public $nome = '';

    static public function find(int $id_ramo): ?self {
        $sql = "SELECT
            id_ramo as id,
            nm_ramo as nome
        FROM tb_ramo WHERE id_ramo = :id_ramo";

        $query = PDO->prepare($sql);
        $query->execute(['id_ramo' => $id_ramo]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $ramo = $query->fetch();

        return $ramo ?: null;
    }

    static public function load(): ?array {
        $sql = "SELECT
            id_ramo as id,
            nm_ramo as nome
        FROM tb_ramo ORDER BY id_ramo";

        $query = PDO->prepare($sql);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        return $query->fetchAll();
    }
}