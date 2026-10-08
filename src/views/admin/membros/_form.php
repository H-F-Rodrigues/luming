<?php
/**
 * Formulário compartilhado de membro (create e edit).
 * Campos = os que Membro::validar() e MembroController esperam:
 *   nome, email, senha, funcao, foto (arquivo)
 *   + id_membro (edição), action e csrf_token.
 *
 * @var string $formAcao   URL do action
 * @var bool   $formEdicao true na edição (envia PUT via _method)
 * @var array  $valores
 * @var array  $funcoes    models\Funcao[]
 * @var array  $erros      string[]
 */
$fotoAtual = $formEdicao ? adminFotoUrl($membro) : '';
?>
<form class="adm-form" action="<?= esc($formAcao) ?>" method="post" enctype="multipart/form-data">
    <?= createCsrf() ?>
    <input type="hidden" name="action" value="<?= $formEdicao ? 'update' : 'save' ?>">
    <?php if ($formEdicao): ?>
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="id_membro" value="<?= (int) $membro->id ?>">
    <?php endif ?>

    <?= adminErros($erros) ?>

    <section class="adm-fieldset">
        <h2 class="adm-fieldset-title">Dados do membro</h2>

        <label>Nome completo
            <input name="nome" required maxlength="100" placeholder="Nome do membro" autocomplete="off" value="<?= esc($valores['nome']) ?>">
        </label>
        <label>Função
            <select name="funcao" required>
                <option value="" disabled<?= empty($valores['funcao']) ? ' selected' : '' ?>>Selecione a função</option>
                <?php foreach ($funcoes as $funcao): ?>
                    <option value="<?= (int) $funcao->id ?>"<?= (int) $valores['funcao'] === (int) $funcao->id ? ' selected' : '' ?>><?= esc($funcao->nome) ?></option>
                <?php endforeach ?>
            </select>
        </label>
        <label>E-mail profissional
            <input name="email" type="email" required maxlength="150" placeholder="nome@luming.com.br" autocomplete="off" value="<?= esc($valores['email']) ?>">
        </label>
        <label>Senha
            <input name="senha" type="password" <?= $formEdicao ? '' : 'required ' ?>placeholder="<?= $formEdicao ? 'Deixe em branco para manter a atual' : 'Senha de acesso' ?>" autocomplete="new-password">
            <?php if ($formEdicao): ?><span class="adm-hint">Só preencha se quiser trocar a senha.</span><?php endif ?>
        </label>
    </section>

    <section class="adm-fieldset">
        <h2 class="adm-fieldset-title">Foto</h2>

        <?php if ($fotoAtual !== ''): ?>
            <div class="adm-current adm-field-full">
                <img class="adm-avatar-photo" src="<?= esc($fotoAtual) ?>" alt="Foto atual">
                <span>Foto atual. Envie outra para substituí-la.</span>
            </div>
        <?php endif ?>

        <label class="adm-field-full">Imagem
            <input name="foto" type="file" accept="image/png,image/jpeg,image/webp">
            <span class="adm-hint">PNG, JPG ou WEBP, até 2 MB. Opcional.</span>
        </label>
    </section>

    <div class="adm-form-actions">
        <a class="adm-btn adm-btn-secondary" href="<?= $formEdicao ? esc($formAcao) : '/admin/membros' ?>">Cancelar</a>
        <button class="adm-btn adm-btn-primary" type="submit"><?= $formEdicao ? 'Salvar alterações' : 'Cadastrar membro' ?></button>
    </div>
</form>
