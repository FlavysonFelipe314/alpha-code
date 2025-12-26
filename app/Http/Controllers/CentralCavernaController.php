<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Tarefa;
use App\Models\Pomodoro;
use App\Models\Objetivo;
use App\Models\Agenda;
use App\Models\Treino;
use App\Models\Ritual;

class CentralCavernaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek();
        $weekEnd = $today->copy()->endOfWeek();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        
        // Estatísticas gerais
        $stats = [
            'tarefas' => [
                'total' => Tarefa::where('user_id', $user->id)->count(),
                'pendentes' => Tarefa::where('user_id', $user->id)->count(),
                'hoje' => Tarefa::where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->count(),
                'semana' => Tarefa::where('user_id', $user->id)
                    ->whereBetween('created_at', [$weekStart, $weekEnd])
                    ->count(),
            ],
            'objetivos' => [
                'total' => Objetivo::where('user_id', $user->id)->count(),
                'completados' => Objetivo::where('user_id', $user->id)->where('completed', true)->count(),
                'pendentes' => Objetivo::where('user_id', $user->id)->where('completed', false)->count(),
                'hoje' => Objetivo::where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->count(),
            ],
            'pomodoros' => [
                'hoje' => Pomodoro::where('user_id', $user->id)
                    ->whereDate('data', $today)
                    ->where('concluido', true)
                    ->count(),
                'hoje_minutos' => Pomodoro::where('user_id', $user->id)
                    ->whereDate('data', $today)
                    ->where('concluido', true)
                    ->sum('duracao_minutos') ?? 0,
                'semana' => Pomodoro::where('user_id', $user->id)
                    ->whereBetween('data', [$weekStart, $weekEnd])
                    ->where('concluido', true)
                    ->count(),
                'total_minutos' => Pomodoro::where('user_id', $user->id)
                    ->whereBetween('data', [$weekStart, $weekEnd])
                    ->where('concluido', true)
                    ->sum('duracao_minutos') ?? 0,
                'total_geral' => Pomodoro::where('user_id', $user->id)
                    ->where('concluido', true)
                    ->count(),
            ],
            'agenda' => [
                'hoje' => Agenda::where('user_id', $user->id)
                    ->whereDate('date', $today)
                    ->where('completed', false)
                    ->count(),
                'completados_hoje' => Agenda::where('user_id', $user->id)
                    ->whereDate('date', $today)
                    ->where('completed', true)
                    ->count(),
                'proximos' => Agenda::where('user_id', $user->id)
                    ->whereDate('date', '>=', $today)
                    ->where('completed', false)
                    ->orderBy('date')
                    ->orderBy('time')
                    ->limit(5)
                    ->get(),
            ],
            'treinos' => [
                'hoje' => Treino::where('user_id', $user->id)
                    ->whereDate('data', $today)
                    ->count(),
                'semana' => Treino::where('user_id', $user->id)
                    ->whereBetween('data', [$weekStart, $weekEnd])
                    ->count(),
                'realizados' => Treino::where('user_id', $user->id)
                    ->whereBetween('data', [$weekStart, $weekEnd])
                    ->where('realizado', true)
                    ->count(),
                'total_mes' => Treino::where('user_id', $user->id)
                    ->whereBetween('data', [$monthStart, $monthEnd])
                    ->count(),
            ],
            'financas' => [
                'total_mes' => \App\Models\Financa::where('user_id', $user->id)
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count(),
            ],
        ];
        
        // Produtividade semanal (dados para gráfico)
        $produtividade = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $dayName = $date->format('D');
            
            $pomodoros = Pomodoro::where('user_id', $user->id)
                ->whereDate('data', $date)
                ->where('concluido', true)
                ->sum('duracao_minutos') ?? 0;
            
            $produtividade[] = [
                'dia' => $date->format('d/m'),
                'dia_nome' => $this->getDayName($dayName),
                'minutos' => $pomodoros,
                'altura' => min(100, ($pomodoros / 480) * 100), // Normalizado para máximo de 8 horas
            ];
        }
        
        // Atividades recentes
        $atividadesRecentes = collect()
            ->merge(
                Agenda::where('user_id', $user->id)
                    ->where('completed', true)
                    ->orderBy('updated_at', 'desc')
                    ->limit(3)
                    ->get()
                    ->map(function($item) {
                        return [
                            'tipo' => 'agenda',
                            'titulo' => $item->title,
                            'data' => $item->updated_at,
                            'icon' => 'calendar',
                        ];
                    })
            )
            ->merge(
                Tarefa::where('user_id', $user->id)
                    ->orderBy('updated_at', 'desc')
                    ->limit(3)
                    ->get()
                    ->map(function($item) {
                        return [
                            'tipo' => 'tarefa',
                            'titulo' => $item->titulo,
                            'data' => $item->updated_at,
                            'icon' => 'check-circle',
                        ];
                    })
            )
            ->sortByDesc('data')
            ->take(5);
        
        return view('central-caverna', compact('stats', 'produtividade', 'atividadesRecentes'));
    }
    
    private function getDayName($day)
    {
        $days = [
            'Mon' => 'Seg',
            'Tue' => 'Ter',
            'Wed' => 'Qua',
            'Thu' => 'Qui',
            'Fri' => 'Sex',
            'Sat' => 'Sáb',
            'Sun' => 'Dom',
        ];
        return $days[$day] ?? $day;
    }
}