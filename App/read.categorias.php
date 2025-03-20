<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Kakashi\Filmes\Conexao;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

try {
    $conexao = Conexao::getConn();
    $query = "SELECT id, nome FROM categorias"; // Obtém as categorias
    $stmt = $conexao->prepare($query);
    $stmt->execute();

    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($categorias);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao buscar categorias: " . $e->getMessage()]);
}
