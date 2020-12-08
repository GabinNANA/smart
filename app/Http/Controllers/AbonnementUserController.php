<?php

namespace App\Http\Controllers;

use App\Models\abonnement_user;
use Illuminate\Http\Request;

class AbonnementUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $abonnement_users = abonnement_user::all();
        return response()->json($abonnement_users);
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
        // $request->validate([
        //     'idabonnement' => 'required',
        //     'iduser' => 'required',
        //     'datedeb' => 'required',
        //     'datefin' => 'required',
        //     'montant' => 'required',
        //     'etat' => 'required',
        // ]);
        $user = AbonnementUser::where('iduser', $request->iduser)->where('etat', 0)->first();
        if ($user) {
            return response()->json([
                'error' => 'false',
                'motif' => 'Vous avez déjà un abonnement en cours',
            ]);
        } else {
            $abonnement_user = abonnement_user::create($request->all());
            return response()->json(['message'=> 'abonnement_user crée', 
            'abonnement_user' => $abonnement_user]);
        }        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\abonnement_user  $abonnement_user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Abonnement_user::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\abonnement_user  $abonnement_user
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
     * @param  \App\Models\abonnement_user  $abonnement_user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $abonnement_user = abonnement_user::findOrFail($id);
        $request->validate([
            'idabonnement'=> 'required',
            'iduser'=> 'required',
            'datedeb'=> 'required',
            'datefin'=> 'required',
            'montant'=> 'required',
            'etat'=> 'required',
        ]);
        $abonnement_user->idabonnement = $request->idabonnement;
        $abonnement_user->iduser = $request->iduser;
        $abonnement_user->datedeb = $request->datedeb;
        $abonnement_user->datefin = $request->datefin;
        $abonnement_user->montant = $request->montant;
        $abonnement_user->etat = $request->etat;
        
        $abonnement_user->save();
        
        return response()->json([
            'message' => 'abonnement_user modifié!',
            'abonnement_user' => $abonnement_user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\abonnement_user  $abonnement_user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $abonnement_user= abonnement_user::find($id);
        $abonnement_user->delete();
        return response()->json([
            'message' => 'abonnement_user supprimé'
        ]);
    }
}
