<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{

    public function users(){
        $users = User::latest('id')->paginate(10);
        if(count($users) <= 0){
            return response()->json(['message'=>'users not have, Please first register']);
        }
        return response()->json($users);
    }


    public function register(Request $request){
        $request->validate([
            'name'=>"required|min:2",
            'email'=>"required|email|unique:users",
            'password'=>'required|min:8|confirmed',
//            'password_confirmation'=>'same:password',
        ]);

        User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);

        if(Auth::attempt($request->only(['email','password']))){
            $deviceName = $request->header('User-Agent');

            $token  = Auth::user()->createToken($deviceName)->plainTextToken;
            return response()->json($token);
        }

        return response()->json(['message'=>"User Not Found"],403);

    }

    public function logout(){
//        $deviceName = $request->header('User-Agent');
         Auth::user()->currentAccessToken()->delete();
         return response()->json(['message'=>'logout Successful'],200);
    }

    public function logoutAll(){

        //ခုဝင်နေတဲ့ tokenကလွဲလို့ကျန်တာတွေအကုန် ဘယ်လိုဖြတ်မလဲစဉ်းစား
        Auth::user()->tokens()->delete();
        //status code 204 က message ထဲက စာ replyမပြန်နိုင်ပါဘူး/message replyပြန်ချင်ရင် 200သုံး
        return response()->json(['message'=>'logOut All successful'],204);
    }

    public function logIn(Request $request){
        $request->validate([
           'email'=>'required|email',
            'password'=>'required|min:8',
        ]);

        if(Auth::attempt($request->only(['email','password']))){

            $deviceName = $request->header('User-Agent');
            $token = Auth::user()->createToken($deviceName)->plainTextToken;
            return $token;
        }

        return response()->json(["message"=>"User Not Found"],401);
    }

    public function tokens(){
        return Auth::user()->tokens;
        return response()->json(['message'=>'tokens']);
    }

}
