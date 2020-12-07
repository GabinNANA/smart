<?php

namespace App\Http\Controllers;

use App\Models\Output;
use Illuminate\Http\Request;

class OutputController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $outputs = Output::all();
        return response()->json($outputs);
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
            'titre' => 'required',
            'classement' => 'required',
            'standard' => 'required',
            'livrable' => '',
            'validite' => '',
            'delai' => '',
            'cout_etude' => '',
            'frais_admin' => '',
            'penalite' => '',
            'ispayer' => '',
        ]);
        $output = Output::create($request->all());
        return response()->json(['message'=> 'output crée', 
        'output' => $output]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Output  $output
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Output::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Output  $output
     * @return \Illuminate\Http\Response
     */
    public function edit(Output $output)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Output  $output
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $output = Output::findOrFail($id);
        $request->validate([
            'titre' => 'required',
            'classement' => 'required',
            'standard' => 'required',
            'livrable' => '',
            'validite' => '',
            'delai' => '',
            'cout_etude' => '',
            'frais_admin' => '',
            'penalite' => '',
            'ispayer' => '',
        ]);
        $output->titre = $request->titre;
        $output->classement = $request->classement;
        $output->standard = $request->standard;
        $output->livrable = $request->livrable;
        $output->validite = $request->validite;
        $output->delai = $request->delai;
        $output->cout_etude = $request->cout_etude;
        $output->frais_admin = $request->frais_admin;
        $output->penalite = $request->penalite;
        $output->ispayer = $request->ispayer;
        
        $output->save();
        
        return response()->json([
            'message' => 'output modifié!',
            'output' => $output
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Output  $output
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $output= Output::find($id);
        $output->delete();
        return response()->json([
            'message' => 'output supprimé'
        ]);
    }
}
