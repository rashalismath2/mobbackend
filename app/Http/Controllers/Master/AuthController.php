<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Events\EmailActivationRequest;

use Illuminate\Support\Facades\Auth;
use App\Models\Master;
use Validator;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:masterapi', ['except' => [
                                            'login', 
                                            'register',
                                            "generateactivationcode"
                                            ]]);
    }

    public function generateactivationcode(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $code=rand(100000,999999);

        EmailActivationRequest::dispatch($request->email,$code);

        return response()->json(["activation-code"=>$code],200);
    }

    public function login(Request $request){

    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        if (! $token = auth("masterapi")->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|between:2,100',
            'lastName' => 'required|string|between:2,100',
            'education' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users|unique:masters',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = Master::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }


    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }


    public function userProfile() {

        return response()->json(auth()->user());
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
    
}
