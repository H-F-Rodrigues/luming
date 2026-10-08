<?php

namespace models;

class PortifolioServico {
    public $idServico = 0;
    public $idPortifolio = 0;

    public function save() {
        $sql = "INSERT into tb_portifolio_servico
        (id_servico, id_portifolio)
        values
        (:idServico, :idPortifolio)";

        $query = PDO->prepare($sql);
        $query->execute([
            'idServico' => $this->idServico,
            'idPortifolio' => $this->idPortifolio
        ]);

        return true;
    }

    /**
     * Serviço vinculado a um projeto do portfólio (null se não houver vínculo).
     */
    static public function findByPortifolio(int $idPortifolio): ?self {
        $sql = "SELECT
            id_servico as idServico,
            id_portifolio as idPortifolio
        FROM tb_portifolio_servico WHERE id_portifolio = :idPortifolio LIMIT 1";

        $query = PDO->prepare($sql);
        $query->execute(['idPortifolio' => $idPortifolio]);
        $query->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $vinculo = $query->fetch();

        return $vinculo ?: null;
    }

    /**
     * Remove todos os vínculos de serviço de um projeto.
     */
    static public function deleteByPortifolio(int $idPortifolio): bool {
        $sql = "DELETE FROM tb_portifolio_servico WHERE id_portifolio = :idPortifolio";

        $query = PDO->prepare($sql);
        $query->execute(['idPortifolio' => $idPortifolio]);

        return true;
    }
}
