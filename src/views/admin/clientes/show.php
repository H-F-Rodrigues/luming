<?php
/**
 * Visualização de um cliente (GET /admin/clientes/{id}).
 *
 * @var models\Cliente $cliente
 * @var array          $mensagens models\Mensagem[] enviadas por este cliente
 */
$mensagens ??= [];
$nomeCliente = adminNome($cliente->nome);
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <div class="adm-section-row">
            <a class="adm-back" href="/admin/clientes">← voltar para clientes</a>
            <div class="adm-btn-group">
                <a class="adm-btn adm-btn-secondary" href="/admin/clientes/<?= (int) $cliente->id ?>/editar"><?= iconAdmin('pencil', 15) ?> Editar</a>
                <button type="button" class="adm-btn adm-btn-danger" data-delete data-action="/admin/clientes/<?= (int) $cliente->id ?>" data-nome="<?= esc($nomeCliente) ?>"><?= iconAdmin('trash-2', 15) ?> Excluir</button>
            </div>
        </div>

        <div class="adm-detail">
            <section class="adm-card">
                <h2 class="adm-card-title">Dados do cliente</h2>
                <dl class="adm-dl">
                    <div><dt>Nome</dt><dd><?= esc($nomeCliente) ?></dd></div>
                    <div><dt>Ramo</dt><dd><?= esc($cliente->ramo) ?></dd></div>
                    <div><dt>E-mail</dt><dd><a href="mailto:<?= esc($cliente->email) ?>"><?= esc($cliente->email) ?></a></dd></div>
                    <div><dt>Telefone</dt><dd><?= esc($cliente->telefone) ?></dd></div>
                </dl>
            </section>

            <section class="adm-card">
                <h2 class="adm-card-title">Mensagens (<?= count($mensagens) ?>)</h2>
                <?php if (empty($mensagens)): ?>
                    <p class="adm-prose">Este cliente ainda não enviou mensagens.</p>
                <?php else: ?>
                    <div class="adm-list">
                        <?php foreach ($mensagens as $mensagem): ?>
                            <article class="adm-row">
                                <div class="adm-row-info">
                                    <span class="adm-pill"><?= esc($mensagem->servico) ?></span>
                                    <h3><a href="/admin/mensagens/<?= (int) $mensagem->id ?>"><?= esc(adminNome($mensagem->projeto)) ?></a></h3>
                                </div>
                            </article>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </section>
        </div>
    </div>
</main>
<?php require COMPONENTS . 'confirm.php'; ?>
