<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('project_owner') || $user->hasRole('engineer') || $user->hasRole('contractor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('project_owner')) {
            // Assuming Project Owner can view tasks in their projects
            // Logic to be refined based on Project Owner relationship to Project
            return true;
        }

        if ($user->id === $task->assigned_to || $user->id === $task->created_by || $user->id === $task->assigned_by) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('project_owner') || $user->hasRole('engineer');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Example: Only assigner or creator can fully update
        if ($user->id === $task->assigned_by || $user->id === $task->created_by) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->hasRole('admin');
    }

    // Role-specific actions
    public function start(User $user, Task $task): bool
    {
        return $user->id === $task->assigned_to && in_array($task->status, ['backlog', 'todo']);
    }

    public function complete(User $user, Task $task): bool
    {
        return $user->id === $task->assigned_to && $task->status === 'in_progress';
    }

    public function updateProgress(User $user, Task $task): bool
    {
        return $user->id === $task->assigned_to && $task->status === 'in_progress';
    }
}
