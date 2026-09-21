<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    /**
     * Fetch conversation thread between authenticated user and another user.
     */
    public function messages(Request $request, User $user)
    {
        $authId = Auth::id();

        $messages = Message::where(function ($q) use ($authId, $user) {
            $q->where('sender_id', $authId)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($authId, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $authId);
        })
            ->with(['sender', 'receiver', 'product'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages from this user as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    /**
     * Send a message to a receiver.
     */
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:2000',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'product_id' => $request->product_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        $message->load(['sender', 'receiver', 'product']);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    /**
     * Vendor Chat Inbox View.
     */
    public function vendorIndex()
    {
        $authId = Auth::id();

        // Get list of users who have messaged this vendor or vice versa
        $conversations = Message::where('sender_id', $authId)
            ->orWhere('receiver_id', $authId)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->groupBy(function ($msg) use ($authId) {
                return $msg->sender_id === $authId ? $msg->receiver_id : $msg->sender_id;
            })
            ->map(function ($msgs, $otherUserId) {
                $lastMsg = $msgs->first();
                $otherUser = $lastMsg->sender_id === Auth::id() ? $lastMsg->receiver : $lastMsg->sender;

                return [
                    'user' => $otherUser,
                    'last_message' => $lastMsg->message,
                    'last_message_at' => $lastMsg->created_at->toIso8601String(),
                    'unread_count' => $msgs->where('receiver_id', Auth::id())->where('is_read', false)->count(),
                ];
            })->values();

        return Inertia::render('Vendor/Chat/Index', [
            'conversations' => $conversations,
        ]);
    }
}
