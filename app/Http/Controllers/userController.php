<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
class userController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['']]);
    }

    // ------------------get allusers chatlist-------------------------------------------------------
    public function allusers(Request $request){
        session(['key'=> $request->keywords]);
        
        $users=User::where('id','!=',Auth::user()->id)->where(function($q){
            $value=session(('key'));
            $q->where('id','LIKE','%'.$value.'%')
            ->orwhere('name','LIKE','%'.$value.'%')
            ->orwhere('mobile','LIKE','%'.$value.'%')
            ->orwhere('email','LIKE','%'.$value.'%');

        }) ->get();

        return response()->json(['users'=>$users]);
    }

    //--------------------get userinfo---------------------------------------------------------
    public function userInfo(Request $request){
        $validator=Validator::make($request->all(),[
            'id'=>'required',
        ]);
        if($validator->fails()){
            return Response::json(['error'=>$validator->errors()->all()],409);
        }
        $userinfo=User::find($request->id);
        return response()->json(['userinfo'=>$userinfo]);
    }
}
