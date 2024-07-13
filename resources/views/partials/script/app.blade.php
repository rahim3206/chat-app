<script>
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
         $(document).on('click', function(event) {
            // Check if the click is outside the notification box
            if (!$(event.target).closest('.notifications, .notificationBtn, .friendRequest').length) {
                $('.notifications').addClass('d-none');
            }
        });

        $(document).on('click', '.friendRequest', function(event){
            $('#notificationUl').parent().addClass('d-none');
            $(this).parent().find('.notifications').toggleClass('d-none');
            event.stopPropagation(); // Prevent the document click handler from firing
        });

        $(document).on('click', '.notificationBtn', function(event){
            $('#friendRequest').addClass('d-none');
            $(this).parent().find('.notifications').toggleClass('d-none');
            event.stopPropagation(); // Prevent the document click handler from firing
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
                        load_friends();
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
            group_id = null;
            receiver_id = null;
            $('#user_chats').css('display','none');
         });

         $(document).ready(function () {
            var elements = $('#suggestedUser');
            
            $(document).click(function (event) {
                if (!elements.is(event.target) && elements.has(event.target).length === 0) {
                    elements.addClass('d-none');
                }
            });

            $(document).on('click','.notification_item',function(){
                var _this = $(this);
                var type = $(this).attr('data-type');
                type = type.split("_");
                var msg_id = '#chat_'+$(this).attr('data-msg');
                if(type[0] == 'friend'){
                    $(`#friend_${type[1]}`).trigger('click');
                }else if(type[0] == 'group'){
                    $(`#group_${type[1]}`).trigger('click');
                }
                setTimeout(() => {
                    var $chatContainer = $('#chats');
                    var $message = $(msg_id);
                    
                    if ($message.length) {
                        $chatContainer.animate({
                            scrollTop: $message.offset().top - $chatContainer.offset().top + $chatContainer.scrollTop() - 250
                        }, 500); 
                    }
                    $(msg_id).css('background-color','#f4f4f4');
                }, 3000);
                

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
    var window_height = $(window).height();
    $('#userListUl').css('height', window_height - 180);
    $('#groups').css('height', window_height - 160);

    $(document).on('keyup','#search_friend',function(){
        let value = $(this).val();
        var search_type = 'friend';
        if($('#home-tab').hasClass('active')){
            search_type = 'friend';
        }else if($('#profile-tab').hasClass('active')){
            search_type = 'group';
        }

        $.ajax({    
            url:"{{ route('search-friend') }}",
            type:"GET",
            data:{value,search_type},
            success:function(response){
                var member_count = 1;
                if(response.status == 'success'){
                    //console.log(response.data);
                    var html = '';
                    if(search_type == 'friend'){
                        response.data.forEach(user => {
                            html += `<li class="list-group-item friend " data-id="${user.id}" id="friend_${user.id}">
                                    <div class="user_name_and_pro">
                                        <div>
                                            <img src="https://www.gravatar.com/avatar/${md5(user.email)}?s=150&d=wavatar" class="profile_image" alt="">
                                        </div>
                                        <div>
                                            <span><strong>${user.name}</strong> <span class="status offline" id="status_${user.id}"></span></span><br>
                                            <span class="last_msg">${user.last_message && user.last_message ? user.last_message.slice(0, 40) + '...' : 'Say hi to '+user.name}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary read" id="unread_${user.id}">${user.unread_chats}</span>
                                </li>`;
                        });
                        $('#userListUl').html(html);
                    }
                    else{
                        response.data.forEach(group => {
                            html += `<li class="list-group-item group" id="group_${group.id}" data-id="${group.id}">
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

                                        html += `</div><div>
                                            
                                    <span><strong>${group.name}</strong></span>
                                    <p class="m-0 last_msg">${group.last_message && group.last_message.message ? group.last_message.message.slice(0, 40) + '...' : 'Say hi to your new group'}</p>
                                    </div>
                                </div>`;
                                
                        html += `<span class="badge bg-primary read" id="unread_${group.id}">0</span>
                                </li>`;
                        });
                        $('#groups').html(html);

                    }
                  
                }
            }
        });
    });
</script>