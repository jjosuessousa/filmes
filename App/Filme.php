<?php
namespace Kakashi\Filmes;

class Filme {
    private $id;
    private $titulo;
    private $descricao;
    private $imagem;
    private $trailer;
    private $categoria;

    // Métodos get e set para 'id'
    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    // Métodos get e set para 'titulo'
    public function getTitulo() {
        return $this->titulo;
    }
    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    // Métodos get e set para 'descricao'
    public function getDescricao() {
        return $this->descricao;
    }
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    // Métodos get e set para 'imagem'
    public function getImagem() {
        return $this->imagem;
    }
    public function setImagem($imagem) {
        $this->imagem = $imagem;
    }

    // Métodos get e set para 'trailer'
    public function getTrailer() {
        return $this->trailer;
    }
    public function setTrailer($trailer) {
        $this->trailer = $trailer;
    }

    // Métodos get e set para 'categoria'
    public function getCategoria() {
        return $this->categoria;
    }
    public function setCategoria($categoria) {
        $this->categoria = $categoria;
    }
}
