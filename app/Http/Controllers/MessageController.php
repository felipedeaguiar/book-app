<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:500',
        ]);

        $message = new \App\Models\Message();
        $message->sender_id = Auth::user()->id;
        $message->receiver_id = $request->receiver_id;
        $message->message = $request->message;

        $message->save();

        return response()->json(['success' => true, 'message' => 'Message sent successfully.']);
    }

    public function getMessageFromReceiver($receiverId)
    {
        $messages = \App\Models\Message::where('receiver_id', $receiverId)
            ->where('sender_id', Auth::user()->id)
            ->orWhere(function ($query) use ($receiverId) {
                $query->where('sender_id', $receiverId)
                      ->where('receiver_id', Auth::user()->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $pagination = [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
        ];

        $messages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                'message' => $message->message,
                'right' => $message->sender_id == auth()->id(),
            ];
        });

        return response()->json(['success' => true, 'data' => $messages, 'pagination' => $pagination]);
    }

    public function getMyMessages()
    {
        $authId = auth()->id();

        $contactIds = Message::selectRaw("
    CASE
        WHEN sender_id = ? THEN receiver_id
        ELSE sender_id
    END as user_id", [$authId])
            ->where('sender_id', $authId)
            ->orWhere('receiver_id', $authId)
            ->groupBy('user_id')
            ->orderByRaw('MAX(created_at) DESC')
            ->pluck('user_id');

// 2. Para cada usuário, pegar a última mensagem
        $conversations = [];

        foreach ($contactIds as $contactId) {
            $lastMessage = Message::where(function ($q) use ($authId, $contactId) {
                $q->where('sender_id', $authId)->where('receiver_id', $contactId);
            })->orWhere(function ($q) use ($authId, $contactId) {
                $q->where('sender_id', $contactId)->where('receiver_id', $authId);
            })
                ->orderByDesc('created_at')
                ->first();

            $user = User::find($contactId);

            $conversations[] = [
                'user' => $user,
                'last_message' => $lastMessage
            ];
        }

        return $conversations;
    }
}
