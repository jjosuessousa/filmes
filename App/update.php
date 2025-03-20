<?php
require_once 'Conexao.php';
require_once 'Filme.php';
require_once 'FilmeDao.php';

use Kakashi\Filmes\Filme;
use Kakashi\Filmes\FilmeDao;



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $titulo = $_POST['titulo'] ?? null;
    $descricao = $_POST['descricao'] ?? null;
    $trailer = $_POST['trailer'] ?? null;
    $imagem = $_FILES['imagem'] ?? null;

    if ($id && $titulo && $descricao && $trailer) {
        $filme = new Filme();
        $filme->setId($id);
        $filme->setTitulo($titulo);
        $filme->setDescricao($descricao);
        $filme->setTrailer($trailer);

        if ($imagem && $imagem['tmp_name']) {
            $uploadDir = '../assets/uploads/';
            $imagemNome = basename($imagem['name']);
            $uploadCaminho = $uploadDir . $imagemNome;

            if (move_uploaded_file($imagem['tmp_name'], $uploadCaminho)) {
                $filme->setImagem($imagemNome); // Salva o novo nome da imagem no banco
            }
        }

        $filmeDao = new FilmeDao();
        $filmeDao->update($filme);
        echo "Filme atualizado com sucesso!";
    } else {
        echo "Todos os campos são obrigatórios!";
    }
}
