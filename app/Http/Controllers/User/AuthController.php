<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Events\EmailActivationRequest;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register',"generateactivationcode"]]);
    }

    // TODO- email verification | before send activation check if a user exist in user table
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
        
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|between:2,100',
            'lastName' => 'required|string|between:2,100',
            'grade' => 'required|string|between:1,100',
            'school' => 'required|string|between:1,100',
            'email' => 'required|string|email|max:100|unique:users|unique:masters',
            'password' => 'required|string|min:6',
            'profile_picture' => 'mimes:jpeg,jpg,png',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $profile_img=$this->uploadPicture($request);

        $user=new User();
        $user->firstName=$request->firstName;
        $user->lastName=$request->lastName;
        $user->email=$request->email;
        $user->profile_img=$profile_img;
        $user->grade=$request->grade;
        $user->school=$request->school;
        $user->password= bcrypt($request->password);

        $user->save();


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

    private function uploadPicture($request){
        if($request->hasFile("profile_picture")){
            $fileNameWithExtenstion=$request->file("profile_picture")->getClientOriginalName();
            $fileName=pathinfo($fileNameWithExtenstion,PATHINFO_FILENAME);
            $extension=$request->file("profile_picture")->getClientOriginalExtension();
            $fileNameToStore=$fileName."_".time().".".$extension;
            $path=$request->file("profile_picture")->storeAs("public/users/profilpics",$fileNameToStore);
    
            return $fileNameToStore;
        }
        else{
            return null;
        }
    }
    
}
