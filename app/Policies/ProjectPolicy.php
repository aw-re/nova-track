<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        // Admin can view any project
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can view their own projects
        if ($project->owner_id === $user->id) {
            return true;
        }

        // Members can view projects they're assigned to
        return $project->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isProjectOwner();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        // Admin can update any project
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can update their own projects
        return $project->owner_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // Admin can delete any project
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can delete their own projects
        return $project->owner_id === $user->id;
    }

    /**
     * Determine whether the user can manage project members.
     */
    public function manageMembers(User $user, Project $project): bool
    {
        // Admin can manage any project
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can manage their own projects
        return $project->owner_id === $user->id;
    }

    /**
     * Determine whether the user can invite members to the project.
     */
    public function invite(User $user, Project $project): bool
    {
        return $this->manageMembers($user, $project);
    }
}
