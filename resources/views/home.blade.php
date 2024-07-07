@extends('layouts.app')

@section('content')
@include('partials.css.home-css')
<div class="container " id="main">
    <div class="card">
        <div class="row justify-content-center">
        <div class="col-md-4 pr-0">
                <div class="card-body userList">
                    <input type="text" name="" class="form-control" placeholder="Search Friends">
                    <ul class="list-group mt-2 userListUl">
                        @foreach ($friends as $user)
                            @php
                                $unread_chats = \App\Models\Chat::where('receiver_id', Auth::user()->id)->where('sender_id', $user->id)->where('read_', 0)->count();
                            @endphp
                             <li class="list-group-item friend " data-id="{{ $user->id }}" id="friend_{{ $user->id }}">
                                <div class="user_name_and_pro">
                                    <img src="{{ gravatar_url($user->email) }}" class="profile_image" alt="">
                                    <span>{{ $user->name }} <span class="status offline" id="status_{{ $user->id }}"></span></span>
                                </div>
                                <span class="badge bg-primary read" id="unread_{{ $user->id }}">{{ $unread_chats }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
        <div class="groupList">
            <div class="card-header d-flex justify-content-between align-items-center">
                    <p class="m-0">{{ __('Groups') }}</p>

                    <button class="btn btn-info btn-sm text-white"  data-bs-toggle="modal" data-bs-target="#createGroupModal">Create</button>
                </div>
                <div class="card-body">
                    <ul class="list-group" id="groups">
                        
                    </ul>
                </div>
        </div>
                
        </div>
        <div class="col-md-8 user_chats" id="user_chats">
            <div class=" " id="single_chat">
                <div class="card-body p-0">
                    <div id="chats">
                        
                    </div>
                    <div  class="messageForm">
                        <input type="text" name="" id="message" class="form-control" placeholder="Message...">
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
