<?php

use models\Membro;
/**
 * @var models\Membro $membro
 */
?>

<h1>Nome: <?= $membro->nome ?></h1>
<h3>Email: <?= $membro->email ?></h3>
<h3>Senha: <?= $membro->senha ?></h3>
<h3>Função: <?= $membro->funcao ?></h3>
<p>Data Criado: <?= $membro->dtCriado ?></p>
<p>Id: <?= $membro->id ?></p>
