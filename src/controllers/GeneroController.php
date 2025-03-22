<?php

namespace Kakashi\Filmes\Controllers;

use Kakashi\Filmes\Models\Genero;

class GeneroController {
    private $generoModel;

    public function __construct($conexao) {
        $this->generoModel = new Genero($conexao);
    }

    // Listar todos os gêneros
    public function listarGeneros() {
        header('Content-Type: application/json');
        try {
            $generos = $this->generoModel->listarGeneros();
            if ($generos) {
                echo json_encode($generos);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Nenhum gênero encontrado."]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao listar gêneros."]);
        }
    }

    // Criar um novo gênero
    public function criarGenero($dados) {
        header('Content-Type: application/json');
        try {
            if (empty($dados['nome'])) {
                http_response_code(400);
                echo json_encode(["message" => "Nome do gênero é obrigatório."]);
                return;
            }

            $resultado = $this->generoModel->criarGenero($dados['nome']);
            if ($resultado) {
                http_response_code(201); // Created
                echo json_encode(["message" => "Gênero criado com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao criar gênero."]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro interno ao criar gênero."]);
        }
    }

    // Atualizar um gênero existente
    public function atualizarGenero($id, $dados) {
        header('Content-Type: application/json');
        try {
            if (empty($id) || empty($dados['nome'])) {
                http_response_code(400);
                echo json_encode(["message" => "ID e nome do gênero são obrigatórios."]);
                return;
            }

            $resultado = $this->generoModel->atualizarGenero($id, $dados['nome']);
            if ($resultado) {
                echo json_encode(["message" => "Gênero atualizado com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao atualizar gênero."]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro interno ao atualizar gênero."]);
        }
    }

    // Excluir um gênero pelo ID
    public function excluirGenero($id) {
        header('Content-Type: application/json');
        try {
            if (empty($id)) {
                http_response_code(400);
                echo json_encode(["message" => "ID do gênero é obrigatório."]);
                return;
            }

            $resultado = $this->generoModel->excluirGenero($id);
            if ($resultado) {
                echo json_encode(["message" => "Gênero excluído com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao excluir gênero."]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro interno ao excluir gênero."]);
        }
    }
}