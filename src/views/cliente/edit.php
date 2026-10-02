<?php

use models\Cliente;
/**
 * @var Cliente $cliente
 * @var array $ramos
 */
?>
<form action="/clientes/update" method="post">
    <?= createCsrf() ?>
    <input type="hidden" name="id_cliente" value="<?= $cliente->id ?>">
    <input type="hidden" name="action" value="update">
    Nome: <input type="text" name="nome" id="nome" value="<?= $cliente->nome ?>">
    Email <input type="email" name="email" id="email" value="<?= $cliente->email ?>">
    Telefone <input type="tel" name="telefone" id="telefone" value="<?= $cliente->telefone ?>">
    Ramo: 
    <?php foreach ($ramos as $ramo): ?>
        <input type="radio" name="ramo" value="<?= $ramo->id ?>" <?= ($ramo->id === $cliente->ramoId) ? 'checked' : '' ?>> <?= $ramo->nome ?>
    <?php endforeach ?>

    <input type="submit" value="Atualizar">
</form>