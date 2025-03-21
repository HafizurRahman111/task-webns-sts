<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::pluck('id')->toArray();

        $tickets = [
            [
                'subject' => 'Website not loading',
                'description' => 'I am unable to access the website. It shows an error.',
                'category' => 'technical',
                'priority' => 'high',
                'status' => 'open',
                'attachment' => null,
                'user_id' => $users[array_rand($users)],
            ],
            [
                'subject' => 'Billing issue - Duplicate Charge',
                'description' => 'I was charged twice for my last invoice. Please check.',
                'category' => 'billing',
                'priority' => 'medium',
                'status' => 'in-progress',
                'attachment' => 'invoices/duplicate_invoice.pdf',
                'user_id' => $users[array_rand($users)],
            ],
            [
                'subject' => 'Change password request',
                'description' => 'I need to reset my account password.',
                'category' => 'general',
                'priority' => 'low',
                'status' => 'resolved',
                'attachment' => null,
                'user_id' => $users[array_rand($users)],
            ],
            [
                'subject' => 'Email not receiving',
                'description' => 'I am not receiving any emails from the system.',
                'category' => 'technical',
                'priority' => 'high',
                'status' => 'open',
                'attachment' => null,
                'user_id' => $users[array_rand($users)],
            ],
            [
                'subject' => 'Refund Request',
                'description' => 'I need a refund for my last transaction due to incorrect billing.',
                'category' => 'billing',
                'priority' => 'medium',
                'status' => 'in-progress',
                'attachment' => 'receipts/refund_request.pdf',
                'user_id' => $users[array_rand($users)],
            ],
            [
                'subject' => 'Feature Request: Dark Mode',
                'description' => 'It would be great if the app had a dark mode feature.',
                'category' => 'general',
                'priority' => 'low',
                'status' => 'closed',
                'attachment' => null,
                'user_id' => $users[array_rand($users)],
            ],
        ];

        foreach ($tickets as $ticket) {
            Ticket::create($ticket);
        }
    }
}
