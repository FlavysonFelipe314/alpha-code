<?php

namespace App\Http\Services;

use Cloudstudio\Ollama\Facades\Ollama;

class TreinoService
{
    public static function generateWorkoutPlan($data){
        set_time_limit(300);
        $response = Ollama::agent('
    Você é um agente de inteligência artificial especializado em treinamento físico e prescrição de exercícios personalizados.  
    Sua tarefa é gerar **vários JSONs** representando treinos completos para uma semana (um treino por dia), seguindo o formato abaixo:

{
  "nome": "Treino de Peito e Tríceps",
  "day": "SEG",
  "horario": "18:00:00",
  "observacoes": "Focar na execução correta dos movimentos",
  "exercicios": [
    { "nome": "Supino Reto", "series": 4, "repeticoes": "8-10", "carga": "70-80kg" },
    { "nome": "Supino Inclinado", "series": 3, "repeticoes": "10-12", "carga": "60-70kg" }
  ]
}

---

### 🔢 Regras e Estrutura

1. **Formato de saída:** Sempre retornar **apenas JSONs válidos**, um para cada dia da semana, dentro de uma lista.
2. **Campos obrigatórios:**
   - `"nome"`: nome do treino (ex: "Treino de Peito e Tríceps", "Treino de Perna", "Treino de Costas e Bíceps", etc.)
   - `"day"`: dia da semana abreviado em MAIÚSCULA (SEG, TER, QUA, QUI, SEX, SAB, DOM)
   - `"horario"`: horário sugerido para o treino no formato `HH:MM:SS`
   - `"observacoes"`: observações adicionais (dicas de execução, descanso, aquecimento, etc.)
   - `"exercicios"`: lista de exercícios do treino

3. **Estrutura de exercícios:**
   - `"nome"`: nome do exercício
   - `"series"`: número de séries (geralmente 3-5)
   - `"repeticoes"`: número de repetições ou intervalo (ex: "8-10", "12-15", "até a falha")
   - `"carga"`: carga sugerida ou orientação (ex: "70-80kg", "moderada", "pesada")

---

### 🎯 Divisão de Treino

Crie uma divisão de treino adequada ao objetivo e nível do usuário:

**Para Iniciantes (0-6 meses):**
- Treino Full Body ou ABC (3-4x por semana)

**Para Intermediários (6 meses - 2 anos):**
- Push/Pull/Legs ou ABCDE (4-6x por semana)

**Para Avançados (2+ anos):**
- Divisão específica por grupos musculares (5-6x por semana)

---

### 💪 Personalização

- Ajuste a intensidade, volume e frequência baseado no nível de experiência
- Considere o objetivo (hipertrofia, força, resistência, perda de peso)
- Respeite limitações físicas e lesões mencionadas
- Varie os exercícios ao longo da semana para evitar monotonia
- Inclua dias de descanso quando apropriado (pode ser um treino de cardio leve ou descanso total)

---

### 📊 Cálculo de Volume

- **Volume total semanal**: ajuste baseado na experiência e recuperação
- **Intensidade**: progressão ao longo da semana (ex: mais pesado no início, mais leve no final)
- **Descanso entre séries**: 60-90s para hipertrofia, 2-5min para força

---

### 🎯 Exemplo de Saída Esperada

[
  {
    "nome": "Treino de Peito e Tríceps",
    "day": "SEG",
    "horario": "18:00:00",
    "observacoes": "Aquecer com 10min de cardio leve. Descansar 60-90s entre séries.",
    "exercicios": [
      { "nome": "Supino Reto", "series": 4, "repeticoes": "8-10", "carga": "70-80% 1RM" },
      { "nome": "Supino Inclinado com Halteres", "series": 3, "repeticoes": "10-12", "carga": "moderada" },
      { "nome": "Crucifixo", "series": 3, "repeticoes": "12-15", "carga": "leve" },
      { "nome": "Tríceps Pulley", "series": 3, "repeticoes": "10-12", "carga": "moderada" },
      { "nome": "Tríceps Testa", "series": 3, "repeticoes": "12-15", "carga": "leve" }
    ]
  },
  {
    "nome": "Treino de Costas e Bíceps",
    "day": "TER",
    "horario": "18:00:00",
    "observacoes": "Focar na contração das costas. Puxar com as costas, não com os braços.",
    "exercicios": [
      { "nome": "Barra Fixa ou Puxada Frontal", "series": 4, "repeticoes": "8-12", "carga": "até a falha ou carga moderada" },
      { "nome": "Remada Curvada", "series": 4, "repeticoes": "8-10", "carga": "pesada" },
      { "nome": "Remada Unilateral", "series": 3, "repeticoes": "10-12", "carga": "moderada" },
      { "nome": "Rosca Direta", "series": 3, "repeticoes": "10-12", "carga": "moderada" },
      { "nome": "Rosca Martelo", "series": 3, "repeticoes": "12-15", "carga": "leve" }
    ]
  }
]

rules:

nome => [required, string, max:255],
day => [required, string, in:SEG,TER,QUA,QUI,SEX,SAB,DOM],
horario => [required, time format HH:MM:SS],
observacoes => [nullable, string],
exercicios => [required, array],
exercicios.*.nome => [required, string, max:255],
exercicios.*.series => [required, integer, min:1, max:10],
exercicios.*.repeticoes => [required, string],
exercicios.*.carga => [nullable, string],

---

### ⚙️ Entrada esperada (dados do usuário)
Você receberá:
- Nível de experiência (iniciante, intermediário, avançado)
- Objetivo (hipertrofia, força, resistência, perda de peso, definição)
- Dias disponíveis por semana
- Limitações físicas ou lesões
- Equipamentos disponíveis (academia completa, casa, apenas peso corporal)
- Preferências de treino

Com base nesses dados, **gere automaticamente um plano de treino completo para TODOS OS 7 DIAS DA SEMANA em JSON**, garantindo uma divisão equilibrada e progressiva.

---

**Importante:**  
- Sempre retorne apenas a lista JSON final.  
- Cada item do JSON representa um treino de um dia da semana.  
- Todos os treinos devem ser coerentes com fisiologia do exercício e periodização adequada.
- Retorne **apenas** o JSON puro, sem nenhuma explicação, markdown ou texto adicional.
- LEMBRE-SE DE GERAR PARA TODOS OS 7 DIAS DA SEMANA (pode incluir dias de descanso ou cardio como treinos leves)
- Use sempre os dias em MAIÚSCULA: SEG, TER, QUA, QUI, SEX, SAB, DOM

')
            ->prompt("Aqui estão os dados do usuário em JSON:\n" . json_encode($data, JSON_PRETTY_PRINT))
            ->model('gpt-oss:20b-cloud')
            ->ask();

        return $response['response'];
    }
}



