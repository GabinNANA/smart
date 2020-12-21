<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\abonnement_user;
use JWTAuth;
use JWTFactory;
use Validator;


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
            $abonnement = User::isabonner($utilisateur->id);
           return response()->json([
                'utilisateur' =>$utilisateur,
                'abonnement' =>$abonnement,
            ], 201);
        }
        else{
            return response()->json([
                'message' =>'Information erroné',
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

        $user = User::create($request->all());
        $abonnement = UserController::isabonner($user->id);
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'abonnement' =>$abonnement,
        ], 201);
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
