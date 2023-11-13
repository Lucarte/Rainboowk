<?php

namespace App\Policies;

use App\Http\Controllers\LoginController;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PublisherPolicy
{
    // true null vs. true false
    public function before(User $user)
    {
        return $user->isAdmin() ? true : null;
    }

    public function createPublisher(User $user)
    {
        return $user !== null ? Response::allow('PublisherPolicy - createPublisher - allowed') : Response::deny('You do not have permission to create an Publisher.');
    }

    public function deletePublisher(User $user, Publisher $publisher)
    {
        // Check if the user is an admin or the owner of the Publisher
        if ($user->id === $publisher->user_id) {
            return Response::allow('PublisherPolicy - deletePublisher - allowed');
        }

        return Response::deny('You do not have permission to delete this Publisher.');
    }

    public function getByName(User $user, Publisher $publisher)
    {
        if (!$publisher) {
            // Publisher not found, return "not found" (HTTP 404) response.
            return response()->json(['message' => 'Publisher not found'], 404);
        }

        // Check if the user is an admin or the owner of the Publisher
        if ($user->id === $publisher->user_id) {
            return Response::allow('PublisherPolicy - getByFullname - allowed');
        }

        // User is neither an admin nor the owner, return "deny."
        return Response::deny('PublisherPolicy - getByFullname - denied');
    }

    public function updatePublisher(User $user, Publisher $publisher)
    {
        // Check if the user is an admin or the owner of the Publisher
        if ($user->id === $publisher->user_id) {
            return Response::allow('PublisherPolicy - updatePublisher - allowed');
        }
        return Response::deny('PublisherPolicy - updatePublisher - denied');
    }
}
