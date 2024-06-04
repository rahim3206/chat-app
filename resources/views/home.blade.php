@extends('layouts.app')

@section('content')
<style>
    .sender_message{
        display: flex;
        justify-content: end;
        margin-bottom: 5px;
    }
    .receiver_message{
        margin-bottom: 5px;
    }
    .sender_message p,
    .receiver_message p
    {
        margin: 0px;
        background-color: rgb(236, 235, 235);
        padding: 6px 8px;
        border-radius: 20px;
        width: fit-content;
    }
    #chats{
        margin-bottom: 10px;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">{{ __('Users') }}</div>

                <div class="card-body">
                    <ul class="list-group">
                        @foreach ($users as $user)
                             <li class="list-group-item friend" data-id="{{ $user->id }}">{{ $user->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Chats') }}</div>

                <div class="card-body">
                    <div id="chats">
                       
                    </div>
                    <div  class="d-flex">
                        <input type="text" name="" id="message" class="form-control" placeholder="Message...">
                        <button id="sendMessage" class="btn btn-info text-white">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
    <script>

function load_chats(){
                $.ajax({
                    url:"{{route('load_chats')}}",
                    type:"GET",
                    data:{sender_id,receiver_id},
                    success:function(response){
                        if(response.status == 'success'){
                            var html = '';
                            var addClass = '';
                            for (let i = 0; i < response.data.length; i++) {
                                if(response.data[i].sender_id == sender_id){
                                    addClass = 'sender_message';
                                }else{
                                    addClass = 'receiver_message';
                                }
                                html +=`<div class="${addClass}">
                                            <p>${response.data[i].message}</p>
                                        </div>`;
                                    
                            }
                            $('#chats').html(html);
                        }
                    },
                });
            }
        $(document).on('click','.friend',function(){
            $('#chats').html('');
            receiver_id = $(this).data('id');
            $('.friend').removeClass('active')
            $(this).addClass('active')
            load_chats();
        });
        

        $(document).on('click','#sendMessage',function(e){
            
            var message = $('#message').val();
            $.ajax({
                url:'{{ route("send-message") }}',
                type:"POST",
                data:{sender_id,receiver_id,message,"_token":"{{ csrf_token() }}"},
                success:function(response){
                    if(response.status == "success")
                    {
                        var html = `<div class="sender_message">
                                        <p>${response.data.message}</p>
                                    </div>`;
                        $('#chats').append(html);
                    }
                }
            });

        });
    </script>
@endsection
@endsection
