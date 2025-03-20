<?php
require_once './App/Conexao.php';
require_once './App/FilmeDao.php';

use Kakashi\Filmes\FilmeDao;

// Configura os cabeçalhos para JSON e CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$filmeDao = new FilmeDao();
$filmes = $filmeDao->read();

// Retorna os filmes como JSON
echo json_encode($filmes);
?>
