<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $online_user = User::where([
            ['id', '!=', Auth::user()->id],
            ['is_online', true]
        ])->get()->map(function($data, $key){
            $message = Message::where([
                ['from_user', $data->id],
                ['to_user', Auth::user()->id],
                ['is_seen', false]
            ])->get();

            $data->unseen = $message->count();
            return $data;
        });
        $offline_user = User::where([
            ['id', '!=', Auth::user()->id],
            ['is_online', false]
        ])->get()->map(function($data, $key){
            $message = Message::where([
                ['from_user', $data->id],
                ['to_user', Auth::user()->id],
                ['is_seen', false]
            ])->get();

            $data->unseen = $message->count();
            return $data;
        });
        return view('content.chat.index', compact('online_user', 'offline_user'));
    }
}
