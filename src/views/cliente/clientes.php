<?php
/**
 * @var array $clientes
 * @var array $ramos
 */
?>

<?php foreach ($clientes as $cliente): ?>
    <h1>Nome: <?= $cliente->nome ?></h1>
    <h3>Email: <?= $cliente->email ?></h3>
    <h3>telefone: <?= $cliente->telefone ?></h3>
    <h3>Ramo: <?= $cliente->ramo ?></h3>
    <p>RamoId: <?= $cliente->ramoId ?></p>
    <p>Id: <?= $cliente->id ?></p>
    <form action="/clientes/edit" method="post">
        <?= createCsrf() ?>
        <input type="hidden" name="id_cliente" value="<?= $cliente->id ?>">
        <input type="submit" value="Editar">
    </form>
    <form action="/clientes/delete" method="post">
        <?= createCsrf() ?>
        <input type="hidden" name="id_cliente" value="<?= $cliente->id ?>">
        <input type="submit" value="Deletar">
    </form>
<?php endforeach ?>

<form action="/clientes/save" method="post">
    <?= createCsrf() ?>
    <input type="hidden" name="action" value="save">
    Nome: <input type="text" name="nome" id="nome">
    Email <input type="email" name="email" id="email">
    Telefone <input type="tel" name="telefone" id="telefone">
    Ramo:
    <?php foreach ($ramos as $ramo): ?>
        <input type="radio" name="ramo" value="<?= $ramo->id ?>"> <?= $ramo->nome ?>
    <?php endforeach ?>

    <input type="submit" value="Adicionar">
</form>