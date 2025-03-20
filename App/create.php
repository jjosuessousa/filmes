<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Define os cabeçalhos para permitir CORS
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("HTTP/1.1 200 OK");
    exit; // Termina a execução para requisição OPTIONS
}


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Lida com requisições OPTIONS (CORS preflight request)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

require_once 'Conexao.php';
require_once 'Filme.php';
require_once 'FilmeDao.php';

use Kakashi\Filmes\Filme;
use Kakashi\Filmes\FilmeDao;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados enviados via POST
    $titulo = $_POST['titulo'] ?? null;
    $descricao = $_POST['descricao'] ?? null;
    $trailer = $_POST['trailer'] ?? null;
    $categoria = $_POST['categoria'] ?? null;
    $lancamento = isset($_POST['lancamento']) && $_POST['lancamento'] === 'on';
    $imagem = $_FILES['imagem'] ?? null;

    // Valida se todos os campos obrigatórios foram preenchidos
    if ($titulo && $descricao && $trailer && $categoria && $imagem) {
        // Define o diretório de upload
        $uploadDir = $lancamento ? '../assets/uploads/lancamentos/' : '../assets/uploads/';
        $imagemNome = basename($imagem['name']);
        $uploadCaminho = $uploadDir . $imagemNome;

        // Tenta salvar a imagem na pasta
        if (move_uploaded_file($imagem['tmp_name'], $uploadCaminho)) {
            try {
                // Cria um novo objeto de Filme
                $filme = new Filme();
                $filme->setTitulo($titulo);
                $filme->setDescricao($descricao);
                $filme->setImagem($imagemNome);
                $filme->setTrailer($trailer);
                $filme->setCategoria($categoria);

                // Salva o filme no banco de dados
                $filmeDao = new FilmeDao();
                $filmeDao->create($filme);

                // Retorna uma resposta de sucesso em JSON
                header('Content-Type: application/json');
                echo json_encode([
                    "mensagem" => "Filme cadastrado com sucesso!",
                    "lancamento" => $lancamento,
                    "dados" => [
                        "titulo" => $titulo,
                        "descricao" => $descricao,
                        "categoria" => $categoria,
                        "trailer" => $trailer,
                        "imagem" => $imagemNome
                    ]
                ]);
            } catch (Exception $e) {
                // Retorna erro caso ocorra algum problema no banco
                http_response_code(500);
                echo json_encode(["erro" => "Erro ao salvar o filme no banco de dados: " . $e->getMessage()]);
            }
        } else {
            // Retorna erro caso o upload da imagem falhe
            http_response_code(500);
            echo json_encode(["erro" => "Erro ao fazer upload da imagem!"]);
        }
    } else {
        // Retorna erro caso algum campo obrigatório esteja faltando
        http_response_code(400);
        echo json_encode(["erro" => "Todos os campos são obrigatórios!"]);
    }
} else {
    // Retorna erro caso o método HTTP não seja POST
    http_response_code(405);
    echo json_encode(["erro" => "Método não permitido!"]);
}
