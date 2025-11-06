<?php

namespace App\Http\Controllers\Api;

use Core\UseCase\Autor\CriarAutorUseCase;
use Core\UseCase\Autor\ListarAutorUseCase;
use Core\UseCase\Autor\ListarAutoresUseCase;
use Core\UseCase\Autor\AtualizarAutorUseCase;
use Core\UseCase\Autor\DeletarAutorUseCase;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\Exception\EntityValidationException;
use InvalidArgumentException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutorController extends Controller
{
    public function __construct(
        private CriarAutorUseCase $criarAutorUseCase,
        private ListarAutorUseCase $listarAutorUseCase,
        private ListarAutoresUseCase $listarAutoresUseCase,
        private AtualizarAutorUseCase $atualizarAutorUseCase,
        private DeletarAutorUseCase $deletarAutorUseCase
    ) {}

    public function index(): JsonResponse
    {
        $autores = $this->listarAutoresUseCase->execute();

        $items = array_map(function ($autor) {
            return [
                'id' => $autor->getId()->value(),
                'nome' => $autor->getNome()->value(),
            ];
        }, $autores);

        return response()->json([
            'data' => $items,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $autor = $this->criarAutorUseCase->execute(
                nome: $request->input('nome') ?? ''
            );

            return response()->json([
                'data' => [
                    'id' => $autor->getId()->value(),
                    'nome' => $autor->getNome()->value(),
                ],
            ], 201);
        } catch (EntityValidationException | InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $autor = $this->listarAutorUseCase->execute($id);

            return response()->json([
                'data' => [
                    'id' => $autor->getId()->value(),
                    'nome' => $autor->getNome()->value(),
                ],
            ], 200);
        } catch (LivroNaoEncontradoException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $autor = $this->atualizarAutorUseCase->execute(
                id: $id,
                nome: $request->input('nome') ?? ''
            );

            return response()->json([
                'data' => [
                    'id' => $autor->getId()->value(),
                    'nome' => $autor->getNome()->value(),
                ],
            ], 200);
        } catch (LivroNaoEncontradoException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        } catch (EntityValidationException | InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->deletarAutorUseCase->execute($id);

            return response()->json([], 204);
        } catch (LivroNaoEncontradoException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}

