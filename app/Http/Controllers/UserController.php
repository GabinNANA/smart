<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Abonnement;
use App\Models\abonnement_user;
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
    public static function isabonner($id)
    {
        $user= abonnement_user::where('iduser',$id)->where('etat',0)->first();
        if($user){
            return 'true';
        }
        else{
            return 'false';
        }
        
    }

    public function recorver(Request $request){

        $utilisateur = User::where('email',$request->email)->first();
        if($utilisateur){

            $to = $request->email;
            $subject = "Paramètre de connexion";

            $message = "Email : ".$utilisateur->email."<br>Mot de passe : ".$utilisateur->password;

                
            $headers = 'From: Mybuildindtip <contact@mybuildingtip.com>'."\r\n";
                
            $headers .= 'Mime-Version: 1.0'."\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
            $headers .= "\r\n";
     
            // Sending email
            if(mail($to, $subject, $message, $headers)){
                return response()->json([
                'message' => 'Un email vous a été envoyé avec vos paramètres de connexion',
            ], 200);
            }
            else{
                return response()->json([
                'message' => 'Problème de connexion',
            ], 200);
            }
        }
        else{
            return response()->json([
                'message' =>'Cette adresse email n\'existe pas dans notre système',
            ], 422);
        }
    }

    public function contacter(Request $request){

        //$to = 'contact@mybuildingtip.com';
        $to = 'tchepda.flavie@yahoo.fr';
        $subject = "Formulaire contact";

        $message = $request->message."<br><br><b>Information complementaire</b><br><br> Téléphone : ".$request->tel."<br>Email : ".$request->email;

            
        $headers = 'From: '.$request->nom.' <'.$request->email.'>'."\r\n";
            
        $headers .= 'Mime-Version: 1.0'."\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
        $headers .= "\r\n";
 
        // Sending email
        if(mail($to, $subject, $message, $headers)){
            return response()->json([
            'message' => 'Enregistrement éffectué avec succès',
            ], 200);
        }
        else{
            return response()->json([
            'message' => 'Problème de connexion',
            ], 200);
        }
    }
}
