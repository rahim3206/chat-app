<?php

namespace App\Http\Controllers;

use App\Events\Delete;
use App\Events\FriendRequestSent;
use App\Events\GroupMessage;
use App\Events\Message;
use App\Events\Notifications;
use App\Events\Unread;
use App\Events\Update;
use App\Models\Chat;
use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\Group;
use App\Models\GroupChat;
use App\Models\GroupMember;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $userId = auth()->user()->id;

        $join_groups_ids = GroupMember::where('user_id',$userId)->pluck('group_id');

        $notifications = Notification::with('user')->where('receiver_id',$userId)->orwhereIn('group_id',$join_groups_ids)->where('sender_id','!=',$userId)->latest()->take(20)->get();
        $unread_notifications = Notification::with('user')->where('receiver_id',$userId)->where('status',0)->get();

        // Get friend relationships
        $friendRelationships = Friend::where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhere('friend_id', $userId);
        })->get();

        // Extract friend IDs
        $friendIds = $friendRelationships->map(function($friend) use ($userId) {
            return $friend->user_id == $userId ? $friend->friend_id : $friend->user_id;
        });

        // Get friends
        $friends = User::whereIn('id', $friendIds)->get();

        // Get friend requests
        $friend_requests = FriendRequest::with('sender', 'receiver')
                                        ->where('receiver_id', $userId)
                                        ->where('status', 'pending')
                                        ->get();

        // Get last messages and order friends by the latest message timestamp
        $friends = User::whereIn('users.id', $friendIds)
                        ->leftJoin('chats', function($join) use ($userId) {
                            $join->on('users.id', '=', 'chats.sender_id')
                                ->orOn('users.id', '=', 'chats.receiver_id');
                        })
                        ->where(function($query) use ($userId) {
                            $query->where('chats.sender_id', $userId)
                                ->orWhere('chats.receiver_id', $userId);
                        })
                        ->select('users.*', DB::raw('MAX(chats.created_at) as last_message_time'))
                        ->groupBy('users.id')
                        ->orderBy('last_message_time', 'desc')
                        ->get();

        return view('home', compact('friends', 'friend_requests','notifications','unread_notifications'));
    }

    public function send_message(Request $request)
    {
        try {
            if($request->receiver_id != null){
                $chat = new Chat();
                $chat->sender_id = $request->sender_id;
                $chat->receiver_id = $request->receiver_id;
            }else if($request->group_id != null){
                $chat = new GroupChat();
                $chat->user_id = $request->sender_id;
                $chat->group_id = $request->group_id;
            }else{
                return response()->json(['status'=>'error','msg'=>'Something went wrong.']);
            }
            $chat->read_ = false;
            if($request->file != null){

                $file_data = $request->file;
                list($type, $data) = explode(';', $file_data);
                list(, $data)      = explode(',', $data);
                $file_data = base64_decode($data);
                $temp_image_name = md5(uniqid()) . '.png';
                $image_path = public_path().'/'.'images/' . $temp_image_name;

                file_put_contents($image_path, $file_data);

                $chat->message = $temp_image_name;
                $chat->type = 'file';
            }else{
                $chat->message = $request->message;
                $chat->type = 'text';
            }

            $chat->save();
            if($request->receiver_id != null){
                event(new Message($chat));

                $unread_chats = Chat::where(function($query) use($request){
                    $query->where('sender_id',$request->sender_id)
                    ->orwhere('sender_id',$request->receiver_id);
                })->where(function($query) use($request){
                    $query->where('receiver_id',$request->sender_id)
                    ->orwhere('receiver_id',$request->receiver_id);
                })->where('read_','0')->get();
                event(new Unread($unread_chats));
                $message = 'Sent you a message.';

            }else if($request->group_id != null){
                $find_group = Group::find($request->group_id);
                $sender = User::find($request->sender_id);
                event(new GroupMessage($chat,$sender));
                $message = "Sent message in ".$find_group->name.'.';
            }
            $notification = new Notification();
            $notification->sender_id = $request->sender_id;
            $notification->receiver_id = $request->receiver_id ?? null;
            $notification->group_id = $request->group_id ?? null;
            $notification->message = $message;
            $notification->url = null;
            $notification->status = 0;
            $notification->save();

            event(new Notifications($notification,auth()->user()));

            return response()->json(['status'=>'success','data'=>$chat]);

        } catch (\Exception $th) {
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

            Chat::where('sender_id', $request->receiver_id)
            ->where('receiver_id', $request->sender_id)
            ->where('read_', false)
            ->update(['read_' => true]);

            return response()->json(['status'=>'success','data'=>$chats]);

        } catch (\Throwable $th) {
            return response()->json(['status'=>'error','data'=>$th]);
        }
    }
    public function delete_chat(Request $request){
        try {
            if($request->group_id != null)
            {
                $chat = GroupChat::find($request->id);
            }else{
                $chat = Chat::find($request->id);
            }
            if($chat->type == 'file'){
                $image_path = public_path().'/'.'images/' . $chat->message;
                if(file_exists($image_path)){
                    unlink($image_path);
                }
            }
            $chat->delete();
            event(new Delete($request->id));
            return response()->json(['status'=>'success','data'=>$chat]);
        } catch (\Throwable $th) {
            return response()->json(['status'=>'error','data'=>$th]);
        }
    }

    public function update_chat(Request $request)
    {
        $userId = auth()->user()->id;
        if($request->group_id != null)
        {
            $chat = GroupChat::find($request->id);
        }else{
            $chat = Chat::find($request->id);
        }
        $chat->message = $request->message;
        $chat->update();
        event(new Update($chat));
        return response()->json(['status'=>'success','data'=>$chat]);
    }
    public function search_user(Request $request){
        $user_id = auth()->user()->id;
        $users = User::where('name','LIKE','%'.$request->value.'%')->where('id','!=',auth()->user()->id)->get();
         
        $html = '';
        foreach ($users as $key => $user) {
            $btnTxt = 'Send Request';
            $btnstatus = '';
            $cancelBtn = '';
            $data_id = $user->id;
            $data_sender_id = '';
            $check_request = FriendRequest::where('sender_id',auth()->user()->id)->where('receiver_id',$user->id)->first();
            if($check_request){
                if($check_request->status == 'pending'){
                    $btnTxt = 'Send';
                    $btnstatus = 'disabled';
                }else if($check_request->status == 'accepted'){
                    $btnTxt = 'Unfriend';
                    $btnstatus = '';
                }
            }
            $check_request_receive = FriendRequest::where('sender_id',$user->id)->where('receiver_id',auth()->user()->id)->first();
            if($check_request_receive){
                if($check_request_receive->status == 'pending'){
                    $btnTxt = 'Accept';
                    $btnstatus = '';
                    $cancelBtn = '&nbsp;&nbsp;<button class="btn btn-secondary btn-sm cancelRequestbtn">Cancel</button>';
                    $data_id = $check_request_receive->id;
                }else if($check_request_receive->status == 'accepted'){
                    $btnTxt = 'Unfriend';
                    $btnstatus = '';
                }
                $data_sender_id = $user->id;
            }

            $html .= '<li class="list-group-item d-flex justify-content-between align-items-center"><div>'.$user->name.'</div> <button class="btn btn-primary btn-sm '.($check_request_receive ? 'acceptFriendRequest' : 'sendFriendRequest').'" '.$btnstatus.' data-id="'.$data_id.'" data-sender-id="'.$data_sender_id.'">'.$btnTxt.'</button>'.$cancelBtn.'</li>';
        }
        return response()->json(['status'=>'success','data'=>$html]);
    }
    public function send_request(Request $request)
    {
        $already_req_send = FriendRequest::where('sender_id',$request->sender_id)->where('receiver_id',$request->user_id)->first();
        if($already_req_send != []){
            return response()->json(['status'=>'error','msg'=>'Request is already Sent!']);
        }
        $friend_request = new FriendRequest();
        $friend_request->sender_id = $request->sender_id;
        $friend_request->receiver_id = $request->user_id;
        $friend_request->status = 'pending';
        $friend_request->save();
        $receiver = User::find($request->user_id);
        event(new FriendRequestSent($friend_request,auth()->user(),$receiver));

        $notification = new Notification();
        $notification->sender_id = $request->sender_id;
        $notification->receiver_id = $request->user_id ?? null;
        $notification->group_id = $request->group_id ?? null;
        $notification->message = "Send you a friend request.";
        $notification->url = null;
        $notification->status = 0;
        $notification->save();

        event(new Notifications($notification,auth()->user()));
        return response()->json(['status'=>'success']);
    }

    public function accept_request(Request $request)
    {
        $friend_request = FriendRequest::find($request->id);
        if($friend_request->status == 'accepted'){
            return response()->json(['status'=>'error','msg'=>'Friend Request in already accepted!']);
        }
        $friend_request->status = 'accepted';
        $friend_request->update();

        $friend = new Friend();
        $friend->user_id = $request->sender_id;
        $friend->friend_id = auth()->user()->id;
        $friend->save();

        
        $notification = new Notification();
        $notification->sender_id = auth()->user()->id;
        $notification->receiver_id = $request->sender_id ?? null;
        $notification->group_id = $request->group_id ?? null;
        $notification->message = "Accept your friend request.";
        $notification->url = null;
        $notification->status = 0;
        $notification->save();
        dd($notification);

        event(new Notifications($notification,auth()->user()));

        return response()->json(['status'=>'success']);

    }
    public function cancel_request(Request $request)
    {
        $friend_request = FriendRequest::find($request->id);
        $friend_request->delete();
        return response()->json(['status'=>'success']);

    }
    public function read_notification(Request $request){
        $notification = Notification::find($request->id);
        $notification->status = 1;
        $notification->update();
        return response()->json(['status'=>'success']);
    }
    public function delete_notification(Request $request){
        $notification = Notification::find($request->id);
        $notification->delete();
        return response()->json(['status'=>'success']);
    }
}
