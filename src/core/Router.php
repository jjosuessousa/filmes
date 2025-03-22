<?php

use Kakashi\Filmes\Controllers\FilmeController;
use Kakashi\Filmes\Controllers\GeneroController;
use Kakashi\Filmes\Controllers\HomeController;
use Kakashi\Filmes\Core\Conexao;

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

require_once __DIR__ . '/../vendor/autoload.php';

// Configurar conexão com o banco
$conexao = Conexao::conectar();

// Definir a rota solicitada na URL
$rota = $_GET['rota'] ?? null;

// Definir ações para cada rota
switch ($rota) {
    case 'home':
        $controller = new HomeController();
        $controller->exibirHome();
        break;

    case 'filmes':
        $controller = new FilmeController($conexao);
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                // Listar todos os filmes
                $controller->listarFilmes();
                break;

            case 'POST':
                // Cadastrar ou atualizar filme
                $dados = json_decode(file_get_contents("php://input"), true);
                if (!$dados) {
                    http_response_code(400);
                    echo json_encode(["message" => "Dados inválidos ou ausentes."]);
                    break;
                }

                if (isset($dados['id'])) {
                    // Atualizar filme existente
                    $controller->atualizarFilme($dados);
                } else {
                    // Cadastrar novo filme
                    $controller->cadastrarFilme($dados);
                }
                break;

            case 'PUT':
                // Atualizar filme via PUT
                $dados = json_decode(file_get_contents("php://input"), true);
                if (!$dados || !isset($dados['id'])) {
                    http_response_code(400);
                    echo json_encode(["message" => "Dados inválidos ou ID ausente."]);
                    break;
                }
                $controller->atualizarFilme($dados);
                break;

            case 'DELETE':
                // Excluir filme
                if (isset($_GET['id'])) {
                    $controller->excluirFilme($_GET['id']);
                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "ID do filme não fornecido para exclusão."]);
                }
                break;

            default:
                // Método HTTP não permitido
                http_response_code(405);
                echo json_encode(["message" => "Método não permitido."]);
                break;
        }
        break;

    case 'filmes/detalhes':
        if (isset($_GET['id'])) {
            $controller = new FilmeController($conexao);
            $controller->detalhesFilme($_GET['id']);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID do filme não fornecido."]);
        }
        break;

    case 'filmes/pesquisa':
        $controller = new FilmeController($conexao);
        $nome = $_GET['nome'] ?? null;
        $genero = $_GET['genero'] ?? null;
        if (!$nome && !$genero) {
            http_response_code(400);
            echo json_encode(["message" => "Parâmetros de pesquisa não fornecidos."]);
            break;
        }
        $controller->pesquisarFilme($nome, $genero);
        break;

    case 'generos':
        $controller = new GeneroController($conexao);
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                // Listar todos os gêneros
                $controller->listarGeneros();
                break;

            case 'POST':
                // Cadastrar novo gênero
                $dados = json_decode(file_get_contents("php://input"), true);
                if (!$dados) {
                    http_response_code(400);
                    echo json_encode(["message" => "Dados inválidos ou ausentes."]);
                    break;
                }
                $controller->criarGenero($dados);
                break;

            case 'PUT':
                // Atualizar gênero existente
                $dados = json_decode(file_get_contents("php://input"), true);
                if (!$dados || !isset($dados['id'])) {
                    http_response_code(400);
                    echo json_encode(["message" => "Dados inválidos ou ID ausente."]);
                    break;
                }
                $controller->atualizarGenero($dados['id'], $dados);
                break;

            case 'DELETE':
                // Excluir gênero
                if (isset($_GET['id'])) {
                    $controller->excluirGenero($_GET['id']);
                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "ID do gênero não fornecido para exclusão."]);
                }
                break;

            default:
                // Método HTTP não permitido
                http_response_code(405);
                echo json_encode(["message" => "Método não permitido."]);
                break;
        }
        break;

    default:
        // Rota não encontrada
        http_response_code(404);
        echo json_encode(["message" => "Rota não encontrada."]);
        break;
}