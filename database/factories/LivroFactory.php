<?php

namespace Database\Factories;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Assunto;
use Illuminate\Database\Eloquent\Factories\Factory;

class LivroFactory extends Factory
{
    protected $model = Livro::class;

    public function definition(): array
    {
        $editoras = [
            'Editora Globo',
            'Companhia das Letras',
            'Editora Record',
            'Editora Saraiva',
            'Editora Ática',
            'Editora Moderna',
            'Editora Scipione',
            'Editora FTD',
            'Editora Melhoramentos',
            'Editora Nova Fronteira',
            'Editora Rocco',
            'Editora Intrínseca',
            'Editora Sextante',
            'Editora Planeta',
            'Editora Leya',
        ];

        $titulos = [
            'O Segredo dos Códigos',
            'A Jornada do Herói',
            'Mistérios do Passado',
            'Aventuras no Espaço',
            'O Poder da Mente',
            'Histórias de Amor',
            'Contos Fantásticos',
            'A Arte da Guerra',
            'O Código da Vida',
            'Viagem ao Desconhecido',
            'Segredos Revelados',
            'O Último Reino',
            'A Chave Perdida',
            'Tempos Modernos',
            'O Destino Escrito',
        ];

        return [
            'titulo' => $this->faker->randomElement($titulos) . ' ' . $this->faker->numberBetween(1, 10),
            'editora' => $this->faker->randomElement($editoras),
            'edicao' => $this->faker->numberBetween(1, 10),
            'ano_publicacao' => $this->faker->numberBetween(1990, now()->year),
            'preco' => $this->faker->randomFloat(2, 20.00, 200.00),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Livro $livro) {
            $autoresCount = Autor::count();
            $assuntosCount = Assunto::count();

            if ($autoresCount > 0 && $assuntosCount > 0) {
                $autoresQty = $this->faker->numberBetween(1, min(3, $autoresCount));
                $assuntosQty = $this->faker->numberBetween(1, min(3, $assuntosCount));

                $autores = Autor::inRandomOrder()->limit($autoresQty)->pluck('id');
                $assuntos = Assunto::inRandomOrder()->limit($assuntosQty)->pluck('id');

                if ($autores->isNotEmpty()) {
                    $livro->autors()->attach($autores);
                }

                if ($assuntos->isNotEmpty()) {
                    $livro->assuntos()->attach($assuntos);
                }
            }
        });
    }
}

