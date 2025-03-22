<?php

namespace Kakashi\Filmes\Core;
// Habilitar CORS
header("Access-Control-Allow-Origin: *"); // Permitir requisições de qualquer origem
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Permitir os métodos especificados
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Permitir os cabeçalhos especificados
header("Access-Control-Allow-Credentials: true"); // Permitir credenciais (se necessário)

// Lidar com requisições OPTIONS (pré-vôo)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

class Conexao {
    public static function conectar() {
        $host = 'localhost';
        $dbname = 'filmes'; // Substitua pelo nome do banco
        $usuario = 'root'; // Usuário do banco

        try {
            // Criar conexão sem senha
            $conexao = new \PDO("mysql:host=$host;dbname=$dbname", $usuario);
            $conexao->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $conexao;
        } catch (\PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }
}