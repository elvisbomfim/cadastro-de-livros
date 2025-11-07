<?php

namespace Tests\Unit\UseCase\Relatorio;

use Core\UseCase\Relatorio\LivrosPorCategoriaUseCase;
use Core\Domain\Repository\RelatorioRepositoryInterface;
use Mockery;

describe('Use Case LivrosPorCategoria', function () {
    
    test('deve retornar lista vazia quando não há dados', function () {
        $repository = Mockery::mock(RelatorioRepositoryInterface::class);
        $useCase = new LivrosPorCategoriaUseCase($repository);
        
        $repository->shouldReceive('livrosPorCategoria')
            ->once()
            ->andReturn([]);
        
        $dados = $useCase->execute();
        
        expect($dados)->toBeArray()
            ->and(count($dados))->toBe(0);
    });

    test('deve retornar dados de livros por categoria', function () {
        $repository = Mockery::mock(RelatorioRepositoryInterface::class);
        $useCase = new LivrosPorCategoriaUseCase($repository);
        
        $dadosEsperados = [
            (object) [
                'categoria' => 'Romance',
                'total_livros' => 5,
                'valor_total' => 150.00,
                'preco_medio' => 30.00
            ],
            (object) [
                'categoria' => 'Ficção Científica',
                'total_livros' => 3,
                'valor_total' => 90.00,
                'preco_medio' => 30.00
            ]
        ];
        
        $repository->shouldReceive('livrosPorCategoria')
            ->once()
            ->andReturn($dadosEsperados);
        
        $dados = $useCase->execute();
        
        expect($dados)->toBeArray()
            ->and(count($dados))->toBe(2)
            ->and($dados[0]->categoria)->toBe('Romance')
            ->and($dados[0]->total_livros)->toBe(5)
            ->and($dados[1]->categoria)->toBe('Ficção Científica');
    });

    test('deve chamar o repository uma vez', function () {
        $repository = Mockery::mock(RelatorioRepositoryInterface::class);
        $useCase = new LivrosPorCategoriaUseCase($repository);
        
        $repository->shouldReceive('livrosPorCategoria')
            ->once()
            ->andReturn([]);
        
        $useCase->execute();
    });

})->group('UseCase', 'Relatorio');

