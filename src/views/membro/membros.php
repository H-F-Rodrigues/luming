<?php
/**
 * @var array $membros
 * @var array $funcoes
 */
?>

<?php foreach ($membros as $membro): ?>
    <h1>Nome: <?= $membro->nome ?></h1>
    <h3>Email: <?= $membro->email ?></h3>
    <h3>Senha: <?= $membro->senha ?></h3>
    <h3>Função: <?= $membro->funcao ?></h3>
    <p>Data Criado: <?= $membro->dtCriado ?></p>
    <p>Id: <?= $membro->id ?></p>
    <form action="/membros/edit" method="post">
        <?= createCsrf() ?>
        <input type="hidden" name="id_membro" value="<?= $membro->id ?>">
        <input type="submit" value="Editar">
    </form>
    <form action="/membros/delete" method="post">
        <?= createCsrf() ?>
        <input type="hidden" name="id_membro" value="<?= $membro->id ?>">
        <input type="submit" value="Deletar">
    </form>
<?php endforeach ?>

<form action="/membros/save" method="post">
    <?= createCsrf() ?>
    <input type="hidden" name="action" value="save">
    Nome: <input type="text" name="nome" id="nome">
    Email <input type="email" name="email" id="email">
    Senha <input type="password" name="senha" id="senha">
    funcao 
    <?php foreach ($funcoes as $funcao): ?>
        <input type="radio" name="funcao" value="<?= $funcao->id ?>"> <?= $funcao->nome ?>
    <?php endforeach ?>

    <input type="submit" value="Adicionar">
</form>