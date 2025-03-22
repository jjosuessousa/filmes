<?php

namespace Kakashi\Filmes\Models;

class Filme {
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
    }

    // Listar todos os filmes com os gêneros
    public function listarFilmes() {
        try {
            $sql = "SELECT filmes.*, generos.nome AS genero_nome 
                    FROM filmes 
                    LEFT JOIN generos ON filmes.genero_id = generos.id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            $filmes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Verifica se o caminho da imagem existe, caso contrário, define uma imagem padrão
            foreach ($filmes as &$filme) {
                if (empty($filme['capa'])) {
                    $filme['capa'] = '/filmes/uploads/imagem_padrao.jpg'; // Imagem padrão
                }
            }

            return $filmes;
        } catch (\Exception $e) {
            // Log do erro ou tratamento
            return false;
        }
    }

    // Obter detalhes de um filme específico
    public function detalhesFilme($id) {
        try {
            $sql = "SELECT filmes.*, generos.nome AS genero_nome 
                    FROM filmes 
                    LEFT JOIN generos ON filmes.genero_id = generos.id
                    WHERE filmes.id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            $filme = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Verifica se o caminho da imagem existe, caso contrário, define uma imagem padrão
            if (empty($filme['capa'])) {
                $filme['capa'] = '/filmes/uploads/imagem_padrao.jpg'; // Imagem padrão
            }

            return $filme;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Pesquisar filmes por nome e, opcionalmente, por gênero
    public function pesquisarFilme($nome, $genero) {
        try {
            $sql = "SELECT filmes.*, generos.nome AS genero_nome 
                    FROM filmes 
                    LEFT JOIN generos ON filmes.genero_id = generos.id
                    WHERE filmes.titulo LIKE :nome";
            if ($genero) {
                $sql .= " AND generos.nome = :genero";
            }
            $stmt = $this->conexao->prepare($sql);
            $nome = '%' . $nome . '%';
            $stmt->bindParam(':nome', $nome, \PDO::PARAM_STR);
            if ($genero) {
                $stmt->bindParam(':genero', $genero, \PDO::PARAM_STR);
            }
            $stmt->execute();
            $filmes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Verifica se o caminho da imagem existe, caso contrário, define uma imagem padrão
            foreach ($filmes as &$filme) {
                if (empty($filme['capa'])) {
                    $filme['capa'] = '/filmes/uploads/imagem_padrao.jpg'; // Imagem padrão
                }
            }

            return $filmes;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Cadastrar um novo filme
    public function cadastrarFilme($dados) {
        try {
            $sql = "INSERT INTO filmes (titulo, sinopse, genero_id, capa, trailer, lancamento, duracao) 
                    VALUES (:titulo, :sinopse, :genero_id, :caminho_capa, :trailer, :lancamento, :duracao)";
            $stmt = $this->conexao->prepare($sql);

            $stmt->bindParam(':titulo', $dados['titulo'], \PDO::PARAM_STR);
            $stmt->bindParam(':sinopse', $dados['sinopse'], \PDO::PARAM_STR);
            $stmt->bindParam(':genero_id', $dados['genero_id'], \PDO::PARAM_INT);
            $stmt->bindParam(':caminho_capa', $dados['caminhoCapa'], \PDO::PARAM_STR);
            $stmt->bindParam(':trailer', $dados['trailer'], \PDO::PARAM_STR);
            $stmt->bindParam(':lancamento', $dados['lancamento'], \PDO::PARAM_STR);
            $stmt->bindParam(':duracao', $dados['duracao'], \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\Exception $e) {
            return false;
        }
    }

    // Atualizar um filme existente
    public function atualizarFilme($dados) {
        try {
            // Buscar o ID do filme com base no título ou outro identificador exclusivo
            $sqlBuscaId = "SELECT id FROM filmes WHERE titulo = :titulo";
            $stmtBuscaId = $this->conexao->prepare($sqlBuscaId);
            $stmtBuscaId->bindParam(':titulo', $dados['titulo'], \PDO::PARAM_STR);
            $stmtBuscaId->execute();
            $resultadoId = $stmtBuscaId->fetch(\PDO::FETCH_ASSOC);

            if (!$resultadoId || !isset($resultadoId['id'])) {
                http_response_code(404);
                echo json_encode(["message" => "Filme não encontrado para atualização."]);
                return false;
            }

            $id = $resultadoId['id'];

            // Atualizar o filme com os novos dados
            $sqlAtualiza = "UPDATE filmes SET 
                    titulo = :titulo, 
                    sinopse = :sinopse, 
                    genero_id = :genero_id, 
                    capa = :caminho_capa, 
                    trailer = :trailer, 
                    lancamento = :lancamento, 
                    duracao = :duracao 
                    WHERE id = :id";
            $stmtAtualiza = $this->conexao->prepare($sqlAtualiza);

            $stmtAtualiza->bindParam(':titulo', $dados['titulo'], \PDO::PARAM_STR);
            $stmtAtualiza->bindParam(':sinopse', $dados['sinopse'], \PDO::PARAM_STR);
            $stmtAtualiza->bindParam(':genero_id', $dados['genero_id'], \PDO::PARAM_INT);
            $stmtAtualiza->bindParam(':caminho_capa', $dados['caminhoCapa'], \PDO::PARAM_STR);
            $stmtAtualiza->bindParam(':trailer', $dados['trailer'], \PDO::PARAM_STR);
            $stmtAtualiza->bindParam(':lancamento', $dados['lancamento'], \PDO::PARAM_STR);
            $stmtAtualiza->bindParam(':duracao', $dados['duracao'], \PDO::PARAM_INT);
            $stmtAtualiza->bindParam(':id', $id, \PDO::PARAM_INT);

            return $stmtAtualiza->execute();
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro interno ao atualizar filme.", "error" => $e->getMessage()]);
            return false;
        }
    }

    // Excluir um filme pelo ID
    public function excluirFilme($id) {
        try {
            $sql = "DELETE FROM filmes WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\Exception $e) {
            return false;
        }
    }
}