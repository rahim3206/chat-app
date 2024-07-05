<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.0/dist/emoji-button.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.0/dist/emoji-button.min.js"></script>
    <script src="
https://cdn.jsdelivr.net/npm/js-md5@0.8.3/src/md5.min.js
"></script>
    <script>
        let sender_id = @json(auth()->user()->id ?? null);
        let receiver_id ;
        let group_id ;
        let group_ids = @auth @json(\App\Models\GroupMember::where('user_id',auth()->user()->id )->pluck('group_id') ?? []); @endauth
        function scrollToBottom() {
            var scrollableDiv = document.getElementById('chats');
            scrollableDiv.scrollTop = scrollableDiv.scrollHeight;
        }
        function load_groups(){
            let html = '';
            $.ajax({
                url:"{{ route('groups') }}",
                type:"GET",
                data:{user_id:sender_id},
                success:function(response){
                    if(response.status == 'success'){
                        var member_count = 1;
                        response.data.forEach(group => {
                            group_ = group.group_id;
                            html += `<li class="list-group-item group" data-id="${group.id}">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="group_profile">`;
                                                group.group_members.forEach((member, index) => {
                                                    index++;
                                                    if (index < 4) {
                                                        const avatarUrl = `https://www.gravatar.com/avatar/${md5(member.user.email)}?s=150&d=wavatar`;
                                                        html += `<img src="${avatarUrl}" alt="avatar" class="g_pro_${index}">`;
                                                        member_count++;
                                                    }
                                                });

                            html += `</div>
                                        <span>${group.name}</span></div>`;
                                    
                            html += `<span class="badge bg-primary read" id="unread_${group.id}">0</span>
                                    </li>`;
                        });
                        $('#groups').html(html);
                    }
                },
            });
        }
        function showNotification(title, body) {
            if (!("Notification" in window)) {
                alert("This browser does not support desktop notifications");
            }

            else if (Notification.permission === "granted") {
                new Notification(title, { body: body });
            }

            else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(function (permission) {
                    if (permission === "granted") {
                        new Notification(title, { body: body });
                    }
                });
            }
        }
  

    </script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('js')
    <style>
        .searchuser{
            width: 500px
        }
        .suggestedUser{
            position: absolute;
            top: 36px;
            width: 500px;
            left: 0px;
            background-color: #fff;
            z-index: 2;
            max-height: 240px;
            overflow-y: scroll;
        }
        .suggestedUser ul
        {
            border-radius:0px;
            border: none; 
            background-color: transparent;
        }
        .suggestedUser ul li
        {
            background-color: transparent;
        }
        
        .notifications{
            position: absolute;
            top: 40px;
            width: 300px;
            background-color: #fff;
            border: 1px solid grey;
            border-radius: 10px;
            z-index: 4;
            max-height: 320px;
            height: 320px;
            overflow-y: scroll;
        }
        .friendRequest{
            position: relative;
            padding-right: 20px !important
        }
        #requestCount,#notificationCount{
            background-color: red;
            color: #fff;
            padding: 0px 4px;
            border-radius: 50%;
            font-size: 11px;
            position: absolute;
            top: 4px;
            right: 5px;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0px rgba(255, 0, 0, 0.2);
            }
            100% {
                box-shadow: 0 0 0 10px rgba(255, 0, 0, 0);
            }
        }
        #notificationUl li{
            cursor: pointer;
        }
        #notificationUl li.unread{
            background-color: #d2d2d2
        }
        #notificationUl li:hover{
            background-color: #f3f3f3
        }
        .time{
            color: grey;
            font-size: 11px;
            margin-left: 5px
        }
    </style>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav">
                        <div class="position-relative">
                            <input type="search" class="form-control searchuser" id="searchuser" autocomplete="off" placeholder="Search people's here...">
                            <div class="suggestedUser d-none" id="suggestedUser">
                                <ul class="list-group" id="suggestedUserUl">
                                    
                                </ul>
                            </div>
                        </div>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        <li class="nav-item position-relative">
                            <a href="javascript:void(0)" class="nav-link friendRequest"><i class="fa fa-group"></i> <span id="requestCount">{{ count($friend_requests ?? [] ) }}</span></a>
                            <div class="notifications d-none" id="friendRequest">
                                <ul class="list-group h-100" id="friendRequestUl">
                                    @auth
                                        @forelse($friend_requests as $friend_request)
                                            <li class="list-group-item d-flex justify-content-center align-item-center flex-column" id="friend-request-{{ $friend_request->id }}">
                                                <div><strong>{{ $friend_request->sender->name }}</strong></div>
                                                <div><button class="btn btn-info btn-sm text-white acceptFriendRequest" data-id="{{ $friend_request->id }}" data-sender-id="{{$friend_request->sender->id}}">Accept</button>&nbsp;&nbsp;<button class="btn btn-secondary btn-sm cancelRequestbtn" data-id="{{ $friend_request->id }}" data-sender-id="{{$friend_request->sender->id}}">Cancel</button></div>
                                            </li>
                                        @empty
                                            <li class="list-group-item text-center m-auto">No Rrequest Found</li>
                                        @endforelse
                                    @endauth
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item position-relative">
                            <a href="javascript:void(0)" class="nav-link notificationBtn"><i class="fa fa-bell"></i> <span id="notificationCount">{{ count($unread_notifications ?? [] ) }}</span></a>
                            <div class="notifications d-none" style="right: 0">
                                <ul class="list-group" id="notificationUl">
                                    @auth
                                        @foreach($notifications as $notification)
                                            <li class="list-group-item notification_item {{ $notification->status == 0 ? 'unread' : '' }} d-flex justify-content-start gap-2 align-item-center" id="notification-{{ $notification->id }}" data-id="{{ $notification->id }}">
                                                <div><img src="{{ gravatar_url($notification->user->email) }}" alt="" class="profile_image"></div>
                                                <div>
                                                    <strong>{{ $notification->user->name }}</strong><span class="time">{{timeElapsedString($notification->created_at)}}</span>
                                                    <p class="m-0">{{ $notification->message }}</p>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endauth
                                </ul>
                            </div>
                        </li>

                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <img src="{{ gravatar_url(Auth::user()->email) }}" class="profile_image">
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script type="module" defer>
        import { EmojiButton } from 'https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.0';

         const picker = new EmojiButton({autoHide:false});
         const trigger = document.querySelector('#emojiButton');

         picker.on('emoji', selection => {
            const inputField = document.querySelector('#message');
            inputField.value += selection.emoji;
         });

         trigger.addEventListener('click', () => picker.togglePicker(trigger));

         $(document).on('keyup','#searchuser',function(){
            var value = $(this).val();
            $.ajax({
                url:"{{ route('search-user') }}",
                type:"GET",
                data:{value},
                success:function(response){
                    if(response.status == 'success'){
                        $('#suggestedUserUl').html(response.data);
                        $('#suggestedUser').removeClass('d-none');
                    }
                },
            });
         });

         $(document).on('click','.sendFriendRequest',function(){
            let user_id = $(this).data('id');
            let _this = $(this);
            $(this).attr('disabled',true);
            $.ajax({
                url:"{{route('send-request')}}",
                type:"POST",
                data:{sender_id,user_id,'_token':"{{csrf_token()}}"},
                success:function(response){
                    if(response.status == 'success'){
                        _this.text('Send').attr('disabled',true);
                    }
                },
            });
         });

         $(document).on('click','.friendRequest',function(){
            $('#notificationUl').parent().addClass('d-none');
            $(this).parent().find('.notifications').toggleClass('d-none');
         });

        $(document).on('click','.notificationBtn',function(){
            $('#friendRequest').addClass('d-none');
            $(this).parent().find('.notifications').toggleClass('d-none');
        });

         $(document).on('click','.acceptFriendRequest',function(){
            let id = $(this).data('id');
            let sender_id = $(this).data('sender-id');
            let _this = $(this);
            $(this).attr('disabled',true);
            $.ajax({
                'url':'{{ route("accept.friend.request") }}',
                'type':'GET',
                'data': {id,sender_id},
                success:function(response){
                    if(response.status == 'success'){
                        $('#requestCount').text(Number($('#requestCount').text()) - 1);
                        _this.text('Accepted').attr('disabled',true);
                        _this.parent().find('.cancelRequestbtn').attr('disabled',true);
                    }
                },
            });
         });
         $(document).on('click','.cancelRequestbtn',function(){
            let id = $(this).data('id');
            let sender_id = $(this).data('sender-id');
            let _this = $(this);
            $(this).attr('disabled',true);
            $.ajax({
                'url':'{{ route("cancel.friend.request") }}',
                'type':'GET',
                'data': {id,sender_id},
                success:function(response){
                    if(response.status == 'success'){
                        $('#requestCount').text(Number($('#requestCount').text()) - 1);
                        _this.parent().parent().remove();
                    }
                },
            });
         });
         $(document).on('click','#backHome',function(){
            $('#user_chats').css('display','none');
         });

         $(document).ready(function () {
            var elements = $('#suggestedUser');
            // var elem2 = $('#friendRequest');
            
            $(document).click(function (event) {
                if (!elements.is(event.target) && elements.has(event.target).length === 0) {
                    elements.addClass('d-none');
                }
                // if (!elem2.is(event.target) && elem2.has(event.target).length === 0) {
                //     elem2.addClass('d-none');
                // }
            });

            $(document).on('click','.notification_item',function(){
                var _this = $(this);
                if($(this).hasClass('unread')){
                    $.ajax({
                        url:"{{ route('read-notification') }}",
                        type:"POST",
                        data:{id:$(this).data('id'),'_token':"{{csrf_token()}}"},
                        success:function(response){
                            if(response.status == 'success'){
                                _this.removeClass('unread');
                                $('#notificationCount').text(Number($('#notificationCount').text()) - 1);
                            }
                        }
                    });
                }
            });
        });


     </script>
</body>
</html>
