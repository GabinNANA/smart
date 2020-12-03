<?php

namespace App\Http\Controllers;

use App\Models\abonnement;
use Illuminate\Http\Request;

class AbonnementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $abonnements = Abonnement::all();
        return response()->json($abonnements);
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
            'intitule'=> 'required',
            'periode'=> 'required',
            'frequence'=> 'required',
            'montant'=> 'required',
        ]);
        $abonnement = Abonnement::create($request->all());
        return response()->json(['message'=> 'abonnement crée', 
        'abonnement' => $abonnement]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\abonnement  $abonnement
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Abonnement::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\abonnement  $abonnement
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
     * @param  \App\Models\abonnement  $abonnement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $abonnement = abonnement::findOrFail($id);
        $request->validate([
            'intitule'=> 'required',
            'periode'=> 'required',
            'frequence'=> 'required',
            'montant'=> 'required',
        ]);
        $abonnement->intitule = $request->intitule;
        $abonnement->periode = $request->periode;
        $abonnement->frequence = $request->frequence;
        $abonnement->montant = $request->montant;
        
        $abonnement->save();
        
        return response()->json([
            'message' => 'abonnement modifié!',
            'abonnement' => $abonnement
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\abonnement  $abonnement
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $abonnement= Abonnement::find($id);
        $abonnement->delete();
        return response()->json([
            'message' => 'abonnement supprimé'
        ]);
    }
}
