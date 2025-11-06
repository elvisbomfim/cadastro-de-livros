<?php

namespace App\Repositories\Eloquent;

use App\Models\Assunto as AssuntoModel;
use Core\Domain\Entity\Assunto;
use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\ValueObject\Descricao;
use Core\Domain\ValueObject\Uuid;

class AssuntoRepository implements AssuntoRepositoryInterface
{
    public function create(Assunto $assunto): Assunto
    {
        $assuntoModel = new AssuntoModel();
        $assuntoModel->id = (string) $assunto->getId();
        $assuntoModel->descricao = $assunto->getDescricao()->value();
        $assuntoModel->save();

        return $this->toEntity($assuntoModel);
    }
    

    public function findById(string $id): ?Assunto
    {
        $assuntoModel = AssuntoModel::find($id);

        if (!$assuntoModel) {
            return null;
        }

        return $this->toEntity($assuntoModel);
    }

    public function findAll(): array
    {
        $assuntosModel = AssuntoModel::all();

        return $assuntosModel->map(function ($assuntoModel) {
            return $this->toEntity($assuntoModel);
        })->toArray();
    }

    public function update(Assunto $assunto): Assunto
    {
        $assuntoModel = AssuntoModel::findOrFail((string) $assunto->getId());

        $assuntoModel->update([
            'descricao' => $assunto->getDescricao()->value(),
        ]);

        return $this->toEntity($assuntoModel);
    }

    public function delete(string $id): void
    {
        $assuntoModel = AssuntoModel::findOrFail($id);
        $assuntoModel->delete();
    }

    private function toEntity(AssuntoModel $assuntoModel): Assunto
    {
        return new Assunto(
            descricao: new Descricao($assuntoModel->descricao),
            id: new Uuid($assuntoModel->id)
        );
    }
}

