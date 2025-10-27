<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Http\Requests\StoreContaRequest;
use App\Http\Requests\UpdateContaRequest;

class ContaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContaRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Conta $conta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContaRequest $request, Conta $conta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conta $conta)
    {
        //
    }
}
