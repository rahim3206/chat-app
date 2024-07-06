import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});

window.Echo.channel('delete-chat')
.listen('Delete',(id) => {
    $('#chat_'+id.id).remove();
});

window.Echo.channel('update-chat')
.listen('Update',(data) => {
    $('#chat_'+data.data.id).find('.chat_msg').text(data.data.message);
});

window.Echo.channel('chat-message')
        .listen('Message',(data) => {

            // if(sender_id == data.data.receiver_id){
            //     showNotification("Hello", "New Message Received");
            // }
            // console.log(data.data.sender_id);
            let element = $('#friend_'+data.data.sender_id);
            element.prependTo(element.parent());
            if(sender_id == data.data.receiver_id && receiver_id == data.data.sender_id){
                if(data.data.type == 'file'){
                    var html = `<div class="receiver_message" id="chat_${data.data.id}">
                                    <img src="images/${data.data.message}" width="auto" height="100px">`;
                        if(data.data.sender_id != sender_id){
                            html += `<a href="{{ asset('images') }}/${data.data.message}" class="download_image" download><i class="fa fa-download"></i></a>`;
                        }
                        html += `</div>`;
                }else{
                    var html = `<div class="receiver_message" id="chat_${data.data.id}">
                                    <p  class="chat_msg">${data.data.message}</p>
                                </div>`;
                }
                $('#chats').append(html);
                $.ajax({
                    url:"/seen_message",
                    type:"POST",
                    data:{sender_id:data.data.receiver_id,receiver_id:data.data.sender_id,message_id:data.data.id,"_token":$('meta[name="csrf-token"]').attr('content')},
                    success:function(response){

                    },
                });
                scrollToBottom();
            }
        });

window.Echo.channel('group-message')
.listen('GroupMessage',(data) => {
    // console.log(data);
    if(data.data.type == 'alert'){
        load_groups();
    }
    if(group_id == data.data.group_id && sender_id != data.data.user_id){
        if(data.data.type == 'file'){
            var html = `<div class="receiver_message" id="chat_${data.data.id}">
                            <span class="gr_file_chat_user">${data.sender.name}</span>
                            <img src="images/${data.data.message}" width="auto" height="100px">`;
                    if(data.data.sender_id != sender_id){
                        html += `<a href="{{ asset('images') }}/${data.data.message}" class="download_image" download><i class="fa fa-download"></i></a>`;
                }
                html += `</div>`;
        }else if(data.data.type == 'alert'){
            html = `<p class="text-center">${data.data.message}</p>`;
        }else{
            var html = `<div class="receiver_message" id="chat_${data.data.id}">
                            <p><span class="gr_chat_user">${data.sender.name}</span><span class="chat_msg">${data.data.message}</span></p>
                        </div>`;
        }
        $('#chats').append(html);

        scrollToBottom();
    }
});

window.Echo.channel('unread-message')
.listen('Unread',(data) => {
    for (let a = 0; a < data.data.length; a++) {
        if(data.data[a].receiver_id == sender_id){
            $('#unread_'+data.data[a].sender_id).text(data.data.length);
        }
    }
});

window.Echo.join('user-status')
.here((users) => {
    for (let i = 0; i < users.length; i++) {
        if (typeof sender_id !== 'undefined' && sender_id != users[i].id) {
            $('#status_'+users[i].id).addClass('online');
            $('#status_'+users[i].id).removeClass('offline');
        }
    }

})
.joining((user) => {
    $('#status_'+user.id).addClass('online');
    $('#status_'+user.id).removeClass('offline');
})
.leaving((user) => {
    $('#status_'+user.id).removeClass('online');
    $('#status_'+user.id).addClass('offline');
})
.listen('Status',(e) => {
    console.log(e);
});


window.Echo.private(`send-friend-request.${sender_id}`)
.listen('.FriendRequestSent',(data) => {
    // console.log(data);
    var html = `<li class="list-group-item d-flex justify-content-center align-item-center flex-column" id="friend-request-${data.friend_request.id}">
                    <div><strong>${data.sender.name }</strong></div>
                    <div><button class="btn btn-info btn-sm text-white acceptFriendRequest" data-id="${data.friend_request.id }" data-sender-id="${data.sender.id }">Accept</button>&nbsp;&nbsp;<button class="btn btn-secondary btn-sm cancelRequestbtn"  data-id="${data.friend_request.id }" data-sender-id="${data.sender.id }">Cancel</button></div>
                </li>`;

    $('#friendRequestUl').prepend(html);
    $('#requestCount').text(Number($('#requestCount').text()) + 1);
});


window.Echo.channel(`message-notification`)
.listen('Notifications',(data) => {
    if(data.data.sender_id != sender_id){
        if (data.data.sender_id == receiver_id) {
            //console.log(data);
            $.ajax({
                url: "/delete-notification",
                type: "POST",
                data: { id: data.data.id, '_token': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    // Handle the response if needed
                }
            });
        }else {
            // Check if the received notification should be added to the notification list
            if (group_ids.includes(data.data.group_id) && data.data.sender_id != sender_id || data.data.receiver_id == sender_id) {
            
                var html = `<li class="list-group-item unread d-flex justify-content-start gap-2 align-item-center" id="notification-${ data.data.id }">
                                <div><img src="https://www.gravatar.com/avatar/${md5(data.sender.email)}?s=150&d=wavatar" alt="" class="profile_image"></div>
                                <div>
                                    <strong>${ data.sender.name }</strong><span class="time">Just now</span>
                                    <p class="m-0">${ data.data.message }</p>
                                </div>
                            </li>`;
                $('#notificationUl').prepend(html);
                $('#notificationCount').text(Number($('#notificationCount').text()) + 1);
                showNotification(data.sender.name,data.data.message);
            
        }
    }
}
});


window.Echo.channel(`message-seen.${sender_id}`)
.listen('MessageSeenEvent',(data)=>{
    if(sender_id == data.data.sender_id && receiver_id == data.data.receiver_id){
        // console.log(data);
        $(document).find('.seen_indicator').remove();
        $(`#chat_${data.data.message_id}`).prepend(`<div class="seen_indicator"><img src="https://www.gravatar.com/avatar/${md5(data.receiver_email)}?s=150&d=wavatar" alt="Receiver Profile" class="rounded-circle" height="12px" width="12px"></div>`);

    }
});