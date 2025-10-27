<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDietaAlimentosRequest;
use App\Http\Requests\StoreDietaRequest;
use App\Http\Requests\UpdateDietaRequest;
use App\Http\Services\DietaService;
use App\Models\Dieta;
use App\Models\DietaAlimentos;
use App\Models\DietaSuplementos;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DietaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try{
            $dietas = Dieta::with(['user','suplementos', 'alimentos'])->orderBy('created_at', 'DESC')->get();

            $message = ($dietas->count() > 0) ? "Lista com todas as dietas do usuário" : 'Nenhuma dieta esta cadastrada'; 

            return response()->json([
                'status' => true,
                'data' => $dietas,
                'message' => $message
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar as dietas',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDietaRequest $dietaRequest)
    {

        $validatedDietaRequest = $dietaRequest->validated();
        $validatedDietaRequest['user_id'] = 1;

        $alimentos = $validatedDietaRequest['alimentos'];
        $suplementos = $validatedDietaRequest['suplementos'];

        DB::beginTransaction();

        try{
            $dieta = Dieta::create($validatedDietaRequest);

            $newDietaId = $dieta->id;
            
            foreach($alimentos as $alimento){
                $alimentosToInsert = [
                    'name' => $alimento['name'],
                    'quantidade' => $alimento['quantidade'],
                    'dieta_id' => $newDietaId,
                ];
                
                DietaAlimentos::create($alimentosToInsert);
            }

            foreach($suplementos as $suplemento){
                $suplementosToInsert = [
                    'name' => $suplemento['name'],
                    'quantidade' => $suplemento['quantidade'],
                    'dieta_id' => $newDietaId,
                ];
                
                DietaSuplementos::create($suplementosToInsert);
            }

            DB::commit();

            $dieta->load(['alimentos']);

            return response()->json([
                'status' => true,
                'data' => $dieta,
                'message' => 'Refeição Cadastrada com Sucesso'
            ], 200);

        } catch (Exception $err){

            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => 'Não foi possivel cadastrar a refeição',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $dieta = Dieta::with(['user','suplementos', 'alimentos'])->findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => $dieta,
                'message' => 'Dieta encontrada com sucesso'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar as dietas',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDietaRequest $dietaRequest, string $id)
    {
        try{
            $validatedDietaRequest = $dietaRequest->validated();
            $validatedDietaRequest['user_id'] = 1;
            $alimentosNew = $validatedDietaRequest['alimentos'];
            $suplementosNew = $validatedDietaRequest['suplementos'] ?? [];

            DB::beginTransaction();

            $dieta = Dieta::findOrFail($id);
            $dieta->update($validatedDietaRequest);

            foreach($alimentosNew as $alimento){
                $alimentosToUpdate = [
                    'name' => $alimento['name'],
                    'quantidade' => $alimento['quantidade'],
                    'dieta_id' => $id,
                ];
                
                DietaAlimentos::where('id', $alimento["id"])->update($alimentosToUpdate);
            }

            if(!empty($suplementosNew)){
                foreach($suplementosNew as $suplemento){
                    $suplementosToUpdate = [
                        'name' => $suplemento['name'],
                        'quantidade' => $suplemento['quantidade'],
                        'dieta_id' => $id,
                    ];
                    
                    DietaSuplementos::where('id', $suplemento["id"])->update($suplementosToUpdate);
                }
            }

            DB::commit();

            $dieta->load(['alimentos', 'suplementos']);

            return response()->json([
                'status' => true,
                'data' => $dieta,
                'message' => 'Refeição Cadastrada com Sucesso'
            ], 200);

        } catch (Exception $err){

            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => 'Não foi possivel atualizar a refeição',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $dieta = Dieta::findOrFail($id);
            $dieta->delete();

            return response()->json([
                'status' => true,
                'message' => 'Refeição deletada com sucesso'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar a refeição',
                'error' => $err
            ], 400);
        }
    }

    public function processByAi(Request $request){
        $validatedData = $request->input();

        return DietaService::generateDietPlan($validatedData);
    }
}
