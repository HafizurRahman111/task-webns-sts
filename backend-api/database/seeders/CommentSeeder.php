<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = Ticket::with('user')->get();

        $comments = [
            "We are currently looking into this issue.",
            "Can you provide more details about the problem?",
            "Your request has been forwarded to the support team.",
            "We appreciate your patience while we resolve this.",
            "A fix has been deployed, please check again.",
            "Could you please attach a screenshot of the issue?",
            "We are experiencing some delays but will update soon.",
            "Your refund request is under review.",
            "The technical team is working on the resolution.",
            "This issue has been marked as resolved.",
            "Let us know if you need any further assistance.",
            "We are escalating this issue to higher priority.",
            "A solution has been provided in the latest update.",
            "Thank you for bringing this to our attention.",
            "The feature request has been noted for future improvements."
        ];

        $totalComments = rand(12, 15);

        for ($i = 0; $i < $totalComments; $i++) {
            $ticket = $tickets->random();

            if ($ticket->user) {
                Comment::create([
                    'content' => $comments[array_rand($comments)],
                    'ticket_id' => $ticket->id,
                    'user_id' => $ticket->user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
