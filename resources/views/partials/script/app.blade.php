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