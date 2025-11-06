<?php

namespace Core\Domain\Entity;

use Core\Domain\ValueObject\Uuid;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\Moeda;
use Core\Domain\Exception\EntityValidationException;

class Livro extends Entity
{
    protected array $autores = [];
    protected array $assuntos = [];

    public function __construct(
        protected Titulo $titulo,
        protected Editora $editora,
        protected Edicao $edicao,
        protected AnoPublicacao $anoPublicacao,
        protected Moeda $preco,
        protected ?Uuid $id = null,        
    ) {

        $this->id = $this->id ?? Uuid::random();

        $this->validation();
    }

    public function getTitulo(): Titulo
    {
        return $this->titulo;
    }

    public function getEditora(): Editora
    {
        return $this->editora;
    }

    public function getEdicao(): Edicao
    {
        return $this->edicao;
    }

    public function getAnoPublicacao(): AnoPublicacao
    {
        return $this->anoPublicacao;
    }

    public function getPreco(): Moeda
    {
        return $this->preco;
    }

    public function atualizarEdicao(int $edicao): void
    {
        if ($edicao <= $this->edicao->value()) {
            throw new EntityValidationException('A nova edição deve ser maior que a edição atual');
        }

        $this->edicao = new Edicao($edicao);
    }

    public function adicionarAutor(string $autorId): void
    {
        if (in_array($autorId, $this->autores)) {
            throw new EntityValidationException('Autor já adicionado');
        }

        $this->autores[] = $autorId;
    }

    public function removerAutor(string $autorId): void
    {
        if (!in_array($autorId, $this->autores)) {
            throw new EntityValidationException('Autor não encontrado');
        }

        $this->autores = array_values(array_filter($this->autores, function($id) use ($autorId) {
            return $id !== $autorId;
        }));
    }

    public function autores(): array
    {
        return $this->autores;
    }

    public function adicionarAssunto(string $assuntoId): void
    {
        if (in_array($assuntoId, $this->assuntos)) {
            throw new EntityValidationException('Assunto já adicionado');
        }

        $this->assuntos[] = $assuntoId;
    }

    public function removerAssunto(string $assuntoId): void
    {
        if (!in_array($assuntoId, $this->assuntos)) {
            throw new EntityValidationException('Assunto não encontrado');
        }

        $this->assuntos = array_values(array_filter($this->assuntos, function($id) use ($assuntoId) {
            return $id !== $assuntoId;
        }));
    }

    public function assuntos(): array
    {
        return $this->assuntos;
    }

    protected function validation(): void 
    {
        if ($this->titulo->value() === '') {
            throw new EntityValidationException('Titulo é obrigatório');
        }

        if ($this->editora->value() === '') {
            throw new EntityValidationException('Editora é obrigatório');
        }

        if ($this->edicao->value() <= 0) {
            throw new EntityValidationException('Edição é obrigatório');
        }

        
        
    }
}