<?php

use models\Membro;
/**
 * @var Membro $membro
 * @var array $funcoes
 */
?>
<form action="/membros/update" method="post">
    <?= createCsrf() ?>
    <input type="hidden" name="id_membro" value="<?= $membro->id ?>">
    <input type="hidden" name="action" value="update">
    Nome: <input type="text" name="nome" id="nome" value="<?= $membro->nome ?>">
    Email <input type="email" name="email" id="email" value="<?= $membro->email ?>">
    Senha <input type="password" name="senha" id="senha">
    funcao 
    <?php foreach ($funcoes as $funcao): ?>
        <input type="radio" name="funcao" value="<?= $funcao->id ?>" <?= ($funcao->id === $membro->funcaoId) ? 'checked' : '' ?>> <?= $funcao->nome ?>
    <?php endforeach ?>

    <input type="submit" value="Atualizar">
</form>