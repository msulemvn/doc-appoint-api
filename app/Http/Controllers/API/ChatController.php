<?php

namespace App\Http\Controllers\API;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
    /**
     * Start a new chat or retrieve an existing one.
     *
     * @return JsonResponse
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'receiver_id' => [
                'required',
                'exists:users,id',
                Rule::notIn([Auth::id()]),
            ],
            'appointment_id' => 'nullable|exists:appointments,id',
        ]);

        $senderId = Auth::id();
        $receiverId = $request->receiver_id;
        $appointmentId = $request->appointment_id;

        $chat = Chat::where(function ($query) use ($senderId, $receiverId) {
            $query->where('user1_id', $senderId)
                ->where('user2_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('user1_id', $receiverId)
                ->where('user2_id', $senderId);
        })
            ->when($appointmentId, fn ($query) => $query->where('appointment_id', $appointmentId))
            ->first();

        if ($chat) {
            return response()->json($chat->load(['user1', 'user2', 'appointment']), 200);
        }

        $chat = Chat::create([
            'user1_id' => $senderId,
            'user2_id' => $receiverId,
            'appointment_id' => $appointmentId,
        ]);

        return response()->json($chat->load(['user1', 'user2', 'appointment']), 201);
    }

    /**
     * Display a listing of chats for the authenticated user.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $status = $request->query('status', 'active');

        $query = Chat::where(function ($query) use ($userId) {
            $query->where('user1_id', $userId)
                ->orWhere('user2_id', $userId);
        });

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $chats = $query
            ->with(['user1', 'user2', 'lastMessage.sender', 'appointment'])
            ->withCount([
                'messages as unread_count' => function ($query) use ($userId) {
                    $query->where('user_id', '!=', $userId)
                        ->whereNull('read_at');
                },
            ])
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($chats);
    }

    /**
     * Display the specified chat.
     *
     * @return JsonResponse
     */
    public function show(Chat $chat)
    {
        $this->authorize('view', $chat);

        return response()->json($chat->load(['user1', 'user2', 'lastMessage.sender', 'appointment']));
    }

    /**
     * Get messages for a specific chat.
     *
     * @return JsonResponse
     */
    public function getMessages(Chat $chat)
    {
        $this->authorize('view', $chat);

        $messages = $chat->messages()->with('sender')->latest()->paginate(20);

        return response()->json($messages);
    }

    /**
     * Send a new message in a chat.
     *
     * @return JsonResponse
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        $this->authorize('sendMessages', $chat);

        if ($chat->status === 'closed') {
            return response()->json([
                'message' => 'This chat has been closed. No new messages can be sent.',
            ], 403);
        }

        $request->validate([
            'content' => 'required_without:file|string|max:2000',
            'file' => 'nullable|file|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat-attachments', 'public');
        }

        $message = $chat->messages()->create([
            'user_id' => Auth::id(),
            'content' => $request->content ?? '',
            'file' => $filePath,
        ]);

        $chat->update(['last_message_id' => $message->id]);

        MessageSent::dispatch($message->load('sender'));

        return response()->json($message->load('sender'), 201);
    }

    /**
     * Mark a message as read.
     *
     * @return JsonResponse
     */
    public function markAsRead(Message $message)
    {
        $this->authorize('markAsRead', $message);

        if (is_null($message->read_at)) {
            $message->update(['read_at' => now()]);
        }

        return response()->json(['message' => 'Message marked as read.']);
    }

    /**
     * Update chat status (close/reopen chat).
     *
     * @return JsonResponse
     */
    public function updateStatus(Request $request, Chat $chat)
    {
        $this->authorize('view', $chat);

        $request->validate([
            'status' => 'required|in:active,closed',
        ]);

        $chat->update(['status' => $request->status]);

        return response()->json($chat->load(['user1', 'user2', 'lastMessage.sender', 'appointment']));
    }
}
