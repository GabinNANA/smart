<?php

namespace App\Http\Controllers;

use App\Models\montant;
use Illuminate\Http\Request;

class MontantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $montants = Montant::all();
        return response()->json($montants);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'idhabitation' => 'required',
            'etat' => 'required',
            'montant' => 'required',
        ]);
        $montant = montant::create($request->all());
        return response()->json(['message'=> 'montant crée', 
        'montant' => $montant]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\montant  $montant
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Montant::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\montant  $montant
     * @return \Illuminate\Http\Response
     */
    public function edit(montant $montant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\montant  $montant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $montant = Montant::findOrFail($id);
        $request->validate([
            'idhabitation'=> 'required',
            'etat'=> 'required',
            'montant'=> 'required',
        ]);
        $montant->idhabitation = $request->idhabitation;
        $montant->etat = $request->etat;
        $montant->montant = $request->montant;
        
        $montant->save();
        
        return response()->json([
            'message' => 'montant modifié!',
            'montant' => $montant
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\montant  $montant
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $montant= Montant::find($id);
        $montant->delete();
        return response()->json([
            'message' => 'montant supprimé'
        ]);
    }
}
