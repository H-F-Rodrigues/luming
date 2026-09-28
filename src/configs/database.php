<?php

define("BD_DSN", "mysql:dbname=db_luming;host=127.0.0.1;charset=utf8mb4"); 
define("BD_USUARIO", "root");
define("BD_SENHA", "");

try {
    // Criamos um array de opções para forçar o comando de inicialização em UTF-8
    $opcoes = [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];

    // Passamos a variável $opcoes como o quarto parâmetro do PDO
    define('PDO', new PDO(BD_DSN, BD_USUARIO, BD_SENHA, $opcoes));
}
catch (PDOException $e) {
    echo 'Falha na conexão com o banco de dados: ' . $e->getMessage();
    die();
}
