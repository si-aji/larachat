{{-- Offline --}}
<ul class="list-group" id="user_offline">
    <li class="list-group-item bg-secondary text-white">Offline</li>
    @foreach($offline_user as $user)
    <a href="javascript:void(0);" class="nav-link p-0" id="user_{{ $user->id }}" data-user_id="{{ $user->id }}" data-user_name="{{ $user->name }}" data-message_unseen="{{ $user->unseen }}" onclick="startChat(this)"><li class="list-group-item">{{ $user->name }} @if($user->unseen > 0) <span class="badge badge-dark" data-badge_user="{{ $user->id }}">{{ $user->unseen }}</span> @endif</li></a>
    @endforeach
</ul>

{{-- Online --}}
<ul class="list-group mt-2 mb-2 mb-lg-0" id="user_online">
    <li class="list-group-item bg-success text-white">Online</li>
    @foreach($online_user as $user)
    <a href="javascript:void(0);" class="nav-link p-0" id="user_{{ $user->id }}" data-user_id="{{ $user->id }}" data-user_name="{{ $user->name }}" data-message_unseen="{{ $user->unseen }}" onclick="startChat(this)"><li class="list-group-item">{{ $user->name }} @if($user->unseen > 0) <span class="badge badge-dark" data-badge_user="{{ $user->id }}">{{ $user->unseen }}</span> @endif</li></a>
    @endforeach
</ul>