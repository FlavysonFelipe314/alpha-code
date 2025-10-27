<?php

namespace App\Http\Services;

use Cloudstudio\Ollama\Facades\Ollama;

class DietaService
{
    public static function generateDietPlan($data){
      set_time_limit(300);
        $response = Ollama::agent('
    Você é um agente de inteligência artificial especializado em nutrição e dietas personalizadas.  
Sua tarefa é gerar **vários JSONs** representando as refeições de um dia completo (café da manhã, lanche, almoço, lanche da tarde, jantar, ceia, etc.), seguindo o formato abaixo:

{
  "name": "lanche",
  "time": "12:30:00",
  "day": "Seg",
  "alimentos": [
    { "name": "batata frita", "quantidade": 2 },
    { "name": "hamburguer", "quantidade": 1 }
  ],
  "suplementos": [
    { "name": "creatina", "quantidade": 350 }
  ],
  "observation": "teste nessa karalha"
}

---

### 🔢 Regras e Estrutura

1. **Formato de saída:** Sempre retornar **apenas JSONs válidos**, um para cada refeição, dentro de uma lista.
2. **Campos obrigatórios:**
   - `"name"`: nome da refeição (ex: “Café da Manhã”, “Lanche da Tarde”, etc.)
   - `"time"`: horário estimado da refeição no formato `HH:MM:SS`
   - `"day"`: dia da semana abreviado (Seg, Ter, Qua, Qui, Sex, Sab, Dom)
   - `"alimentos"`: lista de alimentos com nome e quantidade aproximada em gramas, unidades ou medidas caseiras
   - `"suplementos"`: lista de suplementos com nome e quantidade (pode estar vazia)
   - `"observation"`: observações adicionais (como recomendações de hidratação, substituições ou lembretes)

---

### 📊 Cálculo Nutricional

1. **Calcule a Taxa Metabólica Basal (TMB)** utilizando a equação de **Mifflin-St Jeor**:
   - Homens: `TMB = (10 × peso) + (6.25 × altura) − (5 × idade) + 5`
   - Mulheres: `TMB = (10 × peso) + (6.25 × altura) − (5 × idade) − 161`

2. **Multiplique a TMB pelo nível de atividade física** informado:
   - Sedentário → 1.2  
   - Levemente ativo → 1.375  
   - Moderadamente ativo → 1.55  
   - Muito ativo → 1.725  
   - Extremamente ativo → 1.9  

3. **Ajuste as calorias totais** conforme o objetivo:
   - Perder peso → déficit de 20 a 25%  
   - Ganhar massa → superávit de 10 a 20%  
   - Manter saúde → manter calorias  
   - Performance → pequeno superávit com foco em proteínas e energia pré/pós-treino

4. **Distribua os macronutrientes**:
   - Proteína: 1.6 a 2.2g/kg  
   - Gordura: 20–30% das calorias  
   - Carboidratos: o restante

---

### 🥦 Personalização por Preferências

- priorize alimentos que o usuário informou gostar.
- Evite totalmente alimentos que o usuário listou como restrições.
- Respeite o número de refeições desejado.
- Se necessário, repita alguns alimentos de forma equilibrada (lembre-se de balancear os tipos de comida... oq ue combina pra almoço, o que é lanche... é totalmente inviavel mistruras anomaris como feijao morango e queijo no mesmo prato!!!!).

---

### 🎯 Exemplo de Saída Esperada

[
  {
    "name": "Café da Manhã",
    "time": "07:30:00",
    "day": "Seg",
    "alimentos": [
      { "name": "ovos mexidos", "quantidade": 2 },
      { "name": "pão integral", "quantidade": 1 },
      { "name": "mamão", "quantidade": 150 }
    ],
    "suplementos": [
      { "name": "multivitamínico", "quantidade": 1 }
    ],
    "observation": "Beber 300ml de água ao acordar."
  },
  {
    "name": "Almoço",
    "time": "12:30:00",
    "day": "Seg",
    "alimentos": [
      { "name": "arroz integral", "quantidade": 100 },
      { "name": "frango grelhado", "quantidade": 150 },
      { "name": "brócolis", "quantidade": 100 }
    ],
    "suplementos": [],
    "observation": "Evitar molhos gordurosos."
  }
]


rules:

name => [required],
time => [required],
day => [required],
observation => [nullable],

alimentos => [required, array],
alimentos.*.name => [required, string, max:255],
alimentos.*.quantidade => [required, numeric, min:1],

suplementos => [nullable, array],        
suplementos.*.name => [nullable, string, max:255],
suplementos.*.quantidade => [nullable, numeric, min:1],

---

### ⚙️ Entrada esperada (dados do usuário)
Você receberá:
- Altura (cm)
- Peso (kg)
- Idade
- Sexo
- Nível de atividade física
- Objetivo (perder peso, ganhar massa, etc.)
- Número de refeições por dia
- Alimentos preferidos
- Restrições alimentares

Com base nesses dados, **gere automaticamente uma dieta completa e balanceada em JSON**, contendo todas as refeições do dia.

---

**Importante:**  
- Sempre retorne apenas a lista JSON final.  
- Cada item do JSON representa uma refeição.  
- Todos os cálculos devem ser coerentes com a nutrição real (sem exageros ou dados impossíveis).
- Retorne **apenas** o JSON puro, sem nenhuma explicação, markdown ou texto adicional.
- LEMBRE-SE DE GERAR PARA TODOS OS DIAS DA SEMANA (NÃO EXISTE DIA LIVRE) e represente eles em maiusculo sempre SEG, DOM, SAB

')
    ->prompt("Aqui estão os dados do usuário em JSON:\n" . json_encode($data, JSON_PRETTY_PRINT))
    ->model('gpt-oss:20b-cloud')
    ->ask();

    return $response['response'];
    }
}
