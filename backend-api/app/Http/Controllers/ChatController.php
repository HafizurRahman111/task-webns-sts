<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\Chat;
use App\Models\Ticket;
use App\Traits\AuthorizeResourceTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ChatController extends Controller
{
    use AuthorizeResourceTrait, ResponseTrait;

    /**
     * Display a listing of all chats for a specific ticket.
     */
    public function index(Request $request, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $this->authorizeAccess($ticket);

            $page = $request->get('page', 1);

            $chats = $ticket->chats()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(5, ['*'], 'page', $page);

            return $this->successResponse($chats);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Ticket not found.', $e, 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch chats.', $e);

        }
    }

    public function allChats(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 10);

            $query = Ticket::with([
                'user:id,name',
                'latestChat.user:id,name',
            ])
                ->withCount([
                    'chats',
                    'chats as unread_messages_count' => function ($query) use ($user) {
                        $query->where('status', 'unread')
                            ->where('user_id', '!=', $user->id);
                    }
                ]);

            if (!$user->isAdmin()) {
                $query->where('user_id', $user->id);
            }

            if ($request->has('search')) {
                $query->where('subject', 'like', '%' . $request->search . '%');
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $tickets = $query->orderByDesc(
                Chat::select('created_at')
                    ->whereColumn('chats.ticket_id', 'tickets.id')
                    ->orderByDesc('created_at')
                    ->limit(1)
            )->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $tickets->map(function ($ticket) use ($user) {
                    return [
                        'ticket' => [
                            'id' => $ticket->id,
                            'subject' => $ticket->subject,
                            'status' => $ticket->status,
                            'priority' => $ticket->priority,
                            'created_at' => $ticket->created_at,
                            'user' => $ticket->user,
                        ],
                        'last_message' => $ticket->latestChat ? [
                            'id' => $ticket->latestChat->id,
                            'message' => $ticket->latestChat->message,
                            'created_at' => $ticket->latestChat->created_at,
                            'user' => $ticket->latestChat->user,
                            'status' => !$ticket->latestChat->is_read &&
                                $ticket->latestChat->user_id !== $user->id,
                        ] : null,
                        'unread_count' => $ticket->unread_messages_count,
                        'total_messages' => $ticket->chats_count,
                    ];
                }),
                'meta' => [
                    'current_page' => $tickets->currentPage(),
                    'per_page' => $tickets->perPage(),
                    'total' => $tickets->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch chats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created chat in storage.
     */
    public function store(Request $request, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $this->authorizeAccess($ticket);

            $validatedData = $request->validate([
                'message' => 'required|string|max:250',
            ]);

            $chat = $ticket->chats()->create([
                'user_id' => Auth::id(),
                'message' => $validatedData['message'],
            ]);

            broadcast(new NewChatMessage($chat))->toOthers();

            return response()->json($chat, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Ticket not found.'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create chat.'], 500);
        }
    }


    /**
     * Delete a chat.
     */
    public function destroy($ticketId, $chatId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $this->authorizeAccess($ticket);

            $chat = $ticket->chats()->findOrFail($chatId);
            $chat->delete();

            return $this->successResponse(['message' => 'Chat deleted successfully.']);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Chat not found.', $e, 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete chat.', $e);
        }
    }

    public function markAllAsRead($ticketId)
    {
        try {
            $chats = Chat::where('ticket_id', $ticketId)->get();

            if ($chats->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No chats found for this ticket.',
                ], 404);
            }

            foreach ($chats as $chat) {
                $chat->status = 'read';
                $chat->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'All chats marked as read.',
                'data' => $chats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all chats as read.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}