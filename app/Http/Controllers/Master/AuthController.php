<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Events\EmailActivationRequest;

use Illuminate\Support\Facades\Auth;
use App\Models\Master;
use App\Models\VerificationCode;
use Validator;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:masterapi', ['except' => [
                                            'login', 
                                            'register',
                                            "generateactivationcode",
                                            "verifyActivation"
                                            ]]);
    }

    public function verifyActivation(Request $request){
 
        $validator = Validator::make($request->all(), [
            'activationCode' => 'required|numeric',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //TODO- schedule jobs to delete sent codes after a time
        $codeEntry=VerificationCode::where("email",$request["email"])->latest()->first();
        if($codeEntry->code==(int)$request["activationCode"]){
            return response()->json(["message"=>"email verified"], 200);
        }
        else{
            return response()->json(["message"=>"code does not match"], 401);
        }

    }

    public function generateactivationcode(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = Master::where('email', '=', $request->email)->first();
        if ($user != null) {
            return response()->json(["message"=>"email already exists"], 409);
        }

        $code=rand(100000,999999);

        EmailActivationRequest::dispatch($request->email,$code);

        $codeEntry=new VerificationCode();
        $codeEntry->code=$code;
        $codeEntry->email=$request->email;
        $codeEntry->save();

        return response()->json(["message"=>"Activation code has been sent"],200);
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
        error_log($request->hasFile("profile_picture"));
        error_log($request["profile_picture"]);
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|between:2,100',
            'lastName' => 'required|string|between:2,100',
            'education' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users|unique:masters',
            'password' => 'required|string|min:6',
            'profile_picture' => 'mimes:jpeg,jpg,png',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $profile_img=$this->uploadPicture($request);

        $master=new Master();
        $master->firstName=$request->firstName;
        $master->lastName=$request->lastName;
        $master->email=$request->email;
        $master->profile_img=$profile_img;
        $master->education=$request->education;
        $master->password= bcrypt($request->password);

        $master->save();

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $master
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
            $path=$request->file("profile_picture")->storeAs("public/masters/profilpics",$fileNameToStore);
    
            return $fileNameToStore;
        }
        else{
            return null;
        }
    }
}
