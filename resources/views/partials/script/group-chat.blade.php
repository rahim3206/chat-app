<script>
    $(document).ready(function(){
        var group_profile = '';
        
        function load_group_chats(){
            $.ajax({
                url:"{{route('load_group_chats')}}",
                type:"GET",
                data:{group_id},
                success:function(response){
                    if(response.status == 'success'){
                        var html = '';
                        var addClass = '';
                        for (let i = 0; i < response.data.length; i++) {
                            if(response.data[i].user_id == sender_id){
                                addClass = 'sender_message';
                            }else{
                                addClass = 'receiver_message';
                            }
                            if(response.data[i].type == 'file'){
                                html += `<div class="${addClass} pt-4" id="chat_${response.data[i].id}">`;
                                html += `<span class="gr_file_chat_user">${response.data[i].user.name}</span>`;
                                if(response.data[i].user_id == sender_id){
                                    html += `<a href="{{ asset('images') }}/${response.data[i].message}" download class="download_image"><i class="fa fa-download"></i></a> <i class="fa fa-trash delete_chat" data-id="${response.data[i].id}"></i>`;
                                }
                                html += `<img src="{{ asset('images') }}/${response.data[i].message}" height="100px" width="auto">`;
                                if(response.data[i].user_id != sender_id){
                                    html += `<a href="{{ asset('images') }}/${response.data[i].message}" class="download_image" download><i class="fa fa-download"></i></a>`;
                                }
                                html += `</div>`;
                            }else if(response.data[i].type == 'alert'){
                                html += `<p class="text-center">${response.data[i].message}</p>`;
                            }
                            else{
                                html += `<div class="${addClass}" id="chat_${response.data[i].id}">`;
                                if(response.data[i].user_id == sender_id){
                                    html += `<i class="fa fa-edit edit_chat" data-id="${response.data[i].id}"></i> <i class="fa fa-trash delete_chat" data-id="${response.data[i].id}"></i>`;
                                }
                                html += `<p>
                                            <span class="gr_chat_user">${response.data[i].user.name}</span>
                                            <span class="chat_msg">${response.data[i].message}</span>
                                        </p>
                                        </div>`;
                            }

                        }
                        $('#chats').html(html);
                        var group_html = `<div class="group_info">
                            <div class="d-flex justify-content-center gap-2">
                                <div class="group_profile">
                                ${group_profile}
                                </div>
                                <h4 id="group_name" class="m-0">${response.group_info.name}</h4>
                            </div>
                            <div class="position-relative">
                                <div class="group_ul d-none">
                                    <ul class="list-group">
                                        <li class="list-group-item" id="see_members"><i class="fa fa-users"></i> Members</li>`;
                                        if(response.group_info.user_id == sender_id){
                                            group_html += `<li class="list-group-item add_member" data-id="${response.group_info.id}" ><i class="fa fa-plus"></i> Add Member</li>`;
                                        }
                                        group_html += `<li class="list-group-item text-danger" id="leave_group"><i class="fa fa-sign-out"></i> Leave Group</li>
                                    </ul>
                                </div>
                                <i class="fa fa-ellipsis-v group_setting" id="group_setting"></i>
                            </div>
                        </div>`;
                        $('#chats').prepend(group_html);
                        scrollToBottom();
                    }
                },
            });
        }
        load_groups();
        $(document).on('click','#createGroupBtn',function(){
            
            let title = $('#group_title').val();
            let image = $('#group_image')[0].files[0];
            let limit = $('#group_limit').val();

            let formData = new FormData();
            formData.append('title', title);
            formData.append('image', image);
            formData.append('limit', limit);
            formData.append('_token', "{{csrf_token()}}");

            $.ajax({
                url:"{{ route('create-group') }}",
                type:"POST",
                data:formData,
                processData: false,
                contentType: false,
                success:function(response){
                    $('#createGroupModal').modal('hide');
                    load_groups();
                },
            });
        });
        $(document).on('click','#group_setting',function(){
            $(document).find('.group_ul').toggleClass('d-none');
        });
        $(document).on('click','.group',function(){
            group_profile = $(this).find('.group_profile').html();
            // console.log(group_profile);
            $('#chats').html('');
            $('#user_chats').css('display','block');
            $('#default_chat_view').addClass('d-none');
            group_id = $(this).data('id');
            receiver_id = null;
            $('.friend').removeClass('active');
            $('.group').removeClass('active');
            $(this).addClass('active');
            $(this).find('.read').text(0);
            load_group_chats();
            scrollToBottom();
            $('#message').focus();
            // console.log(group_id);
        });
        $(document).on('click','.add_member',function(){
            $('#search_member').data('group_id',$(this).data('id'));
            // console.log($(this).data('id'));
            $('#addMemberModal').modal('show');
        });
        $(document).on('keyup','#search_member',function(){
            var value = $(this).val();
            var g_id = $(this).data('group_id');
            // console.log(g_id);
            var html = '';
            $.ajax({
                url:"{{ route('search-members') }}",
                type:"GET",
                data:{value,group_id:g_id},
                success:function(response){
                    if(response.status == 'success'){
                        response.data.forEach(user => {
                            html += `<li class="list-group-item group" data-id="${user.id}">
                                        <span>${user.name}</span>
                                        <button class="btn btn-info btn-sm text-white addMemberBtn" data-user_id='${user.id}' data-group_id='${g_id}'>Add</button>
                                    </li>`;
                        });
                        $('#search_members_list').html(html);
                    }
                },
            });
        });
        $(document).on('click','.addMemberBtn',function(){
            let u_id = $(this).data('user_id');
            let gr_id = $(this).data('group_id');
            let this_ = $(this);
            $.ajax({
                url:"{{ route('add-member') }}",
                type:"POST",
                data:{user_id:u_id,group_id:gr_id,'_token':'{{ csrf_token() }}'},
                success:function(response){
                    if(response.status == 'success'){
                        $(this_).text('Added').attr('disabled',true);
                    }
                }
            });
        });
        $(document).on('click','#see_members',function(){
            $.ajax({
                url:'{{route("members")}}',
                type:'GET',
                data:{group_id},
                success:function(response){
                    var html = "";
                    response.data.forEach(member => {
                        var admin = '';
                        if( sender_id == member.user.id){
                            admin = '(Admin)'
                        }
                        html += `<li class="list-group-item d-flex justify-content-between">${member.user.name+' '+admin}  `;
                            if(sender_id == response.group.user_id && sender_id != member.user.id){
                                html += `<a class="text-danger kick_member" data-id="${member.user.id}" data-group_id="${response.group.id}" href="javascript:void(0)"> Kick</a>`;
                            }
                        html += `</li>`;
                    });
                    $('#all_members_list').html(html);
                    $('#seeMemberModal').modal('show');
                }
            });
        });
        $(document).on('click','#leave_group',function(){
            $.ajax({
                url:'{{route("leave_group")}}',
                type:'POST',
                data:{group_id,user_id:sender_id,'_token':'{{csrf_token()}}'},
                success:function(response){
                    if(response.status == 'success'){
                        $('#chats').html('');
                        group_id = null;
                        load_groups();
                    }
                }
            });
        });
        $(document).on('click','.kick_member',function(){
            let user_id = $(this).data('id');
            let group_id = $(this).data('group_id');
            $.ajax({
                url:'{{route("kick_member")}}',
                type:'POST',
                data:{group_id,user_id,'_token':'{{csrf_token()}}'},
                success:function(response){
                    $('#seeMemberModal').modal('hide');
                }
            });
        });
    });
</script>