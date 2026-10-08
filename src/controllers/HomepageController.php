<?php

namespace controllers;

use ErrorException;
use models\Cliente;
use models\Galeria;
use models\Membro;
use models\Mensagem;
use models\Portifolio;
use models\Ramo;
use models\Servico;

class HomepageController {
    // Imagens de demonstração do wireframe (usadas só quando o banco ainda não tem dados).
    private const IMG_NORTE  = 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Captura%20de%20tela%202026-09-28%20185506-4eQ6X7dxcEa9MFEIwJ8MwwsD832F7l.png';
    private const IMG_ORBITA = 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Captura%20de%20tela%202026-09-28%20185537-ZNqnhTGUyPiQWZtxDwFAb03FEbnhwD.png';
    private const IMG_LUMINA = 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Captura%20de%20tela%202026-09-28%20185528-Mt2Vi1mZ8kaNIuRwebZ2iQbJihTqw4.png';
    private const IMG_PULSE  = 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Captura%20de%20tela%202026-09-28%20185655-l0EQHXsMOmB9pjLiwiXQoSb6MH2OTR.png';

    /* ------------------------------------------------------------------
     * Rotas públicas
     * ------------------------------------------------------------------ */

    // GET /
    static public function makeHome(): void {
        $projetos = self::projetos();
        $equipe = self::equipe();

        makePage('homepage/index', [
            'title'       => 'Home',
            'navbar'      => true,
            'footer'      => true,
            'servicos'    => self::servicosDoBanco() ?: self::servicos(),
            'projetos'    => array_slice($projetos, 0, 4),
            'equipe'      => array_slice($equipe, 0, 4),
            'imagemSobre' => $projetos[0]['imagem'] ?? self::IMG_ORBITA,
            'processo'    => self::processo(),
            'motivos'     => self::motivos(),
            'depoimento'  => self::depoimento(),
            'faqs'        => self::faqs(),
        ]);
    }

    // GET /contato
    static public function makeContato(): void {
        self::renderContato([
            'enviado' => isset($_GET['enviado']),
        ]);
    }

    // POST /contato
    static public function saveContato(): void {
        // Mesmos campos/validadores usados em MensagemController::saveMensagem().
        $dados = [
            'csrf_token' => $_POST['csrf_token'] ?? '',
            'action'     => 'save', // definido no servidor (não confia no hidden do form)
            'nome'       => trim((string) ($_POST['nome'] ?? '')),
            'email'      => trim((string) ($_POST['email'] ?? '')),
            'telefone'   => trim((string) ($_POST['telefone'] ?? '')),
            'ramo'       => (int) ($_POST['ramo'] ?? 0),
            'projeto'    => trim((string) ($_POST['projeto'] ?? '')),
            'servico'    => (int) ($_POST['servico'] ?? 0),
            'descricao'  => trim((string) ($_POST['descricao'] ?? '')),
        ];

        $validacaoCliente = Cliente::validar($dados);
        $validacaoMensagem = Mensagem::validar($dados);
        $erros = array_merge($validacaoCliente['erros'], $validacaoMensagem['erros']);

        if (!empty($erros)) {
            self::renderContato([
                'erros' => $erros,
                'old'   => $dados,
            ]);
            return;
        }

        $cliente = $validacaoCliente['cliente'];
        $mensagem = $validacaoMensagem['mensagem'];
        $mensagem->clienteId = $cliente->save();
        $mensagem->save();

        header('Location: /contato?enviado=1');
        exit;
    }

    // GET /portfolio
    static public function makePortfolio(): void {
        $projetos = self::projetos();
        $anos = array_values(array_unique(array_column($projetos, 'ano')));
        rsort($anos);

        makePage('portfolio/index', [
            'title'    => 'Portfólio',
            'projetos' => $projetos,
            'anos'     => $anos,
        ]);
    }

    // GET /portfolio/{id}
    static public function makeProjeto(array $route, string $uri): void {
        $id = (int) basename($uri);
        $portifolio = $id > 0 ? Portifolio::find($id) : null;

        if (!$portifolio) {
            http_response_code(404);
            throw new ErrorException('Projeto não encontrado.', 404);
        }

        $imagem = self::capaUrl($portifolio);
        $midias = [['tipo' => 'image', 'src' => $imagem, 'alt' => 'Capa de ' . $portifolio->projeto]];

        foreach (Galeria::findByPortifolio($portifolio->id) ?? [] as $item) {
            $src = storageUrl($item->caminho, $item->arquivo);
            $video = (bool) preg_match('/\.(mp4|webm|ogv|mov)$/i', $item->arquivo);

            $midias[] = [
                'tipo' => $video ? 'video' : 'image',
                'src'  => $src,
                'alt'  => ($video ? 'Vídeo de ' : 'Imagem de ') . $portifolio->projeto,
            ];
        }

        $resultados = [];
        foreach (['Ideia' => $portifolio->ideia, 'Intenção' => $portifolio->intencao, 'Impacto' => $portifolio->impacto] as $rotulo => $texto) {
            if (trim((string) $texto) !== '') {
                $resultados[] = [$rotulo, $texto];
            }
        }

        makePage('portfolio/show', [
            'title'   => $portifolio->projeto,
            'projeto' => [
                'id'         => $portifolio->id,
                'titulo'     => $portifolio->projeto,
                'categoria'  => 'projeto · ' . self::ano($portifolio),
                'imagem'     => $imagem,
                'intro'      => mb_strimwidth((string) $portifolio->sobre, 0, 160, '…'),
                'descricao'  => (string) $portifolio->sobre,
                'resultados' => $resultados,
                'midias'     => $midias,
                'url'        => (string) $portifolio->url,
                'github'     => (string) $portifolio->github,
            ],
        ]);
    }

    // GET /servicos/{slug}
    static public function makeServico(array $route, string $uri): void {
        $slug = basename($uri);
        $doBanco = self::servicosDoBanco();

        if ($doBanco) {
            // Fonte de dados: banco. Projetos via tb_portifolio_servico e
            // membros via tb_funcao_servico — nunca os dados de demonstração.
            $servico = $doBanco[$slug] ?? null;

            if (!$servico) {
                http_response_code(404);
                throw new ErrorException('Serviço não encontrado.', 404);
            }

            $projetos = [];
            foreach (Servico::projetos((int) $servico['id']) as $item) {
                if (trim((string) $item->capa) === '') {
                    continue;
                }
                $projetos[] = [
                    'nome'      => $item->projeto,
                    'categoria' => 'Projeto · ' . self::ano($item),
                    'imagem'    => self::capaUrl($item),
                    'url'       => '/portfolio/' . (int) $item->id,
                ];
            }

            $membros = [];
            foreach (Servico::membros((int) $servico['id']) as $item) {
                $membros[] = [
                    'nome'   => $item->nome,
                    'funcao' => $item->funcao,
                    'imagem' => trim((string) $item->foto) !== '' ? storageUrl('membros', $item->foto) : '',
                ];
            }

            $servico['projetos'] = $projetos;
            $servico['membros'] = $membros;
        } else {
            // Só chega aqui se não houver NENHUM serviço no banco (ou ele estiver indisponível).
            $servico = self::servicos()[$slug] ?? null;

            if (!$servico) {
                http_response_code(404);
                throw new ErrorException('Serviço não encontrado.', 404);
            }

            foreach ($servico['projetos'] as &$demo) {
                $demo['url'] = '/portfolio';
            }
            unset($demo);
        }

        makePage('servicos/show', [
            'title'   => $servico['titulo'],
            'servico' => $servico,
        ]);
    }

    /* ------------------------------------------------------------------
     * Auxiliares
     * ------------------------------------------------------------------ */

    /**
     * Serviços cadastrados no banco (tb_servico), indexados pelo slug do nome.
     * Textos e ícone vêm do conteúdo editorial quando o nome é reconhecido.
     * Retorna [] se o banco não tiver serviços ou estiver indisponível.
     */
    static private function servicosDoBanco(): array {
        try {
            $lista = Servico::load() ?? [];
        } catch (\Throwable $e) {
            return [];
        }

        $servicos = [];
        foreach (array_values($lista) as $i => $item) {
            $slug = Servico::slug($item->nome);
            $editorial = self::editorial($slug);

            $servicos[$slug] = [
                'id'        => (int) $item->id,
                'numero'    => sprintf('%02d', $i + 1),
                'icone'     => $editorial['icone'] ?? 'lightbulb',
                'titulo'    => $item->nome,
                'texto'     => $editorial['texto'] ?? 'Soluções sob medida para o seu negócio, com estratégia e criatividade.',
                'lead'      => $editorial['lead'] ?? 'Conheça como a LUMING pode ajudar o seu negócio nesta área.',
                'descricao' => $editorial['descricao'] ?? 'Unimos estratégia, criatividade e tecnologia para entregar soluções sob medida, com processo próximo e transparente.',
            ];
        }

        return $servicos;
    }

    /**
     * Conteúdo editorial (texto/ícone) de um serviço, pelo slug ou por palavra-chave do nome.
     */
    static private function editorial(string $slug): ?array {
        $todos = self::servicos();

        if (isset($todos[$slug])) {
            return $todos[$slug];
        }

        $palavras = [
            'site'       => 'desenvolvimento-de-sites',
            'web'        => 'desenvolvimento-de-sites',
            'design'     => 'design-identidade-visual',
            'identidade' => 'design-identidade-visual',
            'audiovisual' => 'audiovisual-filmmaker',
            'filmmak'    => 'audiovisual-filmmaker',
            'video'      => 'audiovisual-filmmaker',
            'marketing'  => 'marketing-conteudo',
            'conteudo'   => 'marketing-conteudo',
            'solucoes'   => 'solucoes-digitais',
            'sistema'    => 'solucoes-digitais',
        ];

        foreach ($palavras as $palavra => $chave) {
            if (str_contains($slug, $palavra)) {
                return $todos[$chave];
            }
        }

        return null;
    }

    static private function renderContato(array $extra = []): void {
        makePage('contato/index', $extra + [
            'title'    => 'Contato',
            'ramos'    => Ramo::load() ?? [],
            'servicos' => Servico::load() ?? [],
            'erros'    => [],
            'old'      => [],
            'enviado'  => false,
        ]);
    }

    static private function ano(Portifolio $portifolio): string {
        $time = $portifolio->criadoEm ? strtotime((string) $portifolio->criadoEm) : false;
        return date('Y', $time ?: time());
    }

    static private function capaUrl(Portifolio $portifolio): string {
        return storageUrl((string) $portifolio->caminho, (string) $portifolio->capa);
    }

    /**
     * Projetos do portfólio (mais recentes primeiro).
     * Sem dados no banco, usa os projetos de demonstração do wireframe.
     */
    static private function projetos(): array {
        try {
            $lista = array_reverse(Portifolio::load() ?? []);
        } catch (\Throwable $e) {
            $lista = [];
        }

        $projetos = [];
        foreach ($lista as $item) {
            if (trim((string) $item->capa) === '') {
                continue;
            }
            $ano = self::ano($item);
            $projetos[] = [
                'id'        => $item->id,
                'titulo'    => $item->projeto,
                'categoria' => 'Projeto · ' . $ano,
                'ano'       => $ano,
                'imagem'    => self::capaUrl($item),
                'url'       => '/portfolio/' . (int) $item->id,
            ];
        }

        if (!empty($projetos)) {
            return $projetos;
        }

        return [
            ['id' => 0, 'titulo' => 'Norte Studio', 'categoria' => 'Branding & Digital', 'ano' => '2026', 'imagem' => self::IMG_NORTE,  'url' => '/portfolio'],
            ['id' => 0, 'titulo' => 'Órbita', 'categoria' => 'Web Design', 'ano' => '2026', 'imagem' => self::IMG_ORBITA, 'url' => '/portfolio'],
            ['id' => 0, 'titulo' => 'Lumina Films', 'categoria' => 'Audiovisual', 'ano' => '2025', 'imagem' => self::IMG_LUMINA, 'url' => '/portfolio'],
            ['id' => 0, 'titulo' => 'Pulse Lab', 'categoria' => 'Estratégia & Conteúdo', 'ano' => '2025', 'imagem' => self::IMG_PULSE,  'url' => '/portfolio'],
        ];
    }

    /**
     * Membros da equipe. Sem dados no banco, usa os de demonstração do wireframe.
     */
    static private function equipe(): array {
        try {
            $lista = Membro::load() ?? [];
        } catch (\Throwable $e) {
            $lista = [];
        }

        $equipe = [];
        foreach ($lista as $item) {
            $equipe[] = [
                'nome'   => $item->nome,
                'funcao' => $item->funcao,
                'foto'   => trim((string) $item->foto) !== '' ? storageUrl('membros', $item->foto) : '',
            ];
        }

        if (!empty($equipe)) {
            return $equipe;
        }

        return [
            ['nome' => 'Ana Martins', 'funcao' => 'Direção criativa', 'foto' => self::unsplash('photo-1551836022-d5d88e9218df', 600)],
            ['nome' => 'Lucas Rocha', 'funcao' => 'Desenvolvimento', 'foto' => self::unsplash('photo-1500648767791-00dcc994a43e', 600)],
            ['nome' => 'Bia Costa', 'funcao' => 'Design & estratégia', 'foto' => self::unsplash('photo-1531123897727-8f129e1688ce', 600)],
            ['nome' => 'Rafa Dias', 'funcao' => 'Audiovisual', 'foto' => self::unsplash('photo-1506794778202-cad84cf45f1d', 600)],
        ];
    }

    static private function unsplash(string $foto, int $largura): string {
        return "https://images.unsplash.com/{$foto}?auto=format&fit=crop&w={$largura}&q=85";
    }

    /**
     * Serviços (conteúdo editorial do wireframe). A chave é o slug usado em /servicos/{slug}.
     */
    static private function servicos(): array {
        $ana   = ['nome' => 'Ana Martins', 'funcao' => 'Direção criativa', 'imagem' => self::unsplash('photo-1551836022-d5d88e9218df', 400)];
        $lucas = ['nome' => 'Lucas Rocha', 'funcao' => 'Desenvolvimento', 'imagem' => self::unsplash('photo-1500648767791-00dcc994a43e', 400)];
        $bia   = ['nome' => 'Bia Costa', 'funcao' => 'Design & estratégia', 'imagem' => self::unsplash('photo-1531123897727-8f129e1688ce', 400)];
        $rafa  = ['nome' => 'Rafa Dias', 'funcao' => 'Audiovisual', 'imagem' => self::unsplash('photo-1506794778202-cad84cf45f1d', 400)];

        return [
            'desenvolvimento-de-sites' => [
                'numero' => '01', 'icone' => 'globe', 'titulo' => 'Desenvolvimento de Sites',
                'texto' => 'Sites rápidos, estratégicos e feitos para transformar visitas em oportunidades.',
                'lead' => 'Sites rápidos, estratégicos e feitos para transformar visitas em oportunidades.',
                'descricao' => 'Criamos experiências digitais que unem clareza, performance e personalidade. Do primeiro wireframe ao lançamento, cada decisão é pensada para aproximar sua marca das pessoas certas.',
                'projetos' => [
                    ['nome' => 'Órbita', 'categoria' => 'Web design', 'imagem' => self::IMG_ORBITA],
                    ['nome' => 'Norte Studio', 'categoria' => 'Branding & digital', 'imagem' => self::IMG_NORTE],
                ],
                'membros' => [$lucas, $bia],
            ],
            'design-identidade-visual' => [
                'numero' => '02', 'icone' => 'palette', 'titulo' => 'Design & Identidade Visual',
                'texto' => 'Marcas com presença, personalidade e um visual que fica na memória.',
                'lead' => 'Marcas com presença, personalidade e um visual que fica na memória.',
                'descricao' => 'Construímos sistemas visuais consistentes, flexíveis e prontos para fazer sua marca ser reconhecida em todos os pontos de contato.',
                'projetos' => [
                    ['nome' => 'Norte Studio', 'categoria' => 'Identidade visual', 'imagem' => self::IMG_NORTE],
                    ['nome' => 'Pulse Lab', 'categoria' => 'Estratégia & conteúdo', 'imagem' => self::IMG_PULSE],
                ],
                'membros' => [$ana, $bia],
            ],
            'audiovisual-filmmaker' => [
                'numero' => '03', 'icone' => 'film', 'titulo' => 'Audiovisual & Filmmaker',
                'texto' => 'Conteúdo que captura atenção e traduz a essência do seu negócio.',
                'lead' => 'Conteúdo que captura atenção e traduz a essência do seu negócio.',
                'descricao' => 'Do conceito à edição final, criamos narrativas audiovisuais com ritmo, intenção e uma estética que conversa com seu público.',
                'projetos' => [
                    ['nome' => 'Lumina Films', 'categoria' => 'Audiovisual', 'imagem' => self::IMG_LUMINA],
                ],
                'membros' => [$rafa],
            ],
            'marketing-conteudo' => [
                'numero' => '04', 'icone' => 'target', 'titulo' => 'Marketing & Conteúdo',
                'texto' => 'Estratégias criativas para sua marca se conectar com as pessoas certas.',
                'lead' => 'Estratégias criativas para sua marca se conectar com as pessoas certas.',
                'descricao' => 'Planejamos conteúdos e campanhas que transformam presença em conversa, relacionamento e crescimento.',
                'projetos' => [
                    ['nome' => 'Pulse Lab', 'categoria' => 'Estratégia & conteúdo', 'imagem' => self::IMG_PULSE],
                ],
                'membros' => [$ana],
            ],
            'solucoes-digitais' => [
                'numero' => '05', 'icone' => 'zap', 'titulo' => 'Soluções Digitais',
                'texto' => 'Automação e tecnologia para simplificar processos e acelerar resultados.',
                'lead' => 'Automação e tecnologia para simplificar processos e acelerar resultados.',
                'descricao' => 'Desenhamos ferramentas digitais sob medida para tirar tarefas repetitivas do caminho e abrir espaço para o que realmente importa.',
                'projetos' => [
                    ['nome' => 'Órbita', 'categoria' => 'Produto digital', 'imagem' => self::IMG_ORBITA],
                ],
                'membros' => [$lucas],
            ],
        ];
    }

    static private function processo(): array {
        return [
            ['01', 'CONHECEMOS', 'Entendemos sua empresa, seus objetivos e seu público.'],
            ['02', 'PLANEJAMOS', 'Definimos a melhor estratégia para o seu projeto.'],
            ['03', 'CRIAMOS', 'Desenvolvemos uma solução com propósito e precisão.'],
            ['04', 'AJUSTAMOS', 'Alinhamos todos os detalhes com você.'],
            ['05', 'ENTREGAMOS', 'Colocamos o projeto em prática e acompanhamos seu crescimento.'],
        ];
    }

    static private function motivos(): array {
        return [
            'Soluções personalizadas',
            'Atendimento próximo',
            'Design profissional',
            'Tecnologia atual',
            'Criatividade sem fórmula',
            'Foco no seu objetivo',
        ];
    }

    static private function depoimento(): array {
        return [
            'texto'    => 'A LUMING entendeu o que a gente queria dizer antes mesmo de conseguirmos explicar. O resultado foi uma marca que finalmente parece com a nossa ambição.',
            'nome'     => 'André Martins',
            'cargo'    => 'Fundador, Norte Studio',
            'iniciais' => 'AM',
        ];
    }

    static private function faqs(): array {
        return [
            ['Quanto custa um site?', 'Cada projeto é único. Depois de entendermos seus objetivos, montamos uma proposta sob medida para sua realidade.'],
            ['Quanto tempo leva?', 'O prazo depende do escopo, mas projetos costumam durar entre 3 e 8 semanas, com você acompanhando cada etapa.'],
            ['Vocês fazem manutenção?', 'Sim. Oferecemos planos de acompanhamento, evolução e suporte contínuo para sua presença digital.'],
            ['Vocês atendem empresas de outras cidades?', 'Atendemos clientes em todo o Brasil de forma remota, com reuniões e processos simples e próximos.'],
        ];
    }
}
