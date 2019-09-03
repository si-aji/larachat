<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Message;

use App\Events\ShowNewMessage;
use App\Events\SeenMessage;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function fetchMessage(Request $request)
    {
        // Fetch Message based on Related User
        $message = Message::where(function($query) use ($request){
            $query->where('from_user', Auth::user()->id)->where('to_user', $request->user_id);
        })->orWhere(function($query) use ($request){
            $query->where('from_user', $request->user_id)->where('to_user', Auth::user()->id);
        })->latest('created_at')->limit(5)->get()->toArray();

        return response()->json(array_reverse($message));
    }

    public function sentMessage(Request $request)
    {
        // Store new Message
        $store = Message::create([
            'from_user' => Auth::user()->id,
            'to_user' => $request->target_user,
            'message' => $request->chat
        ]);

        $data = [
            'id' => $store->id,
            'to' => $request->target_user,
            'from' => Auth::user()->id,
            'message' => $request->chat,
            'status' => 'add'
        ];
        event(new ShowNewMessage($data));

        return response()->json($store);
    }

    public function seenMessage(Request $request){
        // Set Related Message to Seen
        $unseen = Message::where([
            ['from_user', $request->from_user],
            ['to_user', $request->to_user],
            ['is_seen', false]
        ])->get();

        $data_from = "";
        foreach($unseen as $data){
            $data->is_seen = true;
            $data->save();

            $data_from = $request->from_user;
            $event = [
                'id' => $data->id,
                'message' => 'Telah dibaca pada '.$data->updated_at,
                'to' => $request->to_user,
                'from' => $request->from_user,
            ];
            event(new SeenMessage($event));
        }

        return response()->json([
            'id' => $data_from
        ]);
    }

    public function deleteMessage(Request $request, $id){
        // Unsend Related Message
        $message = Message::find($id);
        $message->is_deleted = true;
        $message->save();

        $message->message = 'This message was deleted';
        $data = [
            'id' => $message->id,
            'to' => $message->to_user,
            'from' => $message->from_user,
            'message' => 'This message was deleted',
            'status' => 'remove'
        ];
        event(new ShowNewMessage($data));

        return response()->json($message);
    }
}
