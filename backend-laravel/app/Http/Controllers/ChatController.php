<?php

namespace App\Http\Controllers;

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
            $this->authorizeAccess($ticket); // Ensure the user has access to the ticket
            $chats = $ticket->chats()->with('user')->paginate(10); // Fetch chats with user info and paginate
            return $this->successResponse($chats);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Ticket not found.', $e, 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch chats.', $e);
        }
    }


    /**
     * Store a newly created chat in storage.
     */
    public function store(Request $request, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $this->authorizeAccess($ticket); // Ensure the user has access to the ticket
            $validatedData = $this->validateChatRequest($request);

            $chat = $ticket->chats()->create([
                'user_id' => Auth::id(),
                'message' => $validatedData['message'],

            ]);

            return $this->successResponse($chat, 201);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Ticket not found.', $e, 404);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create chat.', $e);
        }
    }

    /**
     * Delete a chat.
     */
    public function destroy($ticketId, $chatId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $this->authorizeAccess($ticket); // Ensure the user has access to the ticket
            $chat = $ticket->chats()->findOrFail($chatId); // Ensure the chat belongs to the ticket

            // Delete the chat
            $chat->delete();

            return $this->successResponse(['message' => 'Chat deleted successfully.']);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Chat not found.', $e, 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete chat.', $e);
        }
    }

    // Helper Methods

    /**
     * Validate the chat request.
     */
    private function validateChatRequest(Request $request)
    {
        return $request->validate([
            'message' => 'required|string|max:250', // Validate 'message' field
        ]);
    }
}