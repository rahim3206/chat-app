<style>
    :root{
        --theme:#b6004c;
        --hover-theme:#cf0056;
    }
</style>
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

    // console.log(window_height);

</script>