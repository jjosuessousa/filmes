<?php

namespace Kakashi\Filmes\Controllers;

use Kakashi\Filmes\Models\Filme;

class FilmeController {
    private $filmeModel;

    // Construtor para inicializar o modelo de Filme
    public function __construct($conexao) {
        $this->filmeModel = new Filme($conexao);
    }

    // Listar todos os filmes cadastrados
    public function listarFilmes() {
        header('Content-Type: application/json');
        try {
            $filmes = $this->filmeModel->listarFilmes();
            if ($filmes === false) {
                throw new \Exception("Erro ao buscar filmes no banco de dados.");
            }

            // Verifica se o caminho da imagem existe, caso contrário, define uma imagem padrão
            foreach ($filmes as &$filme) {
                if (empty($filme['capa'])) {
                    $filme['capa'] = '/filmes/uploads/imagem_padrao.jpg'; // Imagem padrão
                }
            }

            echo json_encode($filmes);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao listar filmes.", "error" => $e->getMessage()]);
        }
    }

    // Obter os detalhes de um filme pelo ID
    public function detalhesFilme($id) {
        header('Content-Type: application/json');
        try {
            if (empty($id)) {
                http_response_code(400);
                echo json_encode(["message" => "ID do filme não fornecido."]);
                return;
            }

            $filme = $this->filmeModel->detalhesFilme($id);
            if ($filme === false) {
                throw new \Exception("Erro ao buscar detalhes do filme no banco de dados.");
            }

            if ($filme) {
                // Verifica se o caminho da imagem existe, caso contrário, define uma imagem padrão
                if (empty($filme['capa'])) {
                    $filme['capa'] = '/filmes/uploads/imagem_padrao.jpg'; // Imagem padrão
                }

                echo json_encode($filme);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Filme não encontrado."]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao buscar detalhes do filme.", "error" => $e->getMessage()]);
        }
    }

    // Pesquisar filmes pelo nome e opcionalmente pelo gênero
    public function pesquisarFilme($nome, $genero = null) {
        header('Content-Type: application/json');
        try {
            if (empty($nome) && empty($genero)) {
                http_response_code(400);
                echo json_encode(["message" => "Parâmetros de pesquisa não fornecidos."]);
                return;
            }

            $filmes = $this->filmeModel->pesquisarFilme($nome, $genero);
            if ($filmes === false) {
                throw new \Exception("Erro ao pesquisar filmes no banco de dados.");
            }

            // Verifica se o caminho da imagem existe, caso contrário, define uma imagem padrão
            foreach ($filmes as &$filme) {
                if (empty($filme['capa'])) {
                    $filme['capa'] = '/filmes/uploads/imagem_padrao.jpg'; // Imagem padrão
                }
            }

            echo json_encode($filmes);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao pesquisar filmes.", "error" => $e->getMessage()]);
        }
    }

    // Cadastrar um novo filme
    public function cadastrarFilme($dados) {
        header('Content-Type: application/json');
        try {
            if (empty($dados['titulo']) || empty($dados['genero_id'])) {
                http_response_code(400);
                echo json_encode(["message" => "Dados obrigatórios ausentes."]);
                return;
            }

            // Verifica se o caminho da imagem foi fornecido
            if (empty($dados['caminhoCapa'])) {
                $dados['caminhoCapa'] = '/filmes/uploads/imagem_padrao.jpg'; // Imagem padrão
            }

            $resultado = $this->filmeModel->cadastrarFilme($dados);
            if ($resultado) {
                http_response_code(201); // Criado com sucesso
                echo json_encode(["message" => "Filme cadastrado com sucesso!"]);
            } else {
                throw new \Exception("Erro ao cadastrar filme no banco de dados.");
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro interno ao cadastrar filme.", "error" => $e->getMessage()]);
        }
    }

    // Atualizar um filme existente
    public function atualizarFilme($dados) {
        header('Content-Type: application/json');
        try {
            if (empty($dados['id'])) {
                http_response_code(400);
                echo json_encode(["message" => "ID do filme não fornecido."]);
                return;
            }

            // Verifica se o caminho da imagem foi fornecido
            if (empty($dados['caminhoCapa'])) {
                $dados['caminhoCapa'] = '/filmes/uploads/imagem_padrao.jpg'; // Imagem padrão
            }

            $resultado = $this->filmeModel->atualizarFilme($dados);
            if ($resultado) {
                echo json_encode(["message" => "Filme atualizado com sucesso!"]);
            } else {
                throw new \Exception("Erro ao atualizar filme no banco de dados.");
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro interno ao atualizar filme.", "error" => $e->getMessage()]);
        }
    }

    // Excluir um filme pelo ID
    public function excluirFilme($id) {
        header('Content-Type: application/json');
        try {
            if (empty($id)) {
                http_response_code(400);
                echo json_encode(["message" => "ID do filme não fornecido."]);
                return;
            }

            $resultado = $this->filmeModel->excluirFilme($id);
            if ($resultado) {
                echo json_encode(["message" => "Filme excluído com sucesso!"]);
            } else {
                throw new \Exception("Erro ao excluir filme no banco de dados.");
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro interno ao excluir filme.", "error" => $e->getMessage()]);
        }
    }
}