<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\AuthorizeResourceTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentController extends Controller
{
    use ResponseTrait, AuthorizeResourceTrait;

    /**
     * Display a listing of comments for a ticket.
     */
    public function index(Request $request, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $this->authorizeAccess($ticket);

            $comments = $ticket->comments()->with('user')->paginate(10);
            return $this->successResponse($comments);

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Ticket not found.', $e, 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch comments.', $e);
        }
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $this->authorizeAccess($ticket);

            $validatedData = $this->validateCommentRequest($request);
            $comment = $this->createComment($validatedData, $ticket, $request->user());

            return $this->successResponse($comment, 201);

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Ticket not found.', $e, 404);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create comment.', $e);
        }
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy($ticketId, $commentId)
    {
        try {
            $comment = Comment::where('ticket_id', $ticketId)->findOrFail($commentId);
            $this->authorizeAccess($comment);
            $comment->delete();
            return $this->successResponse(['message' => 'Comment deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Comment not found.', $e, 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete comment.', $e);
        }
    }

    /**
     * Create a new comment.
     */
    private function createComment(array $data, Ticket $ticket, User $user)
    {
        return $ticket->comments()->create(array_merge($data, [
            'user_id' => $user->id,
        ]));
    }

    /**
     * Validate the comment request.
     */
    private function validateCommentRequest(Request $request)
    {
        return $request->validate([
            'content' => 'required|string|max:1000',
        ]);
    }
}