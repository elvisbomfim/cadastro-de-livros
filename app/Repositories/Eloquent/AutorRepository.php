<?php

namespace App\Repositories\Eloquent;

use App\Models\Autor as AutorModel;
use Core\Domain\Entity\Autor;
use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\ValueObject\Nome;
use Core\Domain\ValueObject\Uuid;

class AutorRepository implements AutorRepositoryInterface
{
    public function create(Autor $autor): Autor
    {
        $autorModel = new AutorModel();
        $autorModel->id = (string) $autor->getId();
        $autorModel->nome = $autor->getNome()->value();
        $autorModel->save();

        return $this->toEntity($autorModel);
    }

    public function findById(string $id): ?Autor
    {
        $autorModel = AutorModel::find($id);

        if (!$autorModel) {
            return null;
        }

        return $this->toEntity($autorModel);
    }

    public function findAll(): array
    {
        $autoresModel = AutorModel::all();

        return $autoresModel->map(function ($autorModel) {
            return $this->toEntity($autorModel);
        })->toArray();
    }

    public function update(Autor $autor): Autor
    {
        $autorModel = AutorModel::findOrFail((string) $autor->getId());

        $autorModel->update([
            'nome' => $autor->getNome()->value(),
        ]);

        return $this->toEntity($autorModel);
    }

    public function delete(string $id): void
    {
        $autorModel = AutorModel::findOrFail($id);
        $autorModel->delete();
    }

    private function toEntity(AutorModel $autorModel): Autor
    {
        return new Autor(
            nome: new Nome($autorModel->nome),
            id: new Uuid($autorModel->id)
        );
    }
}

