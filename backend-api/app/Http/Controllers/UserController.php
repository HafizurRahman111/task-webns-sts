<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // Get all users (admin only)
    public function index()
    {
        try {
            $users = User::latest()->paginate(5);

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Throwable $e) {
            Log::error('Error fetching users: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users. Please try again later.'
            ], 500);
        }
    }

    // Delete a user (admin only)
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete user'], 500);
        }
    }

    public function stats()
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

        $ticketQuery = Ticket::query();

        if (!$isAdmin) {
            $ticketQuery->where('user_id', $user->id);
        }

        $ticketCounts = $ticketQuery->clone()->selectRaw("
            COUNT(*) as totalTickets,
            COUNT(CASE WHEN status = 'open' THEN 1 END) as openTickets,
            COUNT(CASE WHEN status = 'in-progress' THEN 1 END) as inProgressTickets,
            COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolvedTickets,
            COUNT(CASE WHEN status = 'closed' THEN 1 END) as closedTickets
        ")->first();

        $totalUsers = $isAdmin ? User::count() : null;

        $recentTickets = $ticketQuery->latest()->take(5)->get();

        return response()->json([
            'stats' => [
                'totalTickets' => $ticketCounts->totalTickets,
                'openTickets' => $ticketCounts->openTickets,
                'inProgressTickets' => $ticketCounts->inProgressTickets,
                'resolvedTickets' => $ticketCounts->resolvedTickets,
                'closedTickets' => $ticketCounts->closedTickets,
                'totalUsers' => $totalUsers,
            ],
            'recentTickets' => $recentTickets,
        ]);
    }
}
