<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\chat;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['','']]);
    }

    //--------------------Chat users get ---------------------------------------------------------------
    public function chats(Request $request){
        $validator=Validator::make($request->all(),[
            'id'=>'required',
        ]);
        if($validator->fails()){
            return Response::json(['error'=>$validator->errors()->all()],409);
        }

        session(['key'=>$request->id]);
        $chats=chat::where(function($q){
            $id=session('key');
            $q->where('sender', '=', Auth::user()->id)
            ->where('reciever', '=', $id);
        })
        ->orwhere(function($q){
            $id=session('key');
            $q->where('reciever', '=', Auth::user()->id)
            ->where('sender', '=', $id);
        })
        ->leftJoin('users as s', 's.id','=','chats.sender')
        ->leftJoin('users as r', 'r.id','=','chats.reciever')
        ->select('chats.*','s.name as sender_name', 'r.name as reciever_name ')
        ->get();

        return response()->json(['chats'=>$chats]);
    }




//--------------------Send Message------------------------------------------------------------------------ 
    public function send(Request $request){
        $validator=Validator::make($request->all(),[
            'id'=>'required',
            'text'=>'required',
        ]);
        if($validator->fails()){
            return Response::json(['error'=>$validator->errors()->all()],409);
        }

        $chat=new chat();
        $chat->sender=Auth::user()->id;
        $chat->reciever=$request->id;
        $chat->text=$request->text;
        $chat->status="Recieved";
        $chat->save();

        return Response::json(['message'=>"Message Successfully sent"]);

    }
}
