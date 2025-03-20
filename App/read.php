<?php
require_once 'Conexao.php';
require_once 'FilmeDao.php';

use Kakashi\Filmes\FilmeDao;



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


// Obtendo parâmetros de busca
$nome = isset($_GET['nome']) ? $_GET['nome'] : null;
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;

$filmeDao = new FilmeDao();
$filmes = $filmeDao->read();

if ($nome) {
    $filmes = array_filter($filmes, function($filme) use ($nome) {
        return stripos($filme['titulo'], $nome) !== false;
    });
}

if ($categoria) {
    $filmes = array_filter($filmes, function($filme) use ($categoria) {
        return stripos($filme['categoria'], $categoria) !== false;
    });
}

header('Content-Type: application/json');
echo json_encode($filmes);
