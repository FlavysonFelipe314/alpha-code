<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForumCategoria;

class ForumCategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nome' => 'Geral',
                'descricao' => 'Discussões gerais sobre o sistema',
                'cor' => '#ef4444',
                'icone' => 'fa-comments',
                'ordem' => 1,
                'ativo' => true,
            ],
            [
                'nome' => 'Dúvidas',
                'descricao' => 'Tire suas dúvidas e ajude outros usuários',
                'cor' => '#3b82f6',
                'icone' => 'fa-question-circle',
                'ordem' => 2,
                'ativo' => true,
            ],
            [
                'nome' => 'Dicas e Truques',
                'descricao' => 'Compartilhe dicas e truques para usar o sistema',
                'cor' => '#10b981',
                'icone' => 'fa-lightbulb',
                'ordem' => 3,
                'ativo' => true,
            ],
            [
                'nome' => 'Sugestões',
                'descricao' => 'Sugira melhorias e novas funcionalidades',
                'cor' => '#f59e0b',
                'icone' => 'fa-lightbulb',
                'ordem' => 4,
                'ativo' => true,
            ],
        ];

        foreach ($categorias as $categoria) {
            ForumCategoria::create($categoria);
        }
    }
}