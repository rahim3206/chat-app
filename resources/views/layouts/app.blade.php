<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.0/dist/emoji-button.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.0/dist/emoji-button.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-md5@0.8.3/src/md5.min.js"></script>
    @include('partials.script.global')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('js')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container-fluid">
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
                                        @php
                                            $not_type = '';
                                            if($notification->receiver_id != null){
                                                $not_type = 'friend_'.$notification->sender_id;
                                            }else if($notification->group_id != null){
                                                $not_type = 'group_'.$notification->group_id;
                                            }
                                        @endphp
                                            <li class="list-group-item notification_item {{ $notification->status == 0 ? 'unread' : '' }} d-flex justify-content-start gap-2 align-item-center" id="notification-{{ $notification->id }}" data-id="{{ $notification->id }}" data-type="{{$not_type}}" data-msg="{{ $notification->url }}">
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
                                    <img src="{{ gravatar_url(Auth::user()->email) }}" class="profile_image main">
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

        <main class="">
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
     </script>
     @include('partials.script.app')
</body>
</html>
