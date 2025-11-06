<?php

namespace Database\Factories;

use App\Models\Assunto;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssuntoFactory extends Factory
{
    protected $model = Assunto::class;

    public function definition(): array
    {
        $assuntos = [
            'Ficção Científica',
            'Romance',
            'Fantasia',
            'Mistério',
            'Suspense',
            'Terror',
            'Aventura',
            'Biografia',
            'História',
            'Filosofia',
            'Ciência',
            'Tecnologia',
            'Programação',
            'Arte',
            'Música',
            'Literatura',
            'Poesia',
            'Teatro',
            'Cinema',
            'Fotografia',
            'Design',
            'Arquitetura',
            'Medicina',
            'Direito',
            'Educação',
            'Psicologia',
            'Sociologia',
            'Economia',
            'Negócios',
            'Autoajuda',
        ];

        return [
            'descricao' => $this->faker->randomElement($assuntos),
        ];
    }
}

