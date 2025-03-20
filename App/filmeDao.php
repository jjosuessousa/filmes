<?php
namespace Kakashi\Filmes;

class filmeDao {
    private $conn;

    public function __construct() {
        try {
            $this->conn = Conexao::getConn();
        } catch (\PDOException $e) {
            die("Erro ao conectar com o banco de dados: " . $e->getMessage());
        }
    }

    public function create(filme $filme) {
        try {
            $sql = 'INSERT INTO filmes (titulo, descricao, imagem, trailer) VALUES (:titulo, :descricao, :imagem, :trailer)';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':titulo', $filme->getTitulo());
            $stmt->bindValue(':descricao', $filme->getDescricao());
            $stmt->bindValue(':imagem', $filme->getImagem());
            $stmt->bindValue(':trailer', $filme->getTrailer());
            $stmt->execute();
        } catch (\PDOException $e) {
            die("Erro ao inserir o filme: " . $e->getMessage());
        }
    }

    public function read() {
        try {
            $sql = 'SELECT * FROM filmes';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        } catch (\PDOException $e) {
            die("Erro ao buscar filmes: " . $e->getMessage());
        }
    }

    public function update(filme $filme) {
        try {
            $sql = 'UPDATE filmes SET titulo = :titulo, descricao = :descricao, imagem = :imagem, trailer = :trailer WHERE id = :id';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':titulo', $filme->getTitulo());
            $stmt->bindValue(':descricao', $filme->getDescricao());
            $stmt->bindValue(':imagem', $filme->getImagem());
            $stmt->bindValue(':trailer', $filme->getTrailer());
            $stmt->bindValue(':id', $filme->getId(), \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $e) {
            die("Erro ao atualizar o filme: " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $sql = 'DELETE FROM filmes WHERE id = :id';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $e) {
            die("Erro ao deletar o filme: " . $e->getMessage());
        }
    }
}
