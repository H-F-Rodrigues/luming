<?php
/**
 * Formulário compartilhado de mensagem (create e edit).
 * Campos = os que Cliente::validar() e Mensagem::validar() esperam:
 *   cliente:  nome, email, telefone, ramo
 *   mensagem: projeto, servico, descricao
 *   + id_mensagem, id_cliente (edição), action e csrf_token.
 *
 * @var string $formAcao   URL do action
 * @var bool   $formEdicao true na edição (envia PUT via _method)
 * @var array  $valores
 * @var array  $servicos   models\Servico[]
 * @var array  $ramos      models\Ramo[]
 * @var array  $erros      string[]
 */
?>
<form class="adm-form" action="<?= esc($formAcao) ?>" method="post">
    <?= createCsrf() ?>
    <input type="hidden" name="action" value="<?= $formEdicao ? 'update' : 'save' ?>">
    <?php if ($formEdicao): ?>
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="id_mensagem" value="<?= (int) $mensagem->id ?>">
        <input type="hidden" name="id_cliente" value="<?= (int) $mensagem->clienteId ?>">
    <?php endif ?>

    <?= adminErros($erros) ?>

    <section class="adm-fieldset">
        <h2 class="adm-fieldset-title">Dados do cliente</h2>

        <label>Nome
            <input name="nome" required maxlength="100" placeholder="Nome do cliente" autocomplete="off" value="<?= esc($valores['nome']) ?>">
        </label>
        <label>E-mail
            <input name="email" type="email" required maxlength="150" placeholder="contato@empresa.com" autocomplete="off" value="<?= esc($valores['email']) ?>">
        </label>
        <label>Telefone
            <input name="telefone" type="tel" required maxlength="20" placeholder="(00) 00000-0000" inputmode="tel" data-mask-telefone value="<?= esc($valores['telefone']) ?>">
        </label>
        <label>Ramo da empresa
            <select name="ramo" required>
                <option value="" disabled<?= empty($valores['ramo']) ? ' selected' : '' ?>>Selecione o ramo</option>
                <?php foreach ($ramos as $ramo): ?>
                    <option value="<?= (int) $ramo->id ?>"<?= (int) $valores['ramo'] === (int) $ramo->id ? ' selected' : '' ?>><?= esc($ramo->nome) ?></option>
                <?php endforeach ?>
            </select>
        </label>
    </section>

    <section class="adm-fieldset">
        <h2 class="adm-fieldset-title">Dados da mensagem</h2>

        <label>Título do projeto
            <input name="projeto" required maxlength="150" placeholder="Ex.: Site institucional para uma nova marca" value="<?= esc($valores['projeto']) ?>">
        </label>
        <label>Serviço / área de interesse
            <select name="servico" required>
                <option value="" disabled<?= empty($valores['servico']) ? ' selected' : '' ?>>Selecione uma opção</option>
                <?php foreach ($servicos as $servico): ?>
                    <option value="<?= (int) $servico->id ?>"<?= (int) $valores['servico'] === (int) $servico->id ? ' selected' : '' ?>><?= esc($servico->nome) ?></option>
                <?php endforeach ?>
            </select>
        </label>
        <label class="adm-field-full">Descrição
            <textarea name="descricao" rows="6" placeholder="Fale sobre o projeto, desafio ou ideia..."><?= esc($valores['descricao']) ?></textarea>
        </label>
    </section>

    <div class="adm-form-actions">
        <a class="adm-btn adm-btn-secondary" href="<?= $formEdicao ? esc($formAcao) : '/admin/mensagens' ?>">Cancelar</a>
        <button class="adm-btn adm-btn-primary" type="submit"><?= $formEdicao ? 'Salvar alterações' : 'Cadastrar mensagem' ?></button>
    </div>
</form>
