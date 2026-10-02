<?php

use models\Cliente;
/**
 * @var models\Cliente $cliente
 */
?>

<h1>Nome: <?= $cliente->nome ?></h1>
<h3>Email: <?= $cliente->email ?></h3>
<h3>telefone: <?= $cliente->telefone ?></h3>
<h3>Ramo: <?= $cliente->ramo ?></h3>
<p>RamoId: <?= $cliente->ramoId ?></p>
<p>Id: <?= $cliente->id ?></p>
