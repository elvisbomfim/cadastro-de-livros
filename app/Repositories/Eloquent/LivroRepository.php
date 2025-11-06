<?php

namespace App\Repositories\Eloquent;

use App\Models\Livro as LivroModel;
use Core\Domain\Entity\Livro;
use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Moeda;
use Core\Domain\ValueObject\Uuid;

class LivroRepository implements LivroRepositoryInterface
{
    public function create(Livro $livro): Livro
    {
        $livroModel = new LivroModel();
        $livroModel->id = (string) $livro->getId();
        $livroModel->titulo = $livro->getTitulo()->value();
        $livroModel->editora = $livro->getEditora()->value();
        $livroModel->edicao = $livro->getEdicao()->value();
        $livroModel->ano_publicacao = $livro->getAnoPublicacao()->value();
        $livroModel->preco = $livro->getPreco()->value();
        $livroModel->save();

        // Sincronizar relacionamentos
        if (!empty($livro->autores())) {
            $livroModel->autors()->sync($livro->autores());
        }

        if (!empty($livro->assuntos())) {
            $livroModel->assuntos()->sync($livro->assuntos());
        }

        return $this->toEntity($livroModel);
    }

    public function findById(string $id): ?Livro
    {
        $livroModel = LivroModel::with(['autors', 'assuntos'])->find($id);

        if (!$livroModel) {
            return null;
        }

        return $this->toEntity($livroModel);
    }

    public function findAll(): array
    {
        $livrosModel = LivroModel::with(['autors', 'assuntos'])->get();

        return $livrosModel->map(function ($livroModel) {
            return $this->toEntity($livroModel);
        })->toArray();
    }

    public function update(Livro $livro): Livro
    {
        $livroModel = LivroModel::findOrFail((string) $livro->getId());

        $livroModel->update([
            'titulo' => $livro->getTitulo()->value(),
            'editora' => $livro->getEditora()->value(),
            'edicao' => $livro->getEdicao()->value(),
            'ano_publicacao' => $livro->getAnoPublicacao()->value(),
            'preco' => $livro->getPreco()->value(),
        ]);

        // Sincronizar relacionamentos
        $livroModel->autors()->sync($livro->autores());
        $livroModel->assuntos()->sync($livro->assuntos());

        $livroModel->refresh();
        $livroModel->load(['autors', 'assuntos']);

        return $this->toEntity($livroModel);
    }

    public function delete(string $id): void
    {
        $livroModel = LivroModel::findOrFail($id);
        $livroModel->delete();
    }

    private function toEntity(LivroModel $livroModel): Livro
    {
        $livro = new Livro(
            titulo: new Titulo($livroModel->titulo),
            editora: new Editora($livroModel->editora),
            edicao: new Edicao($livroModel->edicao),
            anoPublicacao: new AnoPublicacao($livroModel->ano_publicacao),
            preco: new Moeda($livroModel->preco),
            id: new Uuid($livroModel->id)
        );

        // Carregar autores e assuntos existentes usando reflexão para evitar validação
        $reflection = new \ReflectionClass($livro);
        $autoresProperty = $reflection->getProperty('autores');
        $autoresProperty->setAccessible(true);
        $autoresProperty->setValue($livro, $livroModel->autors->pluck('id')->toArray());

        $assuntosProperty = $reflection->getProperty('assuntos');
        $assuntosProperty->setAccessible(true);
        $assuntosProperty->setValue($livro, $livroModel->assuntos->pluck('id')->toArray());

        return $livro;
    }
}

