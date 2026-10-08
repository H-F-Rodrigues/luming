<?php

namespace models;

use ErrorException;

class Portifolio {
    public $id = 0;
    public $projeto = '';
    public $sobre = '';
    public $ideia = '';
    public $intencao = '';
    public $impacto = '';
    public $capa = '';
    public $caminho = '';
    public $url = '';
    public $github = '';
    public $criadoEm = null;

    static public function find(int $id_portifolio): ?self {
        $sql = "SELECT
            id_portifolio as id,
            nm_projeto as projeto,
            ds_projeto as sobre,
            ds_ideia as ideia,
            ds_intencao as intencao,
            ds_impacto as impacto,
            nm_capa as capa,
            nm_caminho_capa as caminho,
            ds_url as url,
            ds_url_github as github,
            created_at as criadoEm
        FROM tb_portifolio
        WHERE id_portifolio = :id_portifolio";

        $query = PDO->prepare($sql);
        $query->execute(['id_portifolio' => $id_portifolio]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $portifolio = $query->fetch();

        return $portifolio ?: null;
    }

    static public function load(): ?array {
        $sql = "SELECT
            id_portifolio as id,
            nm_projeto as projeto,
            ds_projeto as sobre,
            ds_ideia as ideia,
            ds_intencao as intencao,
            ds_impacto as impacto,
            nm_capa as capa,
            nm_caminho_capa as caminho,
            ds_url as url,
            ds_url_github as github,
            created_at as criadoEm
        FROM tb_portifolio
        ORDER BY id_portifolio";

        $query = PDO->prepare($sql);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        return $query->fetchAll();
    }

    static public function validar($method) {
        $portifolio = new self;
        $erros = [];

        if (isset($method)) {
            $action = $method['action'] ?? '';

            if (!isset($method['csrf_token']) || !csrfVerify($method['csrf_token'])) {
                throw new ErrorException('Token CSRF inválido', 403);
            }

            if (empty($method['id_portifolio']) && $action === 'update') {
                throw new ErrorException('Id inválido', 403);
            }

            if (empty($method['projeto']) && $action === 'save') {
                $erros[] = 'O nome do projeto é obrigatório';
            }

            if (empty($erros)) {
                $portifolio->id = $method['id_portifolio'] ?? 0;
                $portifolio->projeto = mb_strtoupper($method['projeto'] ?? '');
                $portifolio->sobre = $method['sobre'] ?? $method['descricao'] ?? '';
                $portifolio->ideia = $method['ideia'] ?? '';
                $portifolio->intencao = $method['intencao'] ?? '';
                $portifolio->impacto = $method['impacto'] ?? '';
                $portifolio->capa = $method['capa'] ?? '';
                $portifolio->url = $method['url'] ?? '';
                $portifolio->github = $method['github'] ?? '';
            }

            return [
                'res'        => empty($erros),
                'portifolio' => $portifolio,
                'erros' => $erros
            ];
        }

        return [
            'res'        => empty($erros),
            'portifolio' => $portifolio,
            'erros' => $erros
        ];
    }

    public function save() {
        $sql = "INSERT INTO tb_portifolio
            (nm_projeto, ds_projeto, ds_ideia, ds_intencao, ds_impacto, nm_capa, nm_caminho_capa, ds_url, ds_url_github)
        VALUES
            (:projeto, :sobre, :ideia, :intencao, :impacto, :capa, :caminho, :url, :github)";

        $query = PDO->prepare($sql);
        $query->execute([
            'projeto' => $this->projeto,
            'sobre' => $this->sobre,
            'ideia' => $this->ideia,
            'intencao' => $this->intencao,
            'impacto' => $this->impacto,
            'capa' => $this->capa,
            'caminho' => $this->caminho,
            'url' => $this->url,
            'github' => $this->github
        ]);

        return (int) PDO->lastInsertId();
    }

    public function update() {
        $sql = "UPDATE tb_portifolio SET
            nm_projeto = :projeto,
            ds_projeto = :sobre,
            ds_ideia = :ideia,
            ds_intencao = :intencao,
            ds_impacto = :impacto,
            nm_capa = :capa,
            nm_caminho_capa = :caminho,
            ds_url = :url,
            ds_url_github = :github
        WHERE id_portifolio = :id";

        $query = PDO->prepare($sql);
        $query->execute([
            'projeto' => $this->projeto,
            'sobre' => $this->sobre,
            'ideia' => $this->ideia,
            'intencao' => $this->intencao,
            'impacto' => $this->impacto,
            'capa' => $this->capa,
            'caminho' => $this->caminho,
            'url' => $this->url,
            'github' => $this->github,
            'id' => $this->id
        ]);

        return true;
    }

    public function delete() {
        $sql = "DELETE FROM tb_portifolio WHERE id_portifolio = :id";
        $query = PDO->prepare($sql);
        $query->execute(['id' => $this->id]);

        return true;
    }
}