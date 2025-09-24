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