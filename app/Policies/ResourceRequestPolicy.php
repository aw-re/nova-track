<?php

namespace App\Policies;

use App\Models\ResourceRequest;
use App\Models\User;
use App\Enums\ResourceRequestStatusEnum;

class ResourceRequestPolicy
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
    public function view(User $user, ResourceRequest $resourceRequest): bool
    {
        // Admin can view any request
        if ($user->isAdmin()) {
            return true;
        }

        // Request creator can view their request
        if ($resourceRequest->requested_by === $user->id) {
            return true;
        }

        // Project owner can view requests for their projects
        if ($user->isProjectOwner() && $resourceRequest->project->owner_id === $user->id) {
            return true;
        }

        // Project members can view requests for their projects
        return $resourceRequest->project->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Engineers and Contractors can create resource requests
        return $user->isEngineer() || $user->isContractor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ResourceRequest $resourceRequest): bool
    {
        // Admin can update any request
        if ($user->isAdmin()) {
            return true;
        }

        // Only creator can update, and only if it's still pending
        if ($resourceRequest->requested_by === $user->id) {
            return $resourceRequest->status === ResourceRequestStatusEnum::PENDING;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ResourceRequest $resourceRequest): bool
    {
        // Admin can delete any request
        if ($user->isAdmin()) {
            return true;
        }

        // Only creator can delete, and only if it's still pending
        if ($resourceRequest->requested_by === $user->id) {
            return $resourceRequest->status === ResourceRequestStatusEnum::PENDING;
        }

        return false;
    }

    /**
     * Determine whether the user can approve the request.
     */
    public function approve(User $user, ResourceRequest $resourceRequest): bool
    {
        // Admin can approve any request
        if ($user->isAdmin()) {
            return $resourceRequest->status === ResourceRequestStatusEnum::PENDING;
        }

        // Project owner can approve requests for their projects
        if ($user->isProjectOwner() && $resourceRequest->project->owner_id === $user->id) {
            return $resourceRequest->status === ResourceRequestStatusEnum::PENDING;
        }

        return false;
    }

    /**
     * Determine whether the user can reject the request.
     */
    public function reject(User $user, ResourceRequest $resourceRequest): bool
    {
        return $this->approve($user, $resourceRequest);
    }

    /**
     * Determine whether the user can mark the request as delivered.
     */
    public function deliver(User $user, ResourceRequest $resourceRequest): bool
    {
        // Admin can mark any request as delivered
        if ($user->isAdmin()) {
            return $resourceRequest->status === ResourceRequestStatusEnum::APPROVED;
        }

        // Project owner can mark requests for their projects as delivered
        if ($user->isProjectOwner() && $resourceRequest->project->owner_id === $user->id) {
            return $resourceRequest->status === ResourceRequestStatusEnum::APPROVED;
        }

        return false;
    }
}
