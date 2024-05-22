<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhysicalActivitySportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $sports = [
            // Aeróbico
            [
                'name' => 'Corrida',
                'description' => 'Atividade física que envolve correr.',
                'category_id' => 1,
                'calories_burned_per_minute' => 9.33,
                'metabolic_equivalent' => 8,
            ],
            [
                'name' => 'Natação',
                'description' => 'Atividade física que envolve nadar.',
                'category_id' => 1,
                'calories_burned_per_minute' => 8.17,
                'metabolic_equivalent' => 7,
            ],
            [
                'name' => 'Ciclismo',
                'description' => 'Atividade física que envolve pedalar.',
                'category_id' => 1,
                'calories_burned_per_minute' => 8.75,
                'metabolic_equivalent' => 7.5,
            ],
            // Anaeróbico
            [
                'name' => 'Musculação',
                'description' => 'Exercícios de alta intensidade e curta duração para desenvolver músculos.',
                'category_id' => 2,
                'calories_burned_per_minute' => 5.83,
                'metabolic_equivalent' => 5,
            ],
            [
                'name' => 'Sprint',
                'description' => 'Corrida rápida de curta distância.',
                'category_id' => 2,
                'calories_burned_per_minute' => 23.33,
                'metabolic_equivalent' => 20,
            ],
            [
                'name' => 'Levantamento de peso',
                'description' => 'Atividade física que envolve levantar pesos pesados.',
                'category_id' => 2,
                'calories_burned_per_minute' => 7,
                'metabolic_equivalent' => 6,
            ],
            // Flexibilidade
            [
                'name' => 'Alongamento',
                'description' => 'Exercícios que melhoram a amplitude de movimento das articulações.',
                'category_id' => 3,
                'calories_burned_per_minute' => 2.92,
                'metabolic_equivalent' => 2.5,
            ],
            [
                'name' => 'Yoga',
                'description' => 'Prática que envolve posturas físicas, meditação e controle da respiração.',
                'category_id' => 3,
                'calories_burned_per_minute' => 3.5,
                'metabolic_equivalent' => 3,
            ],
            [
                'name' => 'Pilates',
                'description' => 'Sistema de exercícios que se concentra na força do núcleo e na flexibilidade.',
                'category_id' => 3,
                'calories_burned_per_minute' => 4.67,
                'metabolic_equivalent' => 4,
            ],
            // Equilíbrio
            [
                'name' => 'Pilates',
                'description' => 'Sistema de exercícios que se concentra na força do núcleo e na flexibilidade.',
                'category_id' => 4,
                'calories_burned_per_minute' => 4.67,
                'metabolic_equivalent' => 4,
            ],
            [
                'name' => 'Slackline',
                'description' => 'Atividade de equilíbrio que envolve caminhar sobre uma fita estreita.',
                'category_id' => 4,
                'calories_burned_per_minute' => 5.83,
                'metabolic_equivalent' => 5,
            ],
            [
                'name' => 'Tai Chi',
                'description' => 'Arte marcial chinesa que se concentra em movimentos lentos e suaves.',
                'category_id' => 4,
                'calories_burned_per_minute' => 3.5,
                'metabolic_equivalent' => 3,
            ],
            // Força
            [
                'name' => 'Calistenia',
                'description' => 'Exercícios de peso corporal que melhoram a força e a resistência muscular.',
                'category_id' => 5,
                'calories_burned_per_minute' => 5.83,
                'metabolic_equivalent' => 5,
            ],
            [
                'name' => 'Musculação',
                'description' => 'Exercícios de alta intensidade e curta duração para desenvolver músculos.',
                'category_id' => 5,
                'calories_burned_per_minute' => 5.83,
                'metabolic_equivalent' => 5,
            ],
            [
                'name' => 'Treinamento com kettlebell',
                'description' => 'Exercícios com kettlebells que melhoram a força e a resistência muscular.',
                'category_id' => 5,
                'calories_burned_per_minute' => 7,
                'metabolic_equivalent' => 6,
            ],
            // Agilidade
            [
                'name' => 'Treinamento funcional',
                'description' => 'Exercícios que melhoram a capacidade de realizar atividades diárias.',
                'category_id' => 6,
                'calories_burned_per_minute' => 9.33,
                'metabolic_equivalent' => 8,
            ],
            [
                'name' => 'Parkour',
                'description' => 'Disciplina física que envolve superar obstáculos através de corrida, salto e escalada.',
                'category_id' => 6,
                'calories_burned_per_minute' => 11.67,
                'metabolic_equivalent' => 10,
            ],
            [
                'name' => 'Treinamento em circuito',
                'description' => 'Treinamento que envolve a realização de uma série de exercícios em um circuito.',
                'category_id' => 6,
                'calories_burned_per_minute' => 9.33,
                'metabolic_equivalent' => 8,
            ],
            // Coordenação
            [
                'name' => 'Dança',
                'description' => 'Atividade física que envolve movimentos ritmados ao som de música.',
                'category_id' => 7,
                'calories_burned_per_minute' => 8.17,
                'metabolic_equivalent' => 7,
            ],
            [
                'name' => 'Ginástica rítmica',
                'description' => 'Esporte que combina elementos de balé, ginástica e dança.',
                'category_id' => 7,
                'calories_burned_per_minute' => 7,
                'metabolic_equivalent' => 6,
            ],
            [
                'name' => 'Arco e flecha',
                'description' => 'Esporte de precisão que envolve o uso de um arco e flechas.',
                'category_id' => 7,
                'calories_burned_per_minute' => 4.08,
                'metabolic_equivalent' => 3.5,
            ],
            // Velocidade
            [
                'name' => 'Corrida de curta distância',
                'description' => 'Corrida em distâncias curtas com alta intensidade.',
                'category_id' => 8,
                'calories_burned_per_minute' => 18.67,
                'metabolic_equivalent' => 16,
            ],
            [
                'name' => 'Natação de velocidade',
                'description' => 'Natação em alta velocidade por distâncias curtas.',
                'category_id' => 8,
                'calories_burned_per_minute' => 15.17,
                'metabolic_equivalent' => 13,
            ],
            [
                'name' => 'Patinação de velocidade',
                'description' => 'Patinação em alta velocidade em distâncias curtas.',
                'category_id' => 8,
                'calories_burned_per_minute' => 14,
                'metabolic_equivalent' => 12,
            ],
            // Potência
            [
                'name' => 'Levantamento de peso',
                'description' => 'Atividade física que envolve levantar pesos pesados.',
                'category_id' => 9,
                'calories_burned_per_minute' => 7,
                'metabolic_equivalent' => 6,
            ],
            [
                'name' => 'Salto em altura',
                'description' => 'Esporte que envolve saltar sobre uma barra horizontal.',
                'category_id' => 9,
                'calories_burned_per_minute' => 14,
                'metabolic_equivalent' => 12,
            ],
            [
                'name' => 'Lançamento de dardo',
                'description' => 'Esporte que envolve lançar um dardo o mais longe possível.',
                'category_id' => 9,
                'calories_burned_per_minute' => 11.67,
                'metabolic_equivalent' => 10,
            ],
            // Resistência
            [
                'name' => 'Corrida de longa distância',
                'description' => 'Corrida em distâncias longas.',
                'category_id' => 10,
                'calories_burned_per_minute' => 12.83,
                'metabolic_equivalent' => 11,
            ],
            [
                'name' => 'Ciclismo de longa distância',
                'description' => 'Ciclismo em distâncias longas.',
                'category_id' => 10,
                'calories_burned_per_minute' => 10.5,
                'metabolic_equivalent' => 9,
            ],
            [
                'name' => 'Triatlo',
                'description' => 'Competição de resistência que envolve natação, ciclismo e corrida.',
                'category_id' => 10,
                'calories_burned_per_minute' => 16.33,
                'metabolic_equivalent' => 14,
            ],
        ];

        foreach ($sports as $sport) {
            \App\Models\PhysicalActivitySport::create($sport);
        }
    }
}
