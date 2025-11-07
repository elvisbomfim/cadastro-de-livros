<?php

namespace App\Http\Controllers\Api;

use Core\UseCase\Livro\CriarLivroUseCase;
use Core\UseCase\Livro\ListarLivroUseCase;
use Core\UseCase\Livro\ListarLivrosUseCase;
use Core\UseCase\Livro\AtualizarLivroUseCase;
use Core\UseCase\Livro\DeletarLivroUseCase;
use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\Exception\EntityValidationException;
use InvalidArgumentException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LivroController extends Controller
{
    public function __construct(
        private CriarLivroUseCase $criarLivroUseCase,
        private ListarLivroUseCase $listarLivroUseCase,
        private ListarLivrosUseCase $listarLivrosUseCase,
        private AtualizarLivroUseCase $atualizarLivroUseCase,
        private DeletarLivroUseCase $deletarLivroUseCase,
        private LivroRepositoryInterface $repository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filter = $request->get('filter', '');
        $order = $request->get('order', 'DESC');
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 15);

        $pagination = $this->repository->paginate($filter, $order, $page, $perPage);

        $livrosModel = \App\Models\Livro::with(['autors', 'assuntos'])
            ->whereIn('id', array_map(fn($l) => $l->getId()->value(), $pagination->items()))
            ->get()
            ->keyBy('id');

        $items = array_map(function ($livro) use ($livrosModel) {
            $model = $livrosModel->get($livro->getId()->value());
            return [
                'id' => $livro->getId()->value(),
                'titulo' => $livro->getTitulo()->value(),
                'editora' => $livro->getEditora()->value(),
                'edicao' => $livro->getEdicao()->value(),
                'ano_publicacao' => $livro->getAnoPublicacao()->value(),
                'preco' => $livro->getPreco()->formatar(),
                'preco_value' => $livro->getPreco()->value(),
                'autores' => $model ? $model->autors->map(fn($a) => ['id' => $a->id, 'nome' => $a->nome])->toArray() : [],
                'assuntos' => $model ? $model->assuntos->map(fn($a) => ['id' => $a->id, 'descricao' => $a->descricao])->toArray() : [],
            ];
        }, $pagination->items());

        return response()->json([
            'data' => $items,
            'meta' => [
                'total' => $pagination->total(),
                'per_page' => $pagination->perPage(),
                'current_page' => $pagination->currentPage(),
                'last_page' => $pagination->lastPage(),
                'first_page' => $pagination->firstPage(),
                'from' => $pagination->from(),
                'to' => $pagination->to(),
            ],
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $livro = $this->criarLivroUseCase->execute(
                titulo: $request->input('titulo') ?? '',
                editora: $request->input('editora') ?? '',
                edicao: (int) ($request->input('edicao') ?? 0),
                anoPublicacao: (int) ($request->input('ano_publicacao') ?? 0),
                preco: (float) ($request->input('preco') ?? 0)
            );

            // Adicionar relacionamentos usando o modelo diretamente
            $autoresIds = $request->input('autores', []);
            $assuntosIds = $request->input('assuntos', []);
            
            $livroModel = \App\Models\Livro::find($livro->getId()->value());
            if ($livroModel) {
                if (!empty($autoresIds)) {
                    $livroModel->autors()->sync($autoresIds);
                }
                if (!empty($assuntosIds)) {
                    $livroModel->assuntos()->sync($assuntosIds);
                }
            }
            
            // Recarregar com relacionamentos
            $livroModel = \App\Models\Livro::with(['autors', 'assuntos'])->find($livro->getId()->value());

            return response()->json([
                'data' => [
                    'id' => $livro->getId()->value(),
                    'titulo' => $livro->getTitulo()->value(),
                    'editora' => $livro->getEditora()->value(),
                    'edicao' => $livro->getEdicao()->value(),
                    'ano_publicacao' => $livro->getAnoPublicacao()->value(),
                    'preco' => $livro->getPreco()->formatar(),
                'preco_value' => $livro->getPreco()->value(),
                    'autores' => $livroModel ? $livroModel->autors->map(fn($a) => ['id' => $a->id, 'nome' => $a->nome])->toArray() : [],
                    'assuntos' => $livroModel ? $livroModel->assuntos->map(fn($a) => ['id' => $a->id, 'descricao' => $a->descricao])->toArray() : [],
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
            $livro = $this->listarLivroUseCase->execute($id);

            $livroModel = \App\Models\Livro::with(['autors', 'assuntos'])->find($livro->getId()->value());

            return response()->json([
                'data' => [
                    'id' => $livro->getId()->value(),
                    'titulo' => $livro->getTitulo()->value(),
                    'editora' => $livro->getEditora()->value(),
                    'edicao' => $livro->getEdicao()->value(),
                    'ano_publicacao' => $livro->getAnoPublicacao()->value(),
                    'preco' => $livro->getPreco()->formatar(),
                'preco_value' => $livro->getPreco()->value(),
                    'autores' => $livroModel ? $livroModel->autors->map(fn($a) => ['id' => $a->id, 'nome' => $a->nome])->toArray() : [],
                    'assuntos' => $livroModel ? $livroModel->assuntos->map(fn($a) => ['id' => $a->id, 'descricao' => $a->descricao])->toArray() : [],
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
            $livro = $this->atualizarLivroUseCase->execute(
                id: $id,
                titulo: $request->input('titulo') ?? '',
                editora: $request->input('editora') ?? '',
                edicao: (int) ($request->input('edicao') ?? 0),
                anoPublicacao: (int) ($request->input('ano_publicacao') ?? 0),
                preco: (float) ($request->input('preco') ?? 0)
            );

            // Atualizar relacionamentos usando o modelo diretamente
            $autoresIds = $request->input('autores', []);
            $assuntosIds = $request->input('assuntos', []);
            
            $livroModel = \App\Models\Livro::find($livro->getId()->value());
            if ($livroModel) {
                if (!empty($autoresIds)) {
                    $livroModel->autors()->sync($autoresIds);
                }
                if (!empty($assuntosIds)) {
                    $livroModel->assuntos()->sync($assuntosIds);
                }
            }
            
            // Recarregar o livro com relacionamentos atualizados
            $livroModel = \App\Models\Livro::with(['autors', 'assuntos'])->find($livro->getId()->value());

            return response()->json([
                'data' => [
                    'id' => $livro->getId()->value(),
                    'titulo' => $livro->getTitulo()->value(),
                    'editora' => $livro->getEditora()->value(),
                    'edicao' => $livro->getEdicao()->value(),
                    'ano_publicacao' => $livro->getAnoPublicacao()->value(),
                    'preco' => $livro->getPreco()->formatar(),
                'preco_value' => $livro->getPreco()->value(),
                    'autores' => $livroModel ? $livroModel->autors->map(fn($a) => ['id' => $a->id, 'nome' => $a->nome])->toArray() : [],
                    'assuntos' => $livroModel ? $livroModel->assuntos->map(fn($a) => ['id' => $a->id, 'descricao' => $a->descricao])->toArray() : [],
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
            $this->deletarLivroUseCase->execute($id);

            return response()->json([], 204);
        } catch (LivroNaoEncontradoException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}

