<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $categories = Categorie::all();
        // return response()->json($categories);

        $categories = DB::table('categories')
            ->get();
        $results = array();
            
        foreach ($categories as $categorie) {
            $result = array();

            $result['id']=$categorie->id;
            $result['idparent']=$categorie->idparent;
            $result['intitule']=$categorie->intitule;

            if($categorie->idparent!=NULL){

                $parent = DB::table('categories')->find($categorie->idparent);
                $result['parent']=$parent->intitule;

            }else{
                $result['parent']='';          
                
            }
            array_push($results,$result);
        }
        return  response()->json($results);
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
            'idparent' => '',
            'intitule' => 'required',
        ]);
        $categorie = Categorie::create($request->all());
        return response()->json(['message'=> 'catégorie crée', 
        'categorie' => $categorie]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Categorie::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function edit(Categorie $categorie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $categorie = Categorie::findOrFail($id);
        $request->validate([
            'idparent'=> 'required',
            'intitule'=> 'required',
        ]);
        $categorie->intitule = $request->intitule;
        
        $categorie->save();
        
        return response()->json([
            'message' => 'catégorie modifié!',
            'categorie' => $categorie
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $categorie= Categorie::find($id);
        $categorie->delete();
        return response()->json([
            'message' => 'catégorie supprimé'
        ]);
    }
}
