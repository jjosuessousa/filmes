<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Kakashi\Filmes\Conexao;

try {
    $conexao = Conexao::getConn();
    echo "Conexão com o banco de dados bem-sucedida!";
} catch (\PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}
