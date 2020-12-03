<?php

namespace App\Http\Controllers;

use App\Models\User_reponse;
use Illuminate\Http\Request;

class UserReponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_reponse = User_reponse::all();
        return response()->json($user_reponse);
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
            'idusers' => 'required',
            'idhabitation' => '',
            'etat' => '',
            'idquestion' => '',
            'idparent' => '',
            'reponse' => 'required'
        ]);
        $user_reponse = user_reponse::create($request->all());
        return response()->json(['message'=> 'Réponse utilisateur crée', 
        'user_reponse' => $user_reponse]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User_reponse  $user_reponse
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User_reponse::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User_reponse  $user_reponse
     * @return \Illuminate\Http\Response
     */
    public function edit(User_reponse $user_reponse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User_reponse  $user_reponse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user_reponse = user_reponse::findOrFail($id);
        $request->validate([
            'idusers' => 'required',
            'idhabitation' => '',
            'etat' => '',
            'idquestion' => '',
            'idparent' => '',
            'reponse' => 'required'
        ]);
        $user_reponse->idusers = $request->idusers;
        $user_reponse->idhabitation = $request->idhabitation;
        $user_reponse->etat = $request->etat;
        $user_reponse->idquestion = $request->idquestion;
        $user_reponse->idparent = $request->idparent;
        $user_reponse->reponse = $request->reponse;
        
        $user_reponse->save();
        
        return response()->json([
            'message' => 'user_reponse modifié!',
            'user_reponse' => $user_reponse
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User_reponse  $user_reponse
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user_reponse= User_reponse::find($id);
        $user_reponse->delete();
        return response()->json([
            'message' => 'user_reponse supprimé'
        ]);
    }
}
