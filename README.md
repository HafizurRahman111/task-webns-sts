# Customer Support Ticketing System

A modern, real-time support ticketing system built with Laravel and Vue.js

## Table of Contents
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Installation](#-installation)
- [Login Credentials](#-credentials)
- [API Documentation](#-api-documentation)
- [Real-Time Chat](#-real-time-chat)
- [Security](#-security)

## Features

### Authentication
- ✅ User Registration & Login
- ✅ Role-Based Access Control (Admin/Customer)
- ✅ Token-Based Auth (Laravel Sanctum)

### Ticket Management
- Create, View, Update, Delete Tickets
- Priority & Category Filtering
- File Attachments (Images etc.)

### Real-Time Chat
- Instant Messaging Between Users & Admins
- Unread Message Indicators
- Message History Persistence

### Admin Dashboard
- View & Manage All Tickets
- Update Ticket Status (Open → In Progress → Resolved/Closed)
- Analytics & Reporting

## Tech Stack

### Backend
- PHP 8.1+
- Laravel 12.x (MVC Framework)
- MySQL 8.0+ (Database)
- Redis (Queue & Cache)
- Pusher (Real-Time Events)

### Frontend
- Vue.js (Reactive UI)
- Tailwind CSS (Styling)
- Axios (HTTP Requests)
- Laravel Pusher (WebSocket Integration)

## Installation

### Prerequisites
- PHP 8.1+
- Composer 2.0+
- Node.js 16+
- MySQL 8.0+
- Redis Server

### Setup Steps
#### Clone the repository
```bash
git clone repo_url
cd backend-api
```

#### Install dependencies
```bash
composer install
npm install
```

#### Configure environment
```bash
cp .env.example .env
```
Edit `.env` with your credentials:
```ini
DB_DATABASE=support_system
DB_USERNAME=root
DB_PASSWORD=

PUSHER_APP_ID=your_pusher_id
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
PUSHER_APP_CLUSTER=mt1
```

#### Generate app key
```bash
php artisan key:generate
```

#### Run migrations & seeders
```bash
php artisan migrate --seed
```

#### Compile frontend assets
```bash
npm run dev
```

#### Start the server
```bash
php artisan serve
```

## Login Credentials

### Admin Account
- Email: admin@example.com
- Password: pass1234

### Regular User-1
- Email: user@example.com
- Password: pass1234


## 📡 API Documentation

### Authentication
| Method | Endpoint | Description | Controller@Method |
|--------|---------|-------------|------------------|
| POST | `/api/login` | User login | `AuthController@login` |
| POST | `/api/logout` | User logout | `AuthController@logout` |
| POST | `/api/refresh-token` | Refresh authentication token | `AuthController@refresh` |
| POST | `/api/register` | Register new user | `AuthController@register` |
| GET | `/api/user` | Get current authenticated user | `AuthController@currentUser` |
| GET | `/sanctum/csrf-cookie` | Get CSRF cookie | `CsrfCookieController@show` |
| GET | `/api/csrf-token` | Get CSRF token | `-` |

### Users (Admin)
| Method | Endpoint | Description | Controller@Method |
|--------|---------|-------------|------------------|
| GET | `/api/admin/users` | List all users | `UserController@index` |
| DELETE | `/api/admin/users/{user}` | Delete a user | `UserController@destroy` |
| GET | `/api/stats` | Get user statistics | `UserController@stats` |

### Tickets
| Method | Endpoint | Description | Controller@Method |
|--------|---------|-------------|------------------|
| GET | `/api/tickets` | List all tickets | `TicketController@index` |
| POST | `/api/tickets` | Create new ticket | `TicketController@store` |
| GET | `/api/tickets/{ticket}` | Show specific ticket | `TicketController@show` |
| PUT | `/api/tickets/{ticket}` | Update ticket | `TicketController@update` |
| DELETE | `/api/tickets/{ticket}` | Delete ticket | `TicketController@destroy` |

### Chats
| Method | Endpoint | Description | Controller@Method |
|--------|---------|-------------|------------------|
| GET | `/api/chats` | Get all chats | `ChatController@allChats` |
| GET | `/api/chats/mark-read-all/{ticketId}` | Mark all chats as read | `ChatController@markAllAsRead` |
| GET | `/api/tickets/{ticket}/chats` | Get chats for a ticket | `ChatController@index` |
| POST | `/api/tickets/{ticket}/chats` | Create new chat for a ticket | `ChatController@store` |
| DELETE | `/api/tickets/{ticket}/chats/{chat}` | Delete a chat | `ChatController@destroy` |

### Comments
| Method | Endpoint | Description | Controller@Method |
|--------|---------|-------------|------------------|
| GET | `/api/tickets/{ticket}/comments` | Get comments for a ticket | `CommentController@index` |
| POST | `/api/tickets/{ticket}/comments` | Create new comment | `CommentController@store` |
| DELETE | `/api/tickets/{ticket}/comments/{comment}` | Delete a comment | `CommentController@destroy` |

### Other
| Method | Endpoint | Description | Controller@Method |
|--------|---------|-------------|------------------|
| GET | `/` | Application root | `-` |
| GET | `/up` | Application status check | `-` |
| GET | `/storage/{path}` | Access storage files | `storage.local` |
| GET/POST | `/broadcasting/auth` | Broadcast authentication | `BroadcastController@authenticate` |


## Real-Time Chat

### How It Works
#### Pusher Integration
- Broadcasts new messages instantly.
- Uses private channels for security.

#### Laravel Events
```php
// app/Events/NewChatMessage.php
class NewChatMessage implements ShouldBroadcast {
    public function broadcastOn() {
        return new PrivateChannel('ticket.'.$this->chat->ticket_id);
    }
}
```

#### Frontend Subscription
```javascript
Echo.private(`ticket.${ticketId}`)
    .listen('NewChatMessage', (message) => {
        this.messages.push(message);
    });
```

## Security
- ✅ CSRF Protection
- ✅ SQL Injection Prevention
- ✅ File Upload Validation
- ✅ Rate Limiting (API Throttling)

