<?php

use Kakashi\Filmes\FilmeDao;
use Kakashi\Filmes\Filme;

// Inicializa um roteador simples
$router = new stdClass();
$router->routes = [];

// Rota para listar filmes (READ)
$router->routes['GET']['/filmes'] = function () {
    $filmeDao = new FilmeDao();
    $filmes = $filmeDao->read();

    header('Content-Type: application/json');
    echo json_encode($filmes);
};

// Rota para criar um novo filme (CREATE)
$router->routes['POST']['/filmes'] = function () {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $titulo = $data['titulo'] ?? null;
    $descricao = $data['descricao'] ?? null;
    $trailer = $data['trailer'] ?? null;
    $imagem = $data['imagem'] ?? null;

    if ($titulo && $descricao && $trailer && $imagem) {
        $filme = new Filme();
        $filme->setTitulo($titulo);
        $filme->setDescricao($descricao);
        $filme->setImagem($imagem);
        $filme->setTrailer($trailer);

        $filmeDao = new FilmeDao();
        $filmeDao->create($filme);

        echo json_encode(["mensagem" => "Filme criado com sucesso!"]);
    } else {
        echo json_encode(["erro" => "Todos os campos são obrigatórios."]);
    }
};

// Rota para atualizar um filme (UPDATE)
$router->routes['PUT']['/filmes'] = function () {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'] ?? null;
    $titulo = $data['titulo'] ?? null;
    $descricao = $data['descricao'] ?? null;
    $trailer = $data['trailer'] ?? null;
    $imagem = $data['imagem'] ?? null;

    if ($id && $titulo && $descricao && $trailer) {
        $filme = new Filme();
        $filme->setId($id);
        $filme->setTitulo($titulo);
        $filme->setDescricao($descricao);
        $filme->setImagem($imagem);
        $filme->setTrailer($trailer);

        $filmeDao = new FilmeDao();
        $filmeDao->update($filme);

        echo json_encode(["mensagem" => "Filme atualizado com sucesso!"]);
    } else {
        echo json_encode(["erro" => "Todos os campos são obrigatórios."]);
    }
};

// Rota para deletar um filme (DELETE)
$router->routes['DELETE']['/filmes'] = function () {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'] ?? null;

    if ($id) {
        $filmeDao = new FilmeDao();
        $filmeDao->delete($id);

        echo json_encode(["mensagem" => "Filme deletado com sucesso!"]);
    } else {
        echo json_encode(["erro" => "ID do filme é obrigatório."]);
    }
};

// Função para executar as rotas
$router->run = function ($routes) {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = $_SERVER['PATH_INFO'] ?? '/';

    foreach ($routes as $routeMethod => $routePaths) {
        if ($routeMethod === $method && isset($routePaths[$path])) {
            $routePaths[$path](); // Executa a rota correspondente
            return;
        }
    }

    http_response_code(404); // Rota não encontrada
    echo json_encode(["erro" => "Rota não encontrada."]);
};
