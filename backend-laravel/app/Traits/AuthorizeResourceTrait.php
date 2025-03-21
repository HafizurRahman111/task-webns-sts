<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable as User;

trait AuthorizeResourceTrait
{
    /**
     * Check if the authenticated user can access the given resource.
     *
     * @param  mixed  $resource  The resource being accessed.
     * @return bool
     * @throws AuthorizationException
     */
    public function authorizeAccess($resource): bool
    {
        /** @var User $user */
        $user = auth()->user();

        // Ensure a user is authenticated
        if (!$user) {
            throw new AuthorizationException('Unauthorized access. No authenticated user found.');
        }

        // Admins have full access; otherwise, check resource ownership
        if ($user->role !== 'admin' && $user->id !== $resource->user_id) {
            throw new AuthorizationException('You are not authorized to access this resource.');
        }

        return true;
    }
}