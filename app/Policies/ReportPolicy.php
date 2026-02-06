<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use App\Enums\ReportStatusEnum;

class ReportPolicy
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
    public function view(User $user, Report $report): bool
    {
        // Admin can view any report
        if ($user->isAdmin()) {
            return true;
        }

        // Report creator can view their report
        if ($report->created_by === $user->id) {
            return true;
        }

        // Project owner can view reports for their projects
        if ($user->isProjectOwner() && $report->project->owner_id === $user->id) {
            return true;
        }

        // Project members can view reports for their projects
        return $report->project->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Engineers and Contractors can create reports
        return $user->isEngineer() || $user->isContractor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Report $report): bool
    {
        // Admin can update any report
        if ($user->isAdmin()) {
            return true;
        }

        // Only creator can update, and only if it's still a draft
        if ($report->created_by === $user->id) {
            return $report->status === ReportStatusEnum::DRAFT;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Report $report): bool
    {
        // Admin can delete any report
        if ($user->isAdmin()) {
            return true;
        }

        // Only creator can delete, and only if it's still a draft
        if ($report->created_by === $user->id) {
            return $report->status === ReportStatusEnum::DRAFT;
        }

        return false;
    }

    /**
     * Determine whether the user can submit the report.
     */
    public function submit(User $user, Report $report): bool
    {
        // Only creator can submit
        if ($report->created_by !== $user->id) {
            return false;
        }

        // Can only submit drafts
        return $report->status === ReportStatusEnum::DRAFT;
    }

    /**
     * Determine whether the user can approve the report.
     */
    public function approve(User $user, Report $report): bool
    {
        // Admin can approve any report
        if ($user->isAdmin()) {
            return $report->status === ReportStatusEnum::SUBMITTED ||
                $report->status === ReportStatusEnum::PENDING;
        }

        // Project owner can approve reports for their projects
        if ($user->isProjectOwner() && $report->project->owner_id === $user->id) {
            return $report->status === ReportStatusEnum::SUBMITTED ||
                $report->status === ReportStatusEnum::PENDING;
        }

        return false;
    }

    /**
     * Determine whether the user can reject the report.
     */
    public function reject(User $user, Report $report): bool
    {
        return $this->approve($user, $report);
    }
}
