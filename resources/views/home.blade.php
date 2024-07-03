@extends('layouts.app')

@section('content')
<style>
    .sender_message{
        display: flex;
        justify-content: end;
        margin-bottom: 5px;
        align-items: center;
        position: relative;
        margin-right: 12px;
        margin-left: 12px;
    }
    .receiver_message{
        margin-bottom: 5px;
        position: relative;
        margin-right: 12px;
        margin-left: 12px;
    }
    #chats .sender_message:nth-child(1){
        margin-top: 10px;
    }
    .sender_message p,
    .receiver_message p
    {
        margin-bottom: 0px;
        background-color: rgb(236, 235, 235);
        padding: 4px 8px;
        border-radius: 5px;
        width: fit-content;
        max-width: 50%;
    }

    #chats{
        margin-bottom: 10px;
        height: 70svh;
        overflow-y: scroll;
        overflow-x: hidden;
        /* padding: 0px 15px 15px 15px; */
        position: relative;
    }
    #chats::-webkit-scrollbar  {
        display: none;
    }
    .status{
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-flex;
        top: -4px;
        position: relative;
    }
    .status.online{
        background-color: #73ff73;
    }
    .status.offline{
        background-color: grey;
    }
    .friend,.group{
        display: flex;
        cursor: pointer;
        justify-content: space-between ;
        align-items: center
    }
    .messageForm{
        display: flex;
        gap: 10px;
        padding: 12px;
    }
    #emojiButton,#imageButton{
        border: none;
        background-color: transparent;
        cursor: pointer;
        background-color: rgb(233, 233, 233);
        border-radius:5px;
        padding: 0px 10px;
        display: flex;
        align-items: center;
    }
    #confirmFileModal{
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 3;
    }
    .badge{
        line-height: 16px;
    }
    .delete_chat{
        cursor: pointer;
        color: red;
        margin-right: 5px;
    }
    .download_image{
        cursor: pointer;
        color: rgb(0, 110, 255);
        margin: 0px 5px;
    }
    .edit_chat{
        cursor: pointer;
        color: black;
        margin: 0px 5px;
    }
    .gr_chat_user{
        font-size: 11px;
        cursor: pointer;
        color: rgb(0, 110, 255);
        display: flex;
        top: 3px;
        position: relative;
    }
    .gr_file_chat_user{
        font-size: 11px;
        cursor: pointer;
        color: rgb(0, 110, 255);
        display: flex;
        top: -4px;
        position: absolute;
        background: #ecebeb;
        background-color: rgb(236, 235, 235);
        padding: 4px 8px;
        border-radius: 5px;
    }
    .group_info{
        display: flex;
        justify-content: space-between;
        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
        position: sticky;
        top: 0px;
        z-index: 3;
        padding: 10px;
        background: #f8fafc;
    }
    .group_setting{
        cursor: pointer;
        padding: 5px;
    }
    .group_ul{
        position: absolute;
        right: 0px;
        top: 24px;
        width: 136px;
    }
    .group_ul ul li{
        cursor: pointer;
    }
    
    .user_chats{
        border-left: 1px solid #ccced0;
        padding-left: 0px;
        display: none;
    }
    #main .card{
        background-color: #fff
    }
    .profile_image{
        widows: 40px;
        height: 40px;
        border-radius: 50%
    }
    .friend.active,
    .group.active
    {
        background-color: rgb(236, 236, 236);
        color: #000
    }
    .group_profile{
        position: relative;
        width: 40px;
        height: 40px;
    }
    .g_pro_1,
    .g_pro_2,
    .g_pro_3{
        width: 25px !important;
        height: 25px;
        position: absolute;
        border-radius: 50%;
        border: 1px solid white;
    }
    .g_pro_1{
        top: 0px;
        left: 0px;
    }
    .g_pro_2{
        top: 5px;
        right: 0px;
    }
    .g_pro_3{
        bottom: 0px;
        left: 5px;
    }
    .userList,.groupList{
        height: 50%;
        overflow: hidden;
    }
    .userListUl{
        overflow-y: scroll;
        height: 82%;
    }
    #backHome{
        display: none;
    }#default_chat_view{
        border-left: 1px solid #ccced0;
    }
    @media(max-width:800px){
        .user_chats{
            position: fixed;
            top: 0px;
            left: 0px;
            background-color: #fff;
            height: 100svh;
            z-index: 2;
            padding-right: 0px;
        }
        .messageForm{
            position: absolute;
            bottom: 0px;
            width: 100%;
        }
        #backHome{
            display: flex;
            font-size: 25px;
            padding: 0px 12px 0px 5px;
            cursor: pointer;
        }
    }
</style>
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
