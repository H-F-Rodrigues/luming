<?php
/**
 * Tela de login da área exclusiva (envia para POST /login — LoginController::login).
 *
 * @var array  $erros
 * @var string $email
 */
$email ??= '';
?>
<main class="auth-page">
    <section class="auth-card" aria-labelledby="login-title">
        <a href="/" class="auth-brand"><span class="brand-mark">✦</span>LUMING</a>
        <span class="eyebrow auth-eyebrow"><i></i> área exclusiva</span>
        <h1 id="login-title">Acesse a<br><em>sua equipe.</em></h1>
        <p class="auth-copy">Entre no ambiente interno da LUMING para visualizar projetos, membros e configurações.</p>

        <form class="contact-form" action="/login" method="post">
            <?= createCsrf() ?>

            <?php if (!empty($erros)): ?>
                <div class="form-errors" role="alert">
                    <?php foreach ($erros as $erro): ?>
                        <p class="form-error"><?= esc($erro) ?></p>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <label for="email">E-mail
                <input id="email" name="email" type="email" required placeholder="voce@luming.com.br" autocomplete="username" value="<?= esc($email) ?>">
            </label>
            <label for="senha">Senha
                <input id="senha" name="senha" type="password" required placeholder="Sua senha" autocomplete="current-password">
            </label>
            <button class="button button-primary auth-submit" type="submit"><?= icon('lock', 15) ?> Entrar na área <?= icon('arrow-right', 16) ?></button>
        </form>

        <a href="/" class="forgot-link">← Voltar para o site</a>
    </section>
</main>
