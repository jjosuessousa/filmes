<?php

// Habilitar CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

use Kakashi\Filmes\Controllers\FilmeController;
use Kakashi\Filmes\Controllers\GeneroController;
use Kakashi\Filmes\Controllers\HomeController;
use Kakashi\Filmes\Core\Conexao;

require_once __DIR__ . '/../vendor/autoload.php';

// Configurar conexão com o banco
$conexao = Conexao::conectar();

// Obter a rota solicitada na URL
$rota = $_GET['rota'] ?? 'filmes'; // Rota padrão será 'filmes'

function listarFilmes($conexao) {
    try {
        $sql = "SELECT filmes.*, generos.nome AS genero_nome 
                FROM filmes 
                LEFT JOIN generos ON filmes.genero_id = generos.id";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();
        $filmes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verifica se o caminho da imagem existe, caso contrário, define uma imagem padrão
        foreach ($filmes as &$filme) {
            if (empty($filme['capa'])) {
                $filme['capa'] = 'http://localhost/filmes/uploads/'; // Imagem padrão
            }
        }

        http_response_code(200);
        echo json_encode($filmes);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Erro ao listar filmes.", "error" => $e->getMessage()]);
    }
}

// Função para cadastrar filme
function cadastrarFilme($conexao) {
    try {
        // Verificar e tratar o upload da imagem
        if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
            $diretorioUploads = __DIR__ . '/../uploads/';
            $nomeArquivo = uniqid() . '_' . basename($_FILES['capa']['name']);
            $caminhoCompleto = $diretorioUploads . $nomeArquivo;

            // Criar pasta de uploads se não existir
            if (!is_dir($diretorioUploads)) {
                mkdir($diretorioUploads, 0755, true);
            }

            // Mover o arquivo para o diretório de uploads
            if (!move_uploaded_file($_FILES['capa']['tmp_name'], $caminhoCompleto)) {
                throw new Exception("Erro ao salvar a imagem.");
            }

            // Guardar o caminho da imagem
            $caminhoCapa = 'uploads/' . $nomeArquivo;
        } else {
            $caminhoCapa = '/filmes/uploads/imagem_padrao.jpg'; // Imagem padrão
        }

        // Dados do formulário
        $titulo = $_POST['titulo'] ?? null;
        $sinopse = $_POST['sinopse'] ?? null;
        $genero_id = $_POST['genero'] ?? null;
        $trailer = $_POST['trailer'] ?? null;
        $lancamento = $_POST['lancamento'] ?? null;
        $duracao = $_POST['duracao'] ?? null;

        // Validar dados obrigatórios
        if (!$titulo || !$sinopse || !$genero_id || !$trailer || !$lancamento || !$duracao) {
            http_response_code(400);
            echo json_encode(["message" => "Todos os campos obrigatórios devem ser preenchidos."]);
            return;
        }

        // Inserir os dados no banco de dados
        $sql = "INSERT INTO filmes (titulo, sinopse, genero_id, capa, trailer, lancamento, duracao) 
                VALUES (:titulo, :sinopse, :genero_id, :caminho_capa, :trailer, :lancamento, :duracao)";
        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':sinopse', $sinopse, PDO::PARAM_STR);
        $stmt->bindParam(':genero_id', $genero_id, PDO::PARAM_INT);
        $stmt->bindParam(':caminho_capa', $caminhoCapa, PDO::PARAM_STR);
        $stmt->bindParam(':trailer', $trailer, PDO::PARAM_STR);
        $stmt->bindParam(':lancamento', $lancamento, PDO::PARAM_STR);
        $stmt->bindParam(':duracao', $duracao, PDO::PARAM_INT);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Filme cadastrado com sucesso!", "capa" => $caminhoCapa]);
        } else {
            throw new Exception("Erro ao cadastrar filme.");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => $e->getMessage()]);
    }
}

// Função para atualizar filme
function atualizarFilme($conexao) {
    try {
        $id = $_POST['id'] ?? null;
        $titulo = $_POST['titulo'] ?? null;
        $sinopse = $_POST['sinopse'] ?? null;
        $genero_id = $_POST['genero'] ?? null;
        $trailer = $_POST['trailer'] ?? null;
        $lancamento = $_POST['lancamento'] ?? null;
        $duracao = $_POST['duracao'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["message" => "ID do filme não fornecido."]);
            return;
        }

        if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
            $diretorioUploads = __DIR__ . '/../uploads/';
            $nomeArquivo = uniqid() . '_' . basename($_FILES['capa']['name']);
            $caminhoCompleto = $diretorioUploads . $nomeArquivo;

            if (!is_dir($diretorioUploads)) {
                mkdir($diretorioUploads, 0755, true);
            }

            if (!move_uploaded_file($_FILES['capa']['tmp_name'], $caminhoCompleto)) {
                throw new Exception("Erro ao salvar a imagem.");
            }

            $caminhoCapa = 'uploads/' . $nomeArquivo;
        } else {
            $caminhoCapa = null;
        }

        $sql = "UPDATE filmes SET 
                    titulo = :titulo, 
                    sinopse = :sinopse, 
                    genero_id = :genero_id, 
                    capa = IF(:capa IS NOT NULL, :capa, capa), 
                    trailer = :trailer, 
                    lancamento = :lancamento, 
                    duracao = :duracao 
                WHERE id = :id";
        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':sinopse', $sinopse, PDO::PARAM_STR);
        $stmt->bindParam(':genero_id', $genero_id, PDO::PARAM_INT);
        $stmt->bindParam(':capa', $caminhoCapa, PDO::PARAM_STR);
        $stmt->bindParam(':trailer', $trailer, PDO::PARAM_STR);
        $stmt->bindParam(':lancamento', $lancamento, PDO::PARAM_STR);
        $stmt->bindParam(':duracao', $duracao, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Filme atualizado com sucesso!"]);
        } else {
            throw new Exception("Erro ao atualizar o filme.");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => $e->getMessage()]);
    }
}

// Função para usar o GeneroController
function usarGeneroController($rota, $conexao) {
    $generoController = new GeneroController($conexao);

    if ($rota === 'generos' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $generoController->listarGeneros();
        exit;
    }

    if ($rota === 'generos' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $dados = json_decode(file_get_contents("php://input"), true);
        $generoController->criarGenero($dados);
        exit;
    }

    if ($rota === 'generos/atualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $dados = json_decode(file_get_contents("php://input"), true);
        $id = $dados['id'] ?? null;
        $generoController->atualizarGenero($id, $dados);
        exit;
    }
}

// Rotas principais
try {
    if ($rota === 'filmes' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        listarFilmes($conexao);
        exit;
    }

    if ($rota === 'filmes' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        cadastrarFilme($conexao);
        exit;
    }

    if ($rota === 'filmes/atualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        atualizarFilme($conexao);
        exit;
    }

    // Chamar funções do GeneroController
    usarGeneroController($rota, $conexao);

    // Rota não encontrada
    http_response_code(404);
    echo json_encode(["message" => "Rota não encontrada."]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erro interno.", "error" => $e->getMessage()]);
}