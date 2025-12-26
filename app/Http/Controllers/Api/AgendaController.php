<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Http\Requests\StoreAgendaRequest;
use App\Http\Requests\UpdateAgendaRequest;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date');
        $query = Agenda::where('user_id', auth()->id());
        if ($date) {
            $query->whereDate('date', $date);
        }
        $items = $query->orderBy('time')->get();
        return response()->json(['status' => true, 'data' => $items]);
    }

    public function store(StoreAgendaRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $agenda = Agenda::create($data);
        return response()->json(['status' => true, 'data' => $agenda], 201);
    }

    public function show(Agenda $agenda)
    {
        if ($agenda->user_id !== auth()->id()) {
            abort(403);
        }
        return response()->json(['status' => true, 'data' => $agenda]);
    }

    public function update(UpdateAgendaRequest $request, Agenda $agenda)
    {
        if ($agenda->user_id !== auth()->id()) {
            abort(403);
        }
        $agenda->update($request->validated());
        return response()->json(['status' => true, 'data' => $agenda]);
    }

    public function destroy(Agenda $agenda)
    {
        if ($agenda->user_id !== auth()->id()) {
            abort(403);
        }
        $agenda->delete();
        return response()->json(['status' => true], 204);
    }
}
