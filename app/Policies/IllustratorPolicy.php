<?php

namespace App\Policies;

use App\Models\Illustrator;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IllustratorPolicy
{
    public function createIllustrator(User $user)
    {
        return $user !== null ? Response::allow('IllustratorPolicy - createIllustrator - allowed') : Response::deny('You do not have permission to create an Illustrator.');
    }

    public function deleteIllustrator(User $user, Illustrator $illustrator)
    {
        // Check if the user is an admin or the owner of the Illustrator
        if ($user->isAdmin() || $user->id === $illustrator->user_id) {
            return Response::allow('IllustratorPolicy - deleteIllustrator - allowed');
        }

        return Response::deny('You do not have permission to delete this Illustrator.');
    }

    public function getByFullname(User $user, Illustrator $illustrator)
    {
        if (!$illustrator) {
            // Illustrator not found, return "not found" (HTTP 404) response.
            return response()->json(['message' => 'Illustrator not found'], 404);
        }

        // Check if the user is an admin or the owner of the Illustrator
        if ($user->isAdmin() || $user->id === $illustrator->user_id) {
            return Response::allow('IllustratorPolicy - getByFullname - allowed');
        }

        // User is neither an admin nor the owner, return "deny."
        return Response::deny('IllustratorPolicy - getByFullname - denied');
    }

    public function updateIllustrator(User $user, Illustrator $illustrator)
    {
        // Check if the user is an admin or the owner of the Illustrator
        if ($user->isAdmin() || $user->id === $illustrator->user_id) {
            return Response::allow('IllustratorPolicy - deleteIllustrator - allowed');
        }
        return Response::deny('IllustratorPolicy - updateIllustrator - denied');
    }
}
