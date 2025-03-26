<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = Ticket::with('user')->get();

        $messages = [
            "Thank you for reaching out. We will get back to you soon.",
            "Can you please provide more details about this issue?",
            "We've received your request and are currently working on a resolution.",
            "Please try the suggested solution and let us know if it works.",
            "We have escalated your ticket to a senior technician.",
            "Your issue has been assigned to the support team.",
            "We are currently investigating this problem.",
            "This seems to be a known issue, and we're working on a fix.",
            "A new update has been deployed to address this issue.",
            "We need more information to proceed with troubleshooting.",
            "Thank you for your patience. We will update you soon.",
            "The issue has been resolved. Please confirm if it's working for you.",
            "This is currently under review by our billing department.",
            "Your feature request has been forwarded to the product team.",
            "We appreciate your feedback and will consider it in future updates."
        ];

        $statusOptions = ['read', 'unread'];

        $totalMessages = rand(12, 15);

        for ($i = 0; $i < $totalMessages; $i++) {
            $ticket = $tickets->random();

            if ($ticket->user) {
                $status = $statusOptions[array_rand($statusOptions)];

                Chat::create([
                    'message' => $messages[array_rand($messages)],
                    'ticket_id' => $ticket->id,
                    'user_id' => $ticket->user->id,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
