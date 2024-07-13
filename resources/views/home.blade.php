@extends('layouts.app')

@section('content')
@include('partials.css.home-css')
<div class=" " id="main">
    <div class="card border-radius-0">
        <div class="row justify-content-center">
        <div class="col-md-4 p-0 sidebar">
            <div class="px-3 mt-3">
                <input type="text" name="" class="form-control" placeholder="Search Friends" id="search_friend">
            </div>
            <ul class="nav nav-tabs" id="friend_group" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true"><i class="fa fa-user"></i> Friends</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false"><i class="fa fa-group"></i> Groups</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="pb-0 pt-2 userList">
                    <ul class="list-group mt-2 userListUl" id="userListUl">
                        
                        @foreach ($friends as $user)
                            @php
                                $unread_chats = \App\Models\Chat::where('receiver_id', Auth::user()->id)->where('sender_id', $user->id)->where('read_', 0)->count();
                                $last_message = \App\Models\Chat::where(function($query) use($user){
                                                    $query->where('sender_id',$user->id)
                                                    ->orwhere('sender_id',Auth::user()->id);
                                                })->where(function($query) use($user){
                                                    $query->where('receiver_id',$user->id)
                                                    ->orwhere('receiver_id',Auth::user()->id);
                                                })->latest()->first();
                            @endphp
                             <li class="list-group-item friend " data-id="{{ $user->id }}" id="friend_{{ $user->id }}">
                                <div class="user_name_and_pro">
                                    <div>
                                        <img src="{{ gravatar_url($user->email) }}" class="profile_image" alt="">
                                    </div>
                                    <div>
                                        <span><strong>{{ $user->name }}</strong> <span class="status offline" id="status_{{ $user->id }}"></span></span><br>
                                        <span class="last_msg">{{ $last_message && $last_message ? substr_replace($last_message->message, '...', 40) : 'Say hi to '.$user->name }}</span>
                                    </div>
                                </div>
                                <span class="badge bg-primary read" id="unread_{{ $user->id }}">{{ $unread_chats }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="groupList">
                        <div class="card-header d-flex justify-content-between align-items-center">
                                <p class="m-0"></p>
            
                                <button class="btn btn-primary btn-sm text-white"  data-bs-toggle="modal" data-bs-target="#createGroupModal">Create</button>
                            </div>
                            <div class="">
                                <ul class="list-group" id="groups">
                                    
                                </ul>
                            </div>
                    </div>
                </div>
            </div>
                
       
                
        </div>
        <div class="col-md-8 user_chats" id="user_chats">
            <div class=" " id="single_chat">
                <div class="card-body p-0">
                    <div id="chats">
                        
                    </div>
                    <div  class="messageForm">
                        <textarea type="text" name="" id="message" class="form-control" placeholder="Message..." rows="1"></textarea>
                        <button id="emojiButton">😀</button>
                        <input type="file" name="" hidden id="file_upload">
                        <label id="imageButton" for="file_upload"><i class="fa fa-image"></i></label>
                        <button id="sendMessage" class="btn btn-info text-white" >Send</button>
                    </div>
                </div>
            </div>
        </div> 
        <div class="col-md-8" id="default_chat_view"></div>
    </div>
    </div>
    
</div>

<div id="confirmFileModal" class="d-none">
    <div class="modal_content">
        <div class="card">
            <div class="card-body">
                <p>Do you want to send this file?</p>
                <button class="btn btn-danger" id="confirmFile">Confirm</button>
                <button class="btn btn-info text-white" id="cancelFile">Cancel</button>
        </div>
    </div>
</div>
</div>
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Create Group</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Group Image</label>
                <input type="file" class="form-control" id="group_image">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Group Name</label>
                <input type="text" class="form-control" id="group_title">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Members Limit</label>
                <input type="number" class="form-control" id="group_limit">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="createGroupBtn">Create</button>
        </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add Member</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="text" name="" id="search_member" class="form-control" placeholder="Search People's">
            <ul class="list-group mt-3" id="search_members_list">
                <li class="list-group-item">No Data found</li>
            </ul>
        </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seeMemberModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">All Members</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <ul class="list-group mt-3" id="all_members_list">
                <li class="list-group-item">No Data found</li>
            </ul>
        </div>
        </div>
    </div>
</div>
@section('js')
@include('partials.script.chat')
@include('partials.script.group-chat')
@endsection
@endsection
