<?php
/**
 * Visualização de uma mensagem (GET /admin/mensagens/{id}).
 *
 * @var models\Mensagem $mensagem
 */
$projeto = adminNome($mensagem->projeto);
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <div class="adm-section-row">
            <a class="adm-back" href="/admin/mensagens">← voltar para mensagens</a>
            <div class="adm-btn-group">
                <a class="adm-btn adm-btn-secondary" href="/admin/mensagens/<?= (int) $mensagem->id ?>/editar"><?= iconAdmin('pencil', 15) ?> Editar</a>
                <button type="button" class="adm-btn adm-btn-danger" data-delete data-action="/admin/mensagens/<?= (int) $mensagem->id ?>" data-nome="<?= esc($projeto) ?>"><?= iconAdmin('trash-2', 15) ?> Excluir</button>
            </div>
        </div>

        <div class="adm-detail">
            <section class="adm-card">
                <h2 class="adm-card-title">Mensagem</h2>
                <dl class="adm-dl">
                    <div><dt>Projeto</dt><dd><?= esc($projeto) ?></dd></div>
                    <div><dt>Serviço de interesse</dt><dd><?= esc($mensagem->servico) ?></dd></div>
                    <div class="full"><dt>Descrição</dt><dd><p class="adm-prose"><?= esc($mensagem->descricao !== '' ? $mensagem->descricao : '—') ?></p></dd></div>
                </dl>
            </section>

            <section class="adm-card">
                <h2 class="adm-card-title">Cliente</h2>
                <dl class="adm-dl">
                    <div class="full"><dt>Nome</dt><dd><a href="/admin/clientes/<?= (int) $mensagem->clienteId ?>"><?= esc(adminNome($mensagem->nomeCliente)) ?></a></dd></div>
                    <div class="full"><dt>E-mail</dt><dd><a href="mailto:<?= esc($mensagem->emailCliente) ?>"><?= esc($mensagem->emailCliente) ?></a></dd></div>
                    <div class="full"><dt>Telefone</dt><dd><?= esc($mensagem->telefoneCliente) ?></dd></div>
                </dl>
            </section>
        </div>
    </div>
</main>
<?php require COMPONENTS . 'confirm.php'; ?>
