<?php

namespace App\Http\Controllers;

use App\Models\abonnement_ponctuel;
use Illuminate\Http\Request;

class AbonnementPonctuelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $abonnement_ponctuels = Abonnement_ponctuel::all();
        return response()->json($abonnement_ponctuels);
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
            'iduser_reponse'=> 'required',
            'montant'=> 'required',
        ]);
        $abonnement_ponctuel = abonnement_ponctuel::create($request->all());
        return response()->json(['message'=> 'abonnement_ponctuel crée', 
        'abonnement_ponctuel' => $abonnement_ponctuel]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\abonnement_ponctuel  $abonnement_ponctuel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Abonnement_ponctuel::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\abonnement_ponctuel  $abonnement_ponctuel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\abonnement_ponctuel  $abonnement_ponctuel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $Abonnement_ponctuel = Abonnement_ponctuel::findOrFail($id);
        $request->validate([
            'iduser_reponse'=> 'required',
            'montant'=> 'required',
        ]);
        $Abonnement_ponctuel->iduser_reponse = $request->iduser_reponse;
        $Abonnement_ponctuel->montant = $request->montant;
        
        $Abonnement_ponctuel->save();
        
        return response()->json([
            'message' => 'Abonnement_ponctuel modifié!',
            'Abonnement_ponctuel' => $Abonnement_ponctuel
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\abonnement_ponctuel  $abonnement_ponctuel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $abonnement_ponctuel= abonnement_ponctuel::find($id);
        $abonnement_ponctuel->delete();
        return response()->json([
            'message' => 'abonnement_ponctuel supprimé'
        ]);
    }
}
