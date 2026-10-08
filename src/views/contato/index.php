<?php
/**
 * Página de contato / orçamento.
 * O formulário envia para POST /contato (HomepageController::saveContato),
 * que usa os mesmos validadores de Cliente e Mensagem do MensagemController.
 *
 * @var array $ramos     models\Ramo[]
 * @var array $servicos  models\Servico[]
 * @var array $erros     string[]
 * @var array $old       valores enviados anteriormente (repopula o form)
 * @var bool  $enviado   true após envio com sucesso
 */
$old ??= [];
$enviado ??= false;
?>
<main class="inner-page contact-page">
    <div class="inner-glow"></div>
    <div class="container inner-content">
        <a href="/" class="back-link">← voltar para a LUMING</a>
        <span class="eyebrow"><i></i> fale com a gente</span>
        <h1>Vamos dar luz<br><em>à sua ideia.</em></h1>
        <p class="inner-lead">Conte um pouco sobre o que você quer construir. A gente responde com atenção, clareza e o próximo passo.</p>

        <div class="contact-layout">
            <form class="contact-form" action="/contato" method="post" novalidate data-contact-form>
                <?= createCsrf() ?>
                <input type="hidden" name="action" value="save">

                <?php if (!empty($erros)): ?>
                    <div class="form-errors" role="alert">
                        <?php foreach ($erros as $erro): ?>
                            <p class="form-error"><?= esc($erro) ?></p>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <label>Seu nome
                    <input name="nome" required maxlength="100" placeholder="Como podemos te chamar?" autocomplete="name" value="<?= esc($old['nome'] ?? '') ?>">
                </label>
                <label>Seu e-mail
                    <input name="email" type="email" required maxlength="150" placeholder="voce@empresa.com" autocomplete="email" value="<?= esc($old['email'] ?? '') ?>">
                </label>
                <label>Serviço / ramo da empresa
                    <select name="ramo" required>
                        <option value="" disabled<?= empty($old['ramo']) ? ' selected' : '' ?>>Selecione o ramo da empresa</option>
                        <?php foreach ($ramos ?? [] as $ramo): ?>
                            <option value="<?= (int) $ramo->id ?>"<?= (int) ($old['ramo'] ?? 0) === (int) $ramo->id ? ' selected' : '' ?>><?= esc($ramo->nome) ?></option>
                        <?php endforeach ?>
                    </select>
                </label>
                <label>Telefone
                    <input name="telefone" type="tel" required maxlength="20" placeholder="(00) 00000-0000" autocomplete="tel" inputmode="tel" data-mask-telefone value="<?= esc($old['telefone'] ?? '') ?>">
                </label>
                <label>Título do projeto
                    <input name="projeto" required maxlength="150" placeholder="Ex.: Site institucional para uma nova marca" value="<?= esc($old['projeto'] ?? '') ?>">
                </label>
                <label>Serviço / área de interesse
                    <select name="servico" required>
                        <option value="" disabled<?= empty($old['servico']) ? ' selected' : '' ?>>Selecione uma opção</option>
                        <?php foreach ($servicos ?? [] as $servico): ?>
                            <option value="<?= (int) $servico->id ?>"<?= (int) ($old['servico'] ?? 0) === (int) $servico->id ? ' selected' : '' ?>><?= esc($servico->nome) ?></option>
                        <?php endforeach ?>
                    </select>
                </label>
                <label>Como podemos ajudar?
                    <textarea name="descricao" required placeholder="Fale sobre seu projeto, desafio ou ideia..." rows="5"><?= esc($old['descricao'] ?? '') ?></textarea>
                </label>

                <?php if ($enviado): ?>
                    <p class="form-success" role="status">Recebemos seu contato. Em breve responderemos com os próximos passos.</p>
                <?php endif ?>

                <button class="button button-primary" type="submit">Enviar mensagem <?= icon('arrow-up-right', 17) ?></button>
            </form>

            <aside class="contact-aside">
                <div class="aside-icon"><?= icon('lightbulb', 22) ?></div>
                <h2>Uma conversa pode mudar tudo.</h2>
                <p>Se preferir, fale diretamente com a gente pelos canais abaixo.</p>
                <a href="mailto:oi@luming.com.br"><?= icon('mail', 17) ?> oi@luming.com.br</a>
                <a href="https://wa.me/5500000000000" target="_blank" rel="noopener"><?= icon('message-circle', 17) ?> WhatsApp</a>
                <span><?= icon('map-pin', 17) ?> Brasil · atendimento remoto</span>
            </aside>
        </div>
    </div>
</main>
