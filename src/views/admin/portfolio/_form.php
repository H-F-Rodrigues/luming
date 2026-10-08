<?php
/**
 * Formulário compartilhado de projeto (create e edit).
 * Campos = os que Portifolio::validar() e PortifolioController esperam:
 *   projeto, servico, sobre, ideia, intencao, impacto, url, github,
 *   capa (arquivo), midias[] (arquivos)
 *   + id_portifolio (edição), action e csrf_token.
 *
 * @var string $formAcao   URL do action
 * @var bool   $formEdicao true na edição (envia PUT via _method)
 * @var array  $valores
 * @var int    $totalMidias mídias atuais da galeria (edição)
 * @var array  $erros      string[]
 * @var array $servicos Servico
 */
$capaAtual = $formEdicao ? adminCapaUrl($portifolio) : '';
$totalMidias ??= 0;
$servicos ??= [];
// Serviço selecionado: valor devolvido pelo controller ou, após erro de validação, o que foi enviado.
$servicoSelecionado = (int) ($valores['servico'] ?? ($_POST['servico'] ?? 0));
?>
<form class="adm-form" action="<?= esc($formAcao) ?>" method="post" enctype="multipart/form-data">
    <?= createCsrf() ?>
    <input type="hidden" name="action" value="<?= $formEdicao ? 'update' : 'save' ?>">
    <?php if ($formEdicao): ?>
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="id_portifolio" value="<?= (int) $portifolio->id ?>">
    <?php endif ?>

    <?= adminErros($erros) ?>

    <section class="adm-fieldset">
        <h2 class="adm-fieldset-title">Sobre o projeto</h2>

        <label>Nome do projeto
            <input name="projeto" required maxlength="150" placeholder="Ex.: Norte Studio" value="<?= esc($valores['projeto']) ?>">
        </label>
        <label>Tipo de serviço
            <select name="servico" required>
                <option value="" disabled<?= $servicoSelecionado === 0 ? ' selected' : '' ?>>Selecione o serviço</option>
                <?php foreach ($servicos as $servico): ?>
                    <option value="<?= (int) $servico->id ?>"<?= $servicoSelecionado === (int) $servico->id ? ' selected' : '' ?>><?= esc($servico->nome) ?></option>
                <?php endforeach ?>
            </select>
        </label>
        <label class="adm-field-full">Descrição
            <textarea name="sobre" rows="5" placeholder="Conte o que foi o projeto..."><?= esc($valores['sobre']) ?></textarea>
        </label>
        <label>Site do projeto
            <input name="url" type="url" maxlength="255" placeholder="https://" value="<?= esc($valores['url']) ?>">
        </label>
        <label>GitHub
            <input name="github" type="url" maxlength="255" placeholder="https://github.com/..." value="<?= esc($valores['github']) ?>">
        </label>
    </section>

    <section class="adm-fieldset">
        <h2 class="adm-fieldset-title">Ideia · Intenção · Impacto</h2>

        <label class="adm-field-full">Ideia
            <textarea name="ideia" rows="3" placeholder="Qual foi a ideia por trás do projeto?"><?= esc($valores['ideia']) ?></textarea>
        </label>
        <label class="adm-field-full">Intenção
            <textarea name="intencao" rows="3" placeholder="O que queríamos alcançar?"><?= esc($valores['intencao']) ?></textarea>
        </label>
        <label class="adm-field-full">Impacto
            <textarea name="impacto" rows="3" placeholder="Qual foi o resultado?"><?= esc($valores['impacto']) ?></textarea>
        </label>
    </section>

    <section class="adm-fieldset">
        <h2 class="adm-fieldset-title">Imagens e vídeos</h2>

        <?php if ($capaAtual !== ''): ?>
            <div class="adm-current adm-field-full">
                <img src="<?= esc($capaAtual) ?>" alt="Capa atual">
                <span>Capa atual. Envie outra imagem para substituí-la.</span>
            </div>
        <?php endif ?>

        <label class="adm-field-full">Capa
            <input name="capa" type="file" accept="image/png,image/jpeg,image/webp">
            <span class="adm-hint">PNG, JPG ou WEBP, até 2 MB. Projetos sem capa não aparecem no site.</span>
        </label>

        <label class="adm-field-full">Galeria (imagens e vídeos)
            <input name="midias[]" type="file" multiple accept="image/png,image/jpeg,image/webp,image/gif,video/mp4,video/webm,video/ogg,video/quicktime">
            <span class="adm-hint">
                Imagens até 2 MB e vídeos até 50 MB.
                <?php if ($formEdicao && $totalMidias > 0): ?>
                    Hoje o projeto tem <?= (int) $totalMidias ?> mídia(s): ao enviar novos arquivos, a galeria atual é substituída.
                <?php endif ?>
            </span>
        </label>
    </section>

    <div class="adm-form-actions">
        <a class="adm-btn adm-btn-secondary" href="<?= $formEdicao ? esc($formAcao) : '/admin/portfolio' ?>">Cancelar</a>
        <button class="adm-btn adm-btn-primary" type="submit"><?= $formEdicao ? 'Salvar alterações' : 'Cadastrar projeto' ?></button>
    </div>
</form>
