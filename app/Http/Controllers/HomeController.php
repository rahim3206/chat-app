<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::where('id','!=',auth()->user()->id)->get();
        return view('home',compact('users'));
    }
    public function send_message(Request $request)
    {
        try {
            $chat = new Chat();
            $chat->sender_id = $request->sender_id;
            $chat->receiver_id = $request->receiver_id;
            $chat->message = $request->message;
            $chat->save();
            
            event(new Message($chat));
            return response()->json(['status'=>'success','data'=>$chat]);
        } catch (\Throwable $th) {
            return response()->json(['status'=>'error','data'=>$th]);
        }
    }
    public function load_chats(Request $request)
    {
        try {
            $chats = Chat::where(function($query) use($request){
                $query->where('sender_id',$request->sender_id)
                ->orwhere('sender_id',$request->receiver_id);
            })->where(function($query) use($request){
                $query->where('receiver_id',$request->sender_id)
                ->orwhere('receiver_id',$request->receiver_id);
            })->get();
            return response()->json(['status'=>'success','data'=>$chats]);

        } catch (\Throwable $th) {
            return response()->json(['status'=>'error','data'=>$th]);
        }
    }
}
