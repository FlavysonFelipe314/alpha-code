<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plano;
use Illuminate\Http\Request;

class PlanoController extends Controller
{
    public function index()
    {
        return view('admin.planos');
    }

    public function getPlanos(Request $request, $id = null)
    {
        if ($id) {
            $plano = Plano::findOrFail($id);
            return response()->json(['status' => true, 'data' => $plano]);
        }
        
        $planos = Plano::orderBy('ordem')->orderBy('preco')->get();
        return response()->json(['status' => true, 'data' => $planos]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'moeda' => 'required|string|size:3',
            'periodicidade' => 'required|in:monthly,yearly',
            'dias_trial' => 'nullable|integer|min:0',
            'asaas_product_id' => 'nullable|string',
            'ativo' => 'boolean',
            'ordem' => 'nullable|integer',
            'features' => 'nullable|array',
        ]);

        $plano = Plano::create($validated);
        return response()->json(['status' => true, 'data' => $plano], 201);
    }

    public function update(Request $request, $id)
    {
        $plano = Plano::findOrFail($id);
        
        $validated = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'sometimes|required|numeric|min:0',
            'moeda' => 'sometimes|required|string|size:3',
            'periodicidade' => 'sometimes|required|in:monthly,yearly',
            'dias_trial' => 'nullable|integer|min:0',
            'asaas_product_id' => 'nullable|string',
            'ativo' => 'boolean',
            'ordem' => 'nullable|integer',
            'features' => 'nullable|array',
        ]);

        $plano->update($validated);
        return response()->json(['status' => true, 'data' => $plano]);
    }

    public function destroy($id)
    {
        $plano = Plano::findOrFail($id);
        
        // Verificar se há assinaturas ativas
        if ($plano->assinaturas()->where('status', 'active')->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Não é possível deletar um plano com assinaturas ativas'
            ], 400);
        }
        
        $plano->delete();
        return response()->json(['status' => true, 'message' => 'Plano deletado com sucesso']);
    }
}