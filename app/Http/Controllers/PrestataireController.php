<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestataire;
use DB;

class PrestataireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prestataires = Prestataire::all();
        return response()->json($prestataires);
    }
    
    public function search(Request $request)
    {
        $suite = '';
        if($request->type != ''){
            $suite .='  AND type ="'.$request->type.'"';
        }
        if($request->competence != ''){
            $suite .='  AND domaine LIKE "%'.$request->competence.'%"';
        }
        if($request->search != ''){
            $suite .='  AND (nom LIKE "%'.$request->search.'%" OR adresse LIKE "%'.$request->search.'%")';
        }
        $prestataires = DB::select("SELECT * FROM prestataires WHERE id IS NOT NULL ".$suite);
        return response()->json($prestataires);
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
        $prestataires = Prestataire::whereIn('id',$tablo)->get();
        return response()->json($prestataires);
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
        $prestataire = Prestataire::create($request->all());
        return response()->json(['message'=> 'prestataire crée', 
        'prestataire' => $prestataire]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\prestataire  $prestataire
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Prestataire::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\prestataire  $prestataire
     * @return \Illuminate\Http\Response
     */
    public function edit(prestataire $prestataire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\prestataire  $prestataire
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $prestataire = Prestataire::findOrFail($id);
        $request->validate([
            'idquestion'=> 'required',
            'idcategorie'=> '',
            'choix'=> 'required',
        ]);
        $prestataire->idquestion = $request->idquestion;
        $prestataire->idcategorie = $request->idcategorie;
        $prestataire->choix = $request->choix;
        
        $prestataire->save();
        
        return response()->json([
            'message' => 'prestataire modifié!',
            'prestataire' => $prestataire
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\prestataire  $prestataire
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $prestataire= Prestataire::find($id);
        $prestataire->delete();
        return response()->json([
            'message' => 'prestataire supprimé'
        ]);
    }
}
