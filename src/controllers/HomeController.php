<?php

namespace Kakashi\Filmes\Controllers;

class HomeController {
    public function exibirHome() {
        // Redireciona para a página inicial ou exibe uma mensagem.
        include __DIR__ . "/../../public/index.php";
    }
}