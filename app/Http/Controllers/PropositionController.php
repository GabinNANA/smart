<?php

namespace App\Http\Controllers;

use App\Models\Proposition;
use Illuminate\Http\Request;

class PropositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $propositions = Proposition::all();
        return response()->json($propositions);
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

    
    public function Getin($valeur)
    {
        $tablo = explode(',',$valeur);
        $propositions = Proposition::whereIn('id',$tablo)->get();
        return response()->json($propositions);
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
            'idcategorie' => '',
            'idquestion' => 'required',
            'choix' => 'required',
        ]);
        $proposition = Proposition::create($request->all());
        return response()->json(['message'=> 'proposition crée', 
        'proposition' => $proposition]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Proposition  $proposition
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Proposition::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proposition  $proposition
     * @return \Illuminate\Http\Response
     */
    public function edit(Proposition $proposition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Proposition  $proposition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $proposition = proposition::findOrFail($id);
        $request->validate([
            'idquestion'=> 'required',
            'idcategorie'=> '',
            'choix'=> 'required',
        ]);
        $proposition->idquestion = $request->idquestion;
        $proposition->idcategorie = $request->idcategorie;
        $proposition->choix = $request->choix;
        
        $proposition->save();
        
        return response()->json([
            'message' => 'proposition modifié!',
            'proposition' => $proposition
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Proposition  $proposition
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $proposition= Proposition::find($id);
        $proposition->delete();
        return response()->json([
            'message' => 'proposition supprimé'
        ]);
    }
}
