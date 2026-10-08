<?php
/**
 * Visualização de um membro (GET /admin/membros/{id}).
 * A senha nunca é exibida.
 *
 * @var models\Membro $membro
 */
$nomeMembro = adminNome($membro->nome);
$foto = adminFotoUrl($membro);
$ehVoce = (int) $membro->id === (int) ($_SESSION['membro'] ?? 0);
?>
<main class="adm-main">
    <?php require COMPONENTS . 'topbar.php'; ?>

    <div class="adm-content">
        <div class="adm-section-row">
            <a class="adm-back" href="/admin/membros">← voltar para a equipe</a>
            <?php if ($ehVoce): ?>
                <div class="adm-btn-group">
                    <a class="adm-btn adm-btn-secondary" href="/admin/membros/<?= (int) $membro->id ?>/editar"><?= iconAdmin('pencil', 15) ?> Editar</a>
                    <button type="button" class="adm-btn adm-btn-danger" data-delete data-action="/admin/membros/<?= (int) $membro->id ?>" data-nome="<?= esc($nomeMembro) ?> (a sua conta)"><?= iconAdmin('trash-2', 15) ?> Excluir minha conta</button>
                </div>
            <?php endif ?>
        </div>

        <section class="adm-card">
            <div class="adm-profile">
                <?php if ($foto !== ''): ?>
                    <img class="adm-avatar xl adm-avatar-photo" src="<?= esc($foto) ?>" alt="Foto de <?= esc($nomeMembro) ?>">
                <?php else: ?>
                    <span class="adm-avatar xl purple"><?= esc(adminIniciais($nomeMembro)) ?></span>
                <?php endif ?>
                <div>
                    <span class="adm-pill"><?= esc($membro->funcao) ?><?= $ehVoce ? ' · você' : '' ?></span>
                    <h2 class="adm-section-title"><?= esc($nomeMembro) ?></h2>
                </div>
            </div>

            <dl class="adm-dl">
                <div><dt>E-mail</dt><dd><a href="mailto:<?= esc($membro->email) ?>"><?= esc($membro->email) ?></a></dd></div>
                <div><dt>Função</dt><dd><?= esc($membro->funcao) ?></dd></div>
                <div><dt>Na equipe desde</dt><dd><?= esc(adminData($membro->criadoEm)) ?></dd></div>
            </dl>
        </section>
    </div>
</main>
<?php require COMPONENTS . 'confirm.php'; ?>
