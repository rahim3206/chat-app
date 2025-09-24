
<script>
    $(document).ready(function(){

        var file = null ;
        var edit_chat_id = null;
        var user_profile = ''; 

        function load_chats(){
            $.ajax({
                url:"{{route('load_chats')}}",
                type:"GET",
                data:{sender_id,receiver_id},
                success:function(response){
                    if(response.status == 'success'){
                        var html = '';
                        var addClass = '';
                        var lastSeenMessageId = null;
                        for (let i = 0; i < response.data.length; i++) {
                            if(response.data[i].sender_id == sender_id){
                                addClass = 'sender_message';
                            }else{
                                addClass = 'receiver_message';
                            }

                            if(response.data[i].last_seen_message_id){
                                lastSeenMessageId = response.data[i].last_seen_message_id;
                            }
                            if(response.data[i].type == 'file'){
                                html += `<div class="${addClass}" id="chat_${response.data[i].id}">`;
                                if(response.data[i].sender_id == sender_id){
                                    html += `<a href="{{ asset('images') }}/${response.data[i].message}" download class="download_image"><i class="fa fa-download"></i></a> <i class="fa fa-trash delete_chat" data-id="${response.data[i].id}"></i>`;
                                }
                                html += `<img src="{{ asset('images') }}/${response.data[i].message}" height="100px" width="auto" class="chat_img">`;
                                if(response.data[i].sender_id != sender_id){
                                    html += `<a href="{{ asset('images') }}/${response.data[i].message}" class="download_image" download><i class="fa fa-download"></i></a>`;
                                }
                                html += `</div>`;
                            }else{
                                html += `<div class="${addClass}" id="chat_${response.data[i].id}">`;
                                if(response.data[i].sender_id == sender_id){
                                    html += `<i class="fa fa-edit edit_chat" data-id="${response.data[i].id}"></i> <i class="fa fa-trash delete_chat" data-id="${response.data[i].id}"></i>`;
                                }
                                html += `<p class="chat_msg">${response.data[i].message}</p>
                                        </div>`;
                            }

                        }
                        $('#chats').html(html);
                        $('#chats').prepend(`<div class="group_info justify-content-start align-items-center">
                            <a id="backHome"><i class="fa fa-angle-left"></i></a>
                            <div class="d-flex justify-content-center gap-2">
                                ${user_profile}
                            </div>
                        </div>`);
                        // console.log(response.receiver_email);
                        $(`#chat_${response.last_seen_id}`).prepend(`<div class="seen_indicator"><img data-check="${response.receiver_email}" src="https://www.gravatar.com/avatar/${md5(response.receiver_email)}?s=150&d=wavatar" alt="Receiver Profile" class="rounded-circle" height="12px" width="12px"></div>`);
                        scrollToBottom();
                    }
                },
            });
        }

        $(document).on('click','.friend',function(){
            var user_info_clone = $(this).find('.user_name_and_pro').clone();
            user_info_clone.find('.last_msg').remove();
            user_profile = user_info_clone.html();
            $('#chats').html('');
            $('#user_chats').css('display','block');
            $('#default_chat_view').addClass('d-none');
            group_id = null;
            receiver_id = $(this).data('id');
            $('.friend').removeClass('active')
            $(this).addClass('active')
            $(this).find('.read').text(0);
            load_chats();
            scrollToBottom();
            $('#message').focus();
            // $('#single_chat').removeClass('d-none');
        });

        $(document).on('click','#sendMessage',function(e){
            var message = $('#message').val();
            var _this = $(this);
            if(edit_chat_id == null)
            {
                if(receiver_id == null && group_id == null){
                    return false;
                }
                if(file == null && message == ''){
                    return false;
                }
                $.ajax({
                    url:'{{ route("send-message") }}',
                    type:"POST",
                    data:{sender_id,receiver_id,group_id,message,file,"_token": '{{ csrf_token() }}'},
                    beforeSend:function(){
                        $(_this).text('Sending...').attr('disabled',true);
                    },
                    success:function(response){
                        if(response.status == "success")
                        {
                            if(response.data.type == 'file'){
                                var html = `<div class="sender_message" id="chat_${response.data.id}">`;
                                    if(group_id != null){
                                        html += `<span class="gr_file_chat_user">{{ auth()->user()->name }}</span>`;
                                    }
                                    html +=`<a href="{{ asset('images') }}/${response.data.message}" download class="download_image"><i class="fa fa-download"></i></a><i class="fa fa-trash delete_chat" data-id="${response.data.id}"></i>`;
                                    html +=`<img src="{{ asset('images') }}/${response.data.message}" height="100px" width="auto" class="chat_img">
                                        </div>`;
                            }else{
                                var html = `<div class="sender_message" id="chat_${response.data.id}">`;
                                    html +=`<i class="fa fa-edit edit_chat" data-id="${response.data.id}"></i><i class="fa fa-trash delete_chat" data-id="${response.data.id}"></i>`;
                                    html +=` <p>`;
                                    if(group_id != null){
                                        html += `<span class="gr_chat_user">{{ auth()->user()->name }}</span>`;
                                    }
                                    html += `<span class="chat_msg">${response.data.message}</span></p>
                                        </div>`;
                            }

                            $('#chats').append(html);
                        }
                        $('#message').val(null);
                        file = null;
                    },
                    complete:function(){
                        $(_this).text('Send').attr('disabled',false);
                        scrollToBottom();
                        file = null;
                        let element = $('#friend_'+receiver_id);
                        element.prependTo(element.parent());
                    },
                });
            }else{
                $.ajax({
                    url:'{{ route("update-message") }}',
                    type:"POST",
                    data:{id:edit_chat_id,group_id,message,"_token": '{{ csrf_token() }}'},
                    beforeSend:function(){
                        $(_this).text('Updating...').attr('disabled',true);
                    },
                    success:function(response){
                        if(response.status == "success")
                        {
                            $('#message').val(null);
                            $('chat_'+response.data.id).find('.chat_msg').text(response.data.message);
                        }
                    },
                    complete:function(){
                        $(_this).text('Send').attr('disabled',false);
                        scrollToBottom();
                        file = null;
                        edit_chat_id = null;
                        let element = $('#friend_'+receiver_id);
                        element.prependTo(element.parent());
                    },
                });
            }

        });

        $(document).on('keyup', '#message', function(event) {
            if (event.key === 'Enter' || event.keyCode === 13) {
                $('#sendMessage').trigger('click');
            }
        });

        $(document).on('change','#file_upload',function(){
            var this_file = this.files[0];
            var reader = new FileReader();
            reader.onload = function (event) {
                var imageData = event.target.result;
                file = imageData;
            };
            reader.readAsDataURL(this_file);
            $('#confirmFileModal').removeClass('d-none');
        });
        $(document).on('click','#cancelFile',function(){
            file = null;
            $('#confirmFileModal').addClass('d-none');
        })
        $(document).on('click','#confirmFile',function(){
            $('#sendMessage').trigger('click');
            $('#confirmFileModal').addClass('d-none');
        })

        $(document).on('click','.delete_chat',function(){
            let id = $(this).data('id');
            let this_ = $(this);
            $.ajax({
                url:'{{ route("delete-chat") }}',
                type:"POST",
                data:{id,group_id,"_token": '{{ csrf_token() }}'},
                success:function(response){
                    if(response.status == "success")
                    {
                        $(this_).parent().remove();
                    }
                },
            });
        });

        $(document).on('click','.edit_chat',function(){
            edit_chat_id = $(this).data('id');
            let text = $(this).parent().find('.chat_msg').text();
            // parentElement.find('span').remove(); 
            // let text = parentElement.find('p').trim();
            $('#message').val(text);
            $('#sendMessage').text('Update');

        });
        
        function shift_nav(){
            var rightNav = $('#rightNav').html();
            var leftNav = $('#leftNav').html();
            $('#rightNav').remove();
            $('#navbar-brand').after(`<ul class="navbar-nav ms-auto">${rightNav}</ul><ul class="navbar-nav" style="width:100%">${leftNav}</ul>`);
        }
        var document_width = $(window).width();
        if(document_width <= 767){ 
            shift_nav();
        }
    });

</script>