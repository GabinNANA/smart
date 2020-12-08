<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Abonnement;
use App\Models\AbonnementUser;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
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
            'nom' => 'required',
            'email' => 'required',
            'password' => 'required',
            'telephone' => 'required',
        ]);
        $categorie = Categorie::create($request->all());
        return response()->json(['message'=> 'catégorie crée', 
        'categorie' => $categorie]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'nom' => 'required',
            'telephone' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);
        $user->nom = $request->nom;
        $user->telephone = $request->telephone;
        $user->email = $request->email;
        $user->password = $request->password;
        
        $user->save();

        return response()->json([
            'message' => 'Utilisateur modifié!',
            'user' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user= User::find($id);
        $user->delete();
        return response()->json([
            'message' => 'Utilisateur supprimé'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function isabonner($id)
    {
        $user= AbonnementUser::where('iduser',$id)->where('etat',0)->first();
        if($user){
            return response()->json([
                'error' => 'false'
            ]);
        }
        else{
            return response()->json([
                'error' => 'true'
            ]);
        }
        
    }
}
