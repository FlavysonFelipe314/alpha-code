<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Objetivo;
use App\Http\Requests\StoreObjetivoRequest;
use App\Http\Requests\UpdateObjetivoRequest;
use Illuminate\Http\Request;

class ObjetivoController extends Controller
{
    public function index(Request $request)
    {
        $topic = $request->query('topic');
        $query = Objetivo::where('user_id', auth()->id());
        if ($topic) $query->where('topic', $topic);
        return response()->json($query->orderBy('deadline')->get());
    }

    public function store(StoreObjetivoRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $item = Objetivo::create($data);
        return response()->json($item, 201);
    }

    public function show(Objetivo $objetivo)
    {
        if ($objetivo->user_id !== auth()->id()) {
            abort(403);
        }
        return response()->json($objetivo);
    }

    public function update(UpdateObjetivoRequest $request, Objetivo $objetivo)
    {
        if ($objetivo->user_id !== auth()->id()) {
            abort(403);
        }
        $objetivo->update($request->validated());
        return response()->json($objetivo);
    }

    public function destroy(Objetivo $objetivo)
    {
        if ($objetivo->user_id !== auth()->id()) {
            abort(403);
        }
        $objetivo->delete();
        return response()->json(null, 204);
    }
}
