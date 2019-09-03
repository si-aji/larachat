@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">{{-- Chat Container --}}
                <div class="card-header">Laravel Chatbox</div>
                <div class="card-body">
                    <div class="row d-flex">
                        <div class="col-12 col-lg-4">
                            @include('content.chat.partials.user')
                        </div>
                        <div class="col-12 col-lg-8">
                            @include('content.chat.partials.chatbox')
                        </div>
                    </div>
                </div>
            </div>{{-- Chat Container --}}
        </div>
    </div>
</div>
@endsection

@section('js_plugins')
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="//js.pusher.com/3.1/pusher.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
@endsection

@section('js_inline')
<script>
    $(document).ready(function(){
        // Chat Dikirim
        $("#chatbox_form").submit(function(e){
            e.preventDefault();
            // console.log("Chatbox is sent");
            if($("#chat").val() != ""){
                $.ajax({
                    url: "{{ route('sent_message') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(result){
                        $("#chat").val("").focus();

                        // Hide Old Message
                        let element = $(".msg").length;
                        if(element > 4){
                            $(".msg").first().slideUp(function(){
                                $(this).remove();
                            });
                        }

                        $("#no_message").slideUp();

                        $('<div class="nav-link p-0 msg msg-from" style="display:none"><button class="msg-btn" onclick="deleteFunction(this)" title="Delete" data-id="'+result['id']+'">x</button><li class="list-group-item">'+result['message']+'</li><small class="text-muted" data-message_id="'+result['id']+'">Belum Dibaca</small></div>').appendTo("#chat_container").slideDown('slow', 'swing');
                    }
                });
            }
        });
    });

    // Buka Chat
    function startChat(event){
        // console.log(event);
        closeChat();

        // Set Variable
        var id = $(event).data('user_id');
        var name = $(event).data('user_name');
        // Add Active Class to Active User Chatbox
        $(event).find('.list-group-item').addClass('active');
        // Remove Disabled from Close Chat
        $("#chatbox_close").prop('disabled', false);

        // Fill Necessary Field
        $("#target_user").val(id);
        $("#chatbox_title").text('Chatbox with '+name);
        $("#btn_submit").prop('disabled', true);
        $("#chat").prop('disabled', true);

        // Ambil data message yang sesuai dengan user terkait
        $.ajax({
            url: "{{ route('fetch_message') }}",
            method: "POST",
            data: {'user_id': id, '_token': "{{ csrf_token() }}"},
            success: function(result){
                console.log(result);
                setSeen(id, "{{ Auth::user()->id }}");// Set Seen

                $("#btn_submit").prop('disabled', false); // Un-disabled message subumit
                $("#chat").prop('disabled', false); // Un-disabled message input
                var delete_button = message = status_baca = "";

                if(result.length == 0){
                    // Tampilkan pesan No Message jika catatan chat kosong
                    $("#no_message").slideDown();
                } else {
                    $("#no_message").slideUp();
                }

                $.each(result, function(index, value){
                    // console.log(value);
                    // Check Status Hapus
                    if(value["is_deleted"]){
                        message = 'This message was deleted';
                    } else {
                        message = value['message'];
                    }

                    if(value['from_user'] == "{{ Auth::user()->id }}"){
                        var msg_class = 'msg-from';
                        if(!value['is_deleted']){ // Tidak Dihapus
                            // Check Status Baca
                            if(value["is_seen"]){
                                msg_status = "Telah dibaca pada "+value['updated_at'];
                            } else {
                                msg_status = "Belum dibaca";
                                delete_button = '<button class="msg-btn" onclick="deleteFunction(this)" title="Delete" data-id="'+value['id']+'">x</button>';
                            }
                            status_baca = '<small class="text-muted" data-message_id="'+value['id']+'">'+msg_status+'</small>';
                        }
                    } else {
                        var msg_class = 'msg-to';
                        delete_button = status_baca = '';
                    }

                    $('<div class="nav-link p-0 msg '+msg_class+'" data-message="'+value['id']+'" style="display:none">'+delete_button+'<li class="list-group-item">'+message+'</li>'+status_baca+'</div>').appendTo("#chat_container").slideDown('slow', 'swing');
                });
            }
        })
    }
    // Tutup Chat
    function closeChat(){
        // console.log("Chatbox is closed");
        $("#no_message").slideUp();
        
        $(".msg").slideUp('slow', 'swing', function(){
            setTimeout(function(){
                $(this).remove();
            });
        });

        $("#user_"+$("#target_user").val()).find('.list-group-item').removeClass('active');

        // Fill Necessary Field
        $("#target_user").val('');
        $("#chatbox_title").text('Chatbox');
        $("#btn_submit").prop('disabled', true);
        $("#chat").prop('disabled', true);

        // Add Disabled from Close Chat
        $("#chatbox_close").prop('disabled', false);
    }
    // Unsend Message
    function deleteFunction(input){
        // console.log("Msg Btn Clicked");

        var nilai = $(input).attr('data-id');
        // console.log("Clicked on data-id : "+nilai);

        $.ajax({
            url: "{{ url('/delete') }}/"+nilai,
            method: "POST",
            data: {'_method': "DELETE", '_token': "{{ csrf_token() }}"},
            success: function(result){
                $('[data-id="'+result.id+'"]').closest(".msg").slideUp(function(){
                    $('[data-id="'+result.id+'"]').closest(".msg").find('.list-group-item').text(result.message);
                    
                    $('[data-id="'+result.id+'"]').remove();
                }).slideDown();
            }
        });
    }
    // Set Seen
    function setSeen(id, target){
        $.ajax({
            url: "{{ route('seen_message') }}",
            method: "POST",
            data: {'from_user': id, 'to_user': target, '_token': "{{ csrf_token() }}"},
            success: function(result){
                // console.log("data user badge : "+result.id);
                // Remove Unseen Badge
                $("#user_"+result.id).data("message_unseen", '0');     
                $(".badge[data-badge_user='"+result.id+"']").remove();
            }
        });
    }

    // Pusher Init
    // Pusher.logToConsole = true;
    var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
        cluster: 'ap1',
        encrypted: true
    });

    // Pusher - User Event
    var channel = pusher.subscribe('showuser-list');
    channel.bind('showuser-list', function(data) {
        // console.log(data);

        var unseen_data = data.unseen;
        var unseen_count = 0;
        unseen_data.forEach(function(value, key){
            if(value['to_user'] == "{{ Auth::user()->id }}"){
                unseen_count++;
            }
        });

        var unseen_badge = "";
        if(unseen_count > 0){
            // console.log("Add Badge");
            unseen_badge = '<span class="badge badge-dark" data-badge_user="'+data.id+'">'+unseen_count+'</span>';
        } else {
            // console.log("Badge Empty");
            unseen_badge = "";
        }

        // console.log("Unseen Count : "+unseen_count);
        // console.log("Unseen Badge : "+unseen_badge);

        if(data.trigger == 'login'){
            // User Login, pindahkan dari Offline ke Online
            if(data.id != "{{ Auth::user()->id }}"){
                $("#user_"+data.id).slideUp(function(){
                    setTimeout(function(){
                        $(this).remove();
                    });
                });
                $('<a href="javascript:void(0)" class="nav-link p-0" id="user_'+data.id+'" style="display:none" data-user_id="'+data.id+'" data-user_name="'+data.name+'" data-message_unseen="'+unseen_count+'" onclick="startChat(this)"><li class="list-group-item">'+data.name+' '+unseen_badge+'</li></a>').appendTo("#user_online").slideDown('slow', 'swing');
            }
        } else {
            // User Logout, pindahkan dari Online ke Offline
            if(data.id != "{{ Auth::user()->id }}"){
                $("#user_"+data.id).slideUp(function(){
                    setTimeout(function(){
                        $(this).remove();
                    });
                });
                $('<a href="javascript:void(0)" class="nav-link p-0" id="user_'+data.id+'" style="display:none" data-user_id="'+data.id+'" data-user_name="'+data.name+'" data-message_unseen="'+unseen_count+'" onclick="startChat(this)"><li class="list-group-item">'+data.name+' '+unseen_badge+'</li></a>').appendTo("#user_offline").slideDown('slow', 'swing');
            }
        }
    });

    // Pusher - New Message
    var msg_channel = pusher.subscribe('show-message');
    msg_channel.bind('show-message', function(data) {
        // console.log(data);
        // console.log("From : "+data.from);
        // console.log("To : "+data.to);
        // console.log("Auth : {{ Auth::user()->id }}");

        // Hide Old Message
        let element = $(".msg").length;
        if(element > 4){
            $(".msg").first().slideUp(function(){
                $(this).remove();
            });
        }

        var target_user = $("#target_user").val();
        if(data.to == "{{ Auth::user()->id }}" && data.from == target_user){
            // Set Message sebagai seen
            setSeen(data.from, data.to);

            if(data.status == 'add'){
                // console.log("Pesan Baru");
                $('<div class="nav-link p-0 msg msg-to" data-message="'+data.id+'" style="display:none"><li class="list-group-item">'+data.message+'</li></div>').appendTo("#chat_container").slideDown('slow', 'swing');
            } else {
                // console.log("Pesan dihapus");
                $('[data-message="'+data.id+'"]').slideUp(function(){
                    $('[data-message="'+data.id+'"]').find('.list-group-item').text(data.message);
                }).slideDown();
            }
        } else if(data.to == "{{ Auth::user()->id }}"){
            var unseen = $("#user_"+data.from).data('message_unseen');
            unseen++;

            // console.log("New Message Unseen : "+unseen);
            // console.log("New Message From : "+data.from);

            if(unseen > 1){
                // Increase Unseen badge
                $("#user_"+data.from).find(".badge").text(unseen);
            } else {
                $("#user_"+data.from).find(".list-group-item").append('<span class="badge badge-dark" data-badge_user="'+data.from+'">'+unseen+'</span>');
            }

            $("#user_"+data.from).data("message_unseen", unseen);
            // console.log("Un Open, unseen : "+unseen);
        }
    });

    // Pusher - Message Seen
    var seen_channel = pusher.subscribe('seen-message');
    seen_channel.bind('seen-message', function(data) {
        // console.log("Seen Data : ",data);
        // console.log("Seen Message ID : "+data.id);

        var target_user = $("#target_user").val();
        if(data.from == "{{ Auth::user()->id }}" && data.to == target_user);
        $("[data-message_id='"+data.id+"']").text(data.message);
        $(".msg-btn[data-id='"+data.id+"']").remove();
    });
</script>
@endsection