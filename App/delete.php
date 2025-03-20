<?php
require_once 'Conexao.php';
require_once 'FilmeDao.php';



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


use Kakashi\Filmes\FilmeDao;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id) {
        $filmeDao = new FilmeDao();
        if ($filmeDao->delete($id)) {
            echo "Filme deletado com sucesso!";
        } else {
            echo "Erro ao deletar o filme.";
        }
    } else {
        echo "ID do filme é obrigatório!";
    }
}
