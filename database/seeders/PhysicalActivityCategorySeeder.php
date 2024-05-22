<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhysicalActivityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //in portuguese
        //list of 10 physical activity categories with description
        $categories = [
            ['name' => 'Aeróbico', 'description' => 'Atividades que envolvem movimentos repetitivos e rítmicos, como corrida, natação e ciclismo.'],
            ['name' => 'Anaeróbico', 'description' => 'Atividades de alta intensidade e curta duração, como musculação e sprint.'],
            ['name' => 'Flexibilidade', 'description' => 'Atividades que melhoram a amplitude de movimento das articulações, como alongamento e yoga.'],
            ['name' => 'Equilíbrio', 'description' => 'Atividades que melhora a capacidade de manter o corpo em equilíbrio, como pilates e slackline.'],
            ['name' => 'Força', 'description' => 'Atividades que envolvem resistência muscular, como musculação e calistenia.'],
            ['name' => 'Agilidade', 'description' => 'Atividades que melhoram a capacidade de se mover rapidamente e mudar de direção, como treinamento funcional e parkour.'],
            ['name' => 'Coordenação', 'description' => 'Atividades que melhoram a capacidade de coordenar movimentos, como dança e ginástica rítmica.'],
            ['name' => 'Velocidade', 'description' => 'Atividades que melhoram a capacidade de se mover rapidamente, como corrida e natação.'],
            ['name' => 'Potência', 'description' => 'Atividades que envolvem força e velocidade, como levantamento de peso e salto em altura.'],
            ['name' => 'Resistência', 'description' => 'Atividades que melhoram a capacidade de suportar esforço físico por longos períodos, como corrida de longa distância e ciclismo.'],
        ];

        foreach ($categories as $category) {
            \App\Models\PhysicalActivityCategory::create($category);
        }
    }
}
