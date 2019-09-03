<div class="card">{{-- Chat Box --}}
    <div class="card-header">
        <span id="chatbox_title">Chatbox</span>
        <button id="chatbox_close" class="float-right btn btn-sm btn-danger" onclick="closeChat()" disabled>Close</button>
    </div>

    <div class="card-body">
        <div id="chat_container" class="list-group mt-3">
            <div class="alert alert-dark" role="alert" style="display:none;" id="no_message">
                No Message to Display
            </div>
        </div>

        <form type="form" id="chatbox_form">
            {{ csrf_field() }}

            <div class="form-group mt-3">
                <div class="input-group">
                    <input type="hidden" name="target_user" id="target_user" value="" readonly>
                    <input type="text" name="chat" id="chat" class="form-control" placeholder="Type your text here..." disabled>

                    <div class="input-group-append">
                        <button class="btn btn-primary" id="btn_submit" type="submit" disabled>Send</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>{{-- /.Chat Box --}}