<?php

namespace models;

use ErrorException;
use models\Portifolio;

class Galeria {
    public $id = 0;
    public $arquivo = '';
    public $caminho = '';
    public $portifolioId = 0;
    public $portifolio = '';

    static public function find(int $id_galeria): ?self {
        $sql = "SELECT
            g.id_galeria as id,
            g.nm_arquivo as arquivo,
            g.nm_caminho_arquivo as caminho, 
            g.id_portifolio as portifolioId,
            p.nm_projeto as portifolio
        FROM tb_galeria as g
            JOIN tb_portifolio as p
                ON p.id_portifolio = g.id_portifolio
        WHERE g.id_galeria = :id_galeria";

        $query = PDO->prepare($sql);
        $query->execute(['id_galeria' => $id_galeria]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $galeria = $query->fetch();

        return $galeria ?: null;
    }

    static public function load(): ?array {
        $sql = "SELECT
            g.id_galeria as id,
            g.nm_arquivo as arquivo,
            g.nm_caminho_arquivo as caminho, 
            g.id_portifolio as portifolioId,
            p.nm_projeto as portifolio
        FROM tb_galeria as g
            JOIN tb_portifolio as p
                ON p.id_portifolio = g.id_portifolio
        ORDER BY g.id_galeria";

        $query = PDO->prepare($sql);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        return $query->fetchAll();
    }

    static public function findByPortifolio(int $id_portifolio): ?array {
        $sql = "SELECT
            g.id_galeria as id,
            g.nm_arquivo as arquivo,
            g.nm_caminho_arquivo as caminho, 
            g.id_portifolio as portifolioId,
            p.nm_projeto as portifolio
        FROM tb_galeria as g
            JOIN tb_portifolio as p
                ON p.id_portifolio = g.id_portifolio
        WHERE g.id_portifolio = :id_portifolio
        ORDER BY g.id_galeria";

        $query = PDO->prepare($sql);
        $query->execute(['id_portifolio' => $id_portifolio]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        return $query->fetchAll();
    }

    static public function validar($method) {
        $galeria = new self;
        $erros = [];

        if (isset($method)) {
            $action = $method['action'] ?? '';

            if (!isset($method['csrf_token']) || !csrfVerify($method['csrf_token'])) {
                throw new ErrorException('Token CSRF inválido', 403);
            }

            if (empty($method['id_galeria']) && $action === 'update') {
                throw new ErrorException('Id inválido', 403);
            }

            $portifolioId = $method['portifolio'] ?? $method['id_portifolio'] ?? 0;
            $arquivo = $method['arquivo'] ?? $method['nm_arquivo'] ?? '';

            if (empty($portifolioId)) {
                $erros[] = 'O portfólio é obrigatório';
            } elseif (!Portifolio::find((int) $portifolioId)) {
                throw new ErrorException('Portfólio inválido', 403);
            }

            if (empty($arquivo) && $action === 'save') {
                $erros[] = 'O nome do arquivo é obrigatório';
            }

            if (empty($erros)) {
                $galeria->id = $method['id_galeria'] ?? 0;
                $galeria->arquivo = $arquivo;
                $galeria->portifolioId = (int) $portifolioId;
            }

            return [
                'galeria' => $galeria,
                'erros' => $erros
            ];
        }

        return [
            'galeria' => $galeria,
            'erros' => $erros
        ];
    }

    public function save() {
        $sql = "INSERT INTO tb_galeria
            (nm_arquivo, nm_caminho_arquivo, id_portifolio)
        VALUES
            (:arquivo, :caminho, :portifolio)";

        $query = PDO->prepare($sql);
        $query->execute([
            'arquivo' => $this->arquivo,
            'caminho' => $this->caminho,
            'portifolio' => $this->portifolioId
        ]);

        return (int) PDO->lastInsertId();
    }

    public function update() {
        $sql = "UPDATE tb_galeria SET
            nm_arquivo = :arquivo,
            nm_caminho_arquivo = :caminho,
            id_portifolio = :portifolio
        WHERE id_galeria = :id";

        $query = PDO->prepare($sql);
        $query->execute([
            'arquivo' => $this->arquivo,
            'caminho' => $this->caminho,
            'portifolio' => $this->portifolioId,
            'id' => $this->id
        ]);

        return true;
    }

    public function delete() {
        $sql = "DELETE FROM tb_galeria WHERE id_galeria = :id";
        $query = PDO->prepare($sql);
        $query->execute(['id' => $this->id]);

        return true;
    }
}