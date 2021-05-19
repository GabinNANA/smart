<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\abonnement_user;
use JWTAuth;
use JWTFactory;
use Validator;
use DB;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = request(['email', 'password']);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $utilisateur = User::where('email',$request->email)->where('password',$request->password)->first();
        if($utilisateur){
            //echo "string";
            if ($request->abonnement != '') {
                $otherabonnement = abonnement_user::where('iduser',$utilisateur->id)->get();
                foreach ($otherabonnement as $key => $value) {
                    $value->etat = 1;
                    $value->save();
                }
                $actualite = abonnement_user::create([
                   'idabonnement' => $request->abonnement,
                    'iduser' => $utilisateur->id,
                    'montant' => '3000',
                    'datedeb' => date('Y-m-d'),
                    'datefin' => date('Y-m-d',strtotime('+1 year')),
                    'etat' => '0',
                ]);
            }
            $abonnement = DB::select('SELECT * FROM abonnement_users WHERE iduser='.$utilisateur->id.' AND etat = 0 ORDER BY id DESC LIMIT 1');
            $typeabonnement = DB::select('SELECT * FROM contenu_abonnement WHERE idabonnement IN (SELECT idabonnement FROM abonnement_users WHERE iduser='.$utilisateur->id.')');
            $formation = '';$securite = '';$environnement = '';
            if (count($typeabonnement) != 0) {
                foreach ($typeabonnement as $key => $value) {
                    if ($value->contenu == 'formation') {
                        $formation = 'oui';
                    }
                    if ($value->contenu == 'securite') {
                        $securite = 'oui';
                    }
                    if ($value->contenu == 'environnement') {
                        $environnement = 'oui';
                    }
                }
            }
            //var_dump($abonnement);
           return response()->json([
                'utilisateur' =>$utilisateur,
                'abonnement' =>$abonnement,
                'formation' =>$formation,
                'securite' =>$securite,
                'environnement' =>$environnement,
            ], 201);
        }
        else{
            return response()->json([
                'message' =>'Information erronée',
            ], 201);
        }
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        // $validator = Validator::make($request->all(), [
        //     'nom' => 'required|string|between:2,100',
        //     'email' => 'required|string|email|max:100|unique:users',
        //     'password' => 'required|string|confirmed|min:6',
        //     'telephone' => 'required|string|min:9',
        // ]);

        // if($validator->fails()){
        //     return response()->json($validator->errors()->toJson(), 400);
        // }
        $utilisateur = User::where('email',$request->email)->first();
        if ($utilisateur) {
            return response()->json([
                'message' =>'Cet email existe déjà',
            ], 500);
        }
        else{
            $user = User::create($request->all());

            if ($request->abonnement != '') {
                $otherabonnement = abonnement_user::where('iduser',$user->id)->get();
                foreach ($otherabonnement as $key => $value) {
                    $value->etat = 1;
                    $value->save();
                }
                $actualite = abonnement_user::create([
                   'idabonnement' => $request->abonnement,
                    'iduser' => $user->id,
                    'montant' => '3000',
                    'datedeb' => date('Y-m-d'),
                    'datefin' => date('Y-m-d',strtotime('+1 year')),
                    'etat' => '0',
                ]);
            }
            $abonnement = DB::select('SELECT * FROM abonnement_users WHERE iduser='.$user->id.' AND etat = 0 ORDER BY id DESC LIMIT 1');
            $typeabonnement = DB::select('SELECT * FROM contenu_abonnement WHERE idabonnement IN (SELECT idabonnement FROM abonnement_users WHERE iduser='.$user->id.')');
            $formation = '';$securite = '';$environnement = '';
            if (count($typeabonnement) != 0) {
                foreach ($typeabonnement as $key => $value) {
                    if ($value->contenu == 'formation') {
                        $formation = 'oui';
                    }
                    if ($value->contenu == 'securite') {
                        $securite = 'oui';
                    }
                    if ($value->contenu == 'environnement') {
                        $environnement = 'oui';
                    }
                }
            }
            //var_dump($abonnement);
           return response()->json([
                'utilisateur' =>$user,
                'abonnement' =>$abonnement,
                'formation' =>$formation,
                'securite' =>$securite,
                'environnement' =>$environnement,
            ], 201);
        }
        
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTFactory::getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

}
