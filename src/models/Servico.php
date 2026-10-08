<?php

namespace models;

class Servico {
    public $id = 0;
    public $nome = '';

    static public function find(int $id_servico): ?self {
        $sql = "SELECT 
            id_servico as id,
            nm_servico as nome
        FROM tb_servico WHERE id_servico = :id_servico";

        $query = PDO->prepare($sql);
        $query->execute(['id_servico' => $id_servico]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $servico = $query->fetch();

        return $servico ?: null;
    }

    static public function load(): ?array {
        $sql = "SELECT 
            id_servico as id,
            nm_servico as nome
        FROM tb_servico ORDER BY id_servico";

        $query = PDO->prepare($sql);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        return $query->fetchAll();
    }

    /**
     * Slug usado nas URLs públicas (/servicos/{slug}).
     * Ex.: "Design & Identidade Visual" => "design-identidade-visual"
     */
    static public function slug(string $nome): string {
        $texto = strtr(mb_strtolower($nome, 'UTF-8'), [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
        ]);
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);

        return trim((string) $texto, '-');
    }

    static public function findBySlug(string $slug): ?self {
        foreach (self::load() ?? [] as $servico) {
            if (self::slug($servico->nome) === $slug) {
                return $servico;
            }
        }

        return null;
    }

    /**
     * Projetos do portfólio ligados a este serviço (tb_portifolio_servico),
     * do mais recente para o mais antigo.
     *
     * @return Portifolio[]
     */
    static public function projetos(int $id_servico): array {
        $sql = "SELECT
            p.id_portifolio as id,
            p.nm_projeto as projeto,
            p.ds_projeto as sobre,
            p.ds_ideia as ideia,
            p.ds_intencao as intencao,
            p.ds_impacto as impacto,
            p.nm_capa as capa,
            p.nm_caminho_capa as caminho,
            p.ds_url as url,
            p.ds_url_github as github,
            p.created_at as criadoEm
        FROM tb_portifolio AS p
            JOIN tb_portifolio_servico AS ps
            ON ps.id_portifolio = p.id_portifolio
        WHERE ps.id_servico = :id_servico
        ORDER BY p.id_portifolio DESC";

        $query = PDO->prepare($sql);
        $query->execute(['id_servico' => $id_servico]);
        $query->setFetchMode(\PDO::FETCH_CLASS, Portifolio::class);
        return $query->fetchAll();
    }

    /**
     * Membros cuja função atende a este serviço (tb_funcao_servico).
     * A senha nunca é selecionada.
     *
     * @return Membro[]
     */
    static public function membros(int $id_servico): array {
        $sql = "SELECT
            m.id_membro as id,
            m.nm_membro as nome,
            m.ds_email as email,
            m.nm_foto as foto,
            m.id_funcao as funcaoId,
            f.nm_funcao as funcao,
            m.created_at as criadoEm
        FROM tb_membro AS m
            JOIN tb_funcao AS f
            ON f.id_funcao = m.id_funcao
                JOIN tb_funcao_servico AS fs
                ON fs.id_funcao = f.id_funcao
        WHERE fs.id_servico = :id_servico
        ORDER BY m.nm_membro";

        $query = PDO->prepare($sql);
        $query->execute(['id_servico' => $id_servico]);
        $query->setFetchMode(\PDO::FETCH_CLASS, Membro::class);
        return $query->fetchAll();
    }
}
