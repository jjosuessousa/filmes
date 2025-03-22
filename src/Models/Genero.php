<?php

namespace Kakashi\Filmes\Models;

class Genero {
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
    }

    // Listar todos os gêneros
    public function listarGeneros() {
        try {
            $sql = "SELECT * FROM generos";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            // Log ou tratamento do erro
            return false;
        }
    }

    // Adicionar um novo gênero
    public function criarGenero($nome) {
        try {
            $sql = "INSERT INTO generos (nome) VALUES (:nome)";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':nome', $nome, \PDO::PARAM_STR);
            return $stmt->execute();
        } catch (\Exception $e) {
            return false;
        }
    }

    // Atualizar um gênero existente
    public function atualizarGenero($id, $nome) {
        try {
            $sql = "UPDATE generos SET nome = :nome WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->bindParam(':nome', $nome, \PDO::PARAM_STR);
            return $stmt->execute();
        } catch (\Exception $e) {
            return false;
        }
    }

    // Excluir um gênero pelo ID
    public function excluirGenero($id) {
        try {
            $sql = "DELETE FROM generos WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\Exception $e) {
            return false;
        }
    }
}