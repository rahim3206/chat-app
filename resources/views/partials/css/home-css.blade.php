<style>
    .sender_message{
        display: flex;
        justify-content: end;
        margin-bottom: 5px;
        align-items: center;
        position: relative;
        margin-right: 16px;
        margin-left: 16px;
    }
    .receiver_message{
        margin-bottom: 5px;
        position: relative;
        margin-right: 16px;
        margin-left: 16px;
    }
    #chats .sender_message:nth-child(1){
        margin-top: 10px;
    }
    .sender_message p,
    .receiver_message p
    {
        margin-bottom: 0px;
        background-color: rgb(236, 235, 235);
        padding: 4px 8px;
        border-radius: 10px;
        width: fit-content;
        max-width: 50%;
    }
    .receiver_message p{
        background-color: rgb(0, 110, 255);
        color: #fff;
    }
    .receiver_message .gr_chat_user{
        color: #fff;
        font-weight: 600
    }
    #chats{
        margin-bottom: 10px;
        height:78svh;
        overflow-y: scroll;
        overflow-x: hidden;
        /* padding: 0px 15px 15px 15px; */
        position: relative;
    }
    #chats::-webkit-scrollbar  {
        display: none;
    }
    .status{
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-flex;
        top: -4px;
        position: relative;
    }
    .status.online{
        background-color: #73ff73;
    }
    .status.offline{
        background-color: grey;
    }
    .friend,.group{
        display: flex;
        cursor: pointer;
        justify-content: space-between ;
        align-items: center;
        background-color: #fff;
        border-top: none;
        border-left: none;
        border-radius: 0px;
        border-right: none;
    }
    .messageForm{
        display: flex;
        gap: 10px;
        padding: 12px;
    }
    #emojiButton,#imageButton{
        border: none;
        background-color: transparent;
        cursor: pointer;
        background-color: rgb(233, 233, 233);
        border-radius:5px;
        padding: 0px 10px;
        display: flex;
        align-items: center;
    }
    #confirmFileModal{
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 3;
    }
    .badge{
        line-height: 16px;
    }
    .delete_chat{
        cursor: pointer;
        color: red;
        margin-right: 5px;
    }
    .download_image{
        cursor: pointer;
        color: rgb(0, 110, 255);
        margin: 0px 5px;
    }
    .edit_chat{
        cursor: pointer;
        color: black;
        margin: 0px 5px;
    }
    .gr_chat_user{
        font-size: 11px;
        cursor: pointer;
        color: rgb(0, 110, 255);
        display: flex;
        top: 3px;
        position: relative;
    }
    .gr_file_chat_user{
        font-size: 11px;
        cursor: pointer;
        color: rgb(0, 110, 255);
        display: flex;
        top: -4px;
        position: absolute;
        background: #ecebeb;
        background-color: rgb(236, 235, 235);
        padding: 4px 8px;
        border-radius: 5px;
    }
    .group_info{
        display: flex;
        justify-content: space-between;
        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
        position: sticky;
        top: 0px;
        z-index: 3;
        padding: 10px;
        background: #f8fafc;
    }
    .group_setting{
        cursor: pointer;
        padding: 5px;
    }
    .group_ul{
        position: absolute;
        right: 0px;
        top: 24px;
        width: 136px;
    }
    .group_ul ul li{
        cursor: pointer;
    }
    
    .user_chats{
        border-left: 1px solid #ccced0;
        padding-left: 0px;
        display: none;
    }
    #main .card{
        background-color: #fff;
        overflow: hidden;
    }
    .profile_image{
        width: 40px;
        height: 40px;
        border-radius: 50%
    }
    .profile_image.main{
        width: 30px;
        height: 30px;
        border-radius: 50%
    }
    .friend.active,
    .group.active
    {
        background-color: rgb(236, 236, 236);
        color: #000;
        border: 0px;
        border-radius: 0px;
    }
    .friend:hover,
    .group:hover
    {
        background-color: rgb(236, 236, 236);
    }
    .group_profile{
        position: relative;
        width: 40px;
        height: 40px;
    }
    .g_pro_1,
    .g_pro_2,
    .g_pro_3{
        width: 25px !important;
        height: 25px;
        position: absolute;
        border-radius: 50%;
        border: 1px solid white;
    }
    .g_pro_1{
        top: 0px;
        left: 0px;
    }
    .g_pro_2{
        top: 5px;
        right: 0px;
    }
    .g_pro_3{
        bottom: 0px;
        left: 5px;
    }
    .userList,.groupList{
        height: 50%;
        overflow: hidden;
        padding-left: 10px;
    }
    .userListUl{
        overflow-y: scroll;
        height: 76%;
    }
    #backHome{
        display: none;
    }#default_chat_view{
        border-left: 1px solid #ccced0;
    }
    @media(max-width:800px){
        .user_chats{
            position: fixed;
            top: 0px;
            left: 0px;
            background-color: #fff;
            height: 100svh;
            z-index: 2;
            padding-right: 0px;
        }
        .messageForm{
            position: absolute;
            bottom: 0px;
            width: 100%;
        }
        #backHome{
            display: flex;
            font-size: 25px;
            padding: 0px 12px 0px 5px;
            cursor: pointer;
        }
    }
</style>