<?php
namespace Kakashi\Filmes;



require_once __DIR__ . '/../vendor/autoload.php';




class Conexao {
    private static $instance;

    public static function getConn() {
        if (!isset(self::$instance)) {
            try {
                self::$instance = new \PDO('mysql:host=localhost;dbname=catalogo', 'root', '');
                self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); // Tratamento de erros
            } catch (\PDOException $e) {
                die("Erro ao conectar ao banco de dados: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
