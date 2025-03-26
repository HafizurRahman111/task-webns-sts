<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Traits\AuthorizeResourceTrait;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends Controller
{
    use ResponseTrait, AuthorizeResourceTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $tickets = $this->getUserTickets($request);
            return $this->successResponse($tickets);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch tickets.', $e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Check if the user is not an admin before checking for open tickets
            if ($request->user()->role !== 'admin') {
                $this->checkOpenTicket($request->user()->id);
            }
            $validatedData = $this->validateTicketRequest($request);
            $ticket = $this->createTicket($validatedData, $request->user()->id);
            return $this->successResponse($ticket, 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $ticket = Ticket::with([
                'comments' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'comments.user'
            ])->findOrFail($id);
            $this->authorizeAccess($ticket);
            return $this->successResponse($ticket);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Ticket not found.', $e, 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch ticket.', $e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $this->authorizeAccess($ticket);
            $validatedData = $this->validateTicketRequest($request);

            // Handle file upload if present
            if ($attachment = $this->handleAttachmentUpload($request)) {
                $validatedData['attachment'] = $attachment;
            }

            $ticket->update($validatedData);

            return $this->successResponse($ticket);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Ticket not found.', $e, 404);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update ticket.', $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $this->authorizeAccess($ticket);
            $this->deleteTicketAttachment($ticket);
            $ticket->delete();
            return $this->successResponse(['message' => 'Ticket deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Ticket not found.', $e, 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete ticket.', $e);
        }
    }

    /**
     * Update the status of a ticket.
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $this->authorizeAccess($ticket);
            $this->validateStatus($request);
            $ticket->update(['status' => $request->status]);
            return $this->successResponse($ticket, 200, 'Ticket status updated successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Ticket not found.', $e, 404);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update ticket status.', $e);
        }
    }

    /**
     * Get tickets for the authenticated user.
     */
    private function getUserTickets(Request $request)
    {
        $query = Ticket::query();
        $query->with('user:id,name');

        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        $query->latest('created_at');

        return $query->paginate(5);
    }

    /**
     * Check if the user already has an open ticket.
     */
    private function checkOpenTicket($userId)
    {
        if (Ticket::where('user_id', $userId)->where('status', 'open')->exists()) {
            throw new \Exception('You already have an open ticket. Please resolve it before creating a new one.');
        }
    }

    /**
     * Create a new ticket.
     */
    private function createTicket(array $data, $userId)
    {
        return Ticket::create(array_merge($data, [
            'attachment' => $this->handleAttachmentUpload(request()),
            'user_id' => $userId,
            'status' => 'open',
        ]));
    }

    /**
     * Delete the ticket's attachment.
     */
    private function deleteTicketAttachment(Ticket $ticket)
    {
        if ($ticket->attachment) {
            Storage::disk('public')->delete($ticket->attachment);
        }
    }

    /**
     * Validate the status update request.
     */
    private function validateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:open,in-progress,resolved,closed',
        ]);
    }

    /**
     * Validate the ticket request.
     */
    private function validateTicketRequest(Request $request)
    {
        return $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category' => 'required|string|in:technical,billing,general',
            'priority' => 'required|string|in:low,medium,high',
            'attachment' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    }

    /**
     * Handle file upload for attachments.
     */
    private function handleAttachmentUpload(Request $request)
    {
        if ($request->hasFile('attachment')) {
            // Define the directory path relative to the public directory
            $directoryPath = 'assets/files/tickets/'; // No leading slash
            $file = $request->file('attachment');

            // Generate a unique file name
            $fileName = 'ticket_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Move the file to the specified directory
            $file->move(public_path($directoryPath), $fileName);

            // Return the full URL of the uploaded file
            return asset($directoryPath . $fileName); // Use asset() to generate the full URL
        }

        return null; // Return null if no file was uploaded
    }
}