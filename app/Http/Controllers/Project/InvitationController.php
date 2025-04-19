<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the invitations.
     */
    public function index(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'You do not have permission to view invitations for this project.');
        }
        
        $invitations = $project->invitations()->with(['sender', 'recipient', 'role'])->latest()->get();
        
        return view('projects.invitations.index', compact('project', 'invitations'));
    }

    /**
     * Show the form for creating a new invitation.
     */
    public function create(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'You do not have permission to create invitations for this project.');
        }
        
        $roles = Role::whereIn('name', ['engineer', 'contractor'])->get();
        
        return view('projects.invitations.create', compact('project', 'roles'));
    }

    /**
     * Store a newly created invitation in storage.
     */
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'You do not have permission to create invitations for this project.');
        }
        
        $request->validate([
            'recipient_email' => 'required|email',
            'role_id' => 'required|exists:roles,id',
            'message' => 'nullable|string',
        ]);
        
        // Check if user with this email already exists
        $recipient = User::where('email', $request->recipient_email)->first();
        
        // Check if this user is already a member of the project
        if ($recipient && $project->projectMembers()->where('user_id', $recipient->id)->exists()) {
            return redirect()->route('projects.invitations.create', $project)
                ->with('error', 'This user is already a member of the project.')
                ->withInput();
        }
        
        // Check if there's already a pending invitation for this email
        if ($project->invitations()->where('recipient_email', $request->recipient_email)
                ->where('status', 'pending')->exists()) {
            return redirect()->route('projects.invitations.create', $project)
                ->with('error', 'There is already a pending invitation for this email.')
                ->withInput();
        }
        
        // Create the invitation
        $invitation = Invitation::create([
            'project_id' => $project->id,
            'sender_id' => $user->id,
            'recipient_email' => $request->recipient_email,
            'recipient_id' => $recipient ? $recipient->id : null,
            'role_id' => $request->role_id,
            'token' => Str::random(60),
            'message' => $request->message,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);
        
        // TODO: Send invitation email
        // This would typically use Laravel's Mail facade to send an email
        // Mail::to($request->recipient_email)->send(new ProjectInvitation($invitation));
        
        return redirect()->route('projects.invitations.index', $project)
            ->with('success', 'Invitation sent successfully.');
    }

    /**
     * Display the specified invitation.
     */
    public function show(Project $project, Invitation $invitation)
    {
        $user = Auth::user();
        
        // Check if user has access to this invitation
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $invitation->recipient_id !== $user->id) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'You do not have permission to view this invitation.');
        }
        
        $invitation->load(['sender', 'recipient', 'role', 'project']);
        
        return view('projects.invitations.show', compact('project', 'invitation'));
    }

    /**
     * Accept an invitation.
     */
    public function accept(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->firstOrFail();
        
        $user = Auth::user();
        
        // Check if the invitation is for the authenticated user
        if ($invitation->recipient_email !== $user->email) {
            return redirect()->route('dashboard')
                ->with('error', 'This invitation is not for you.');
        }
        
        // Update the invitation status
        $invitation->update([
            'status' => 'accepted',
            'recipient_id' => $user->id,
        ]);
        
        // Add the user as a project member
        ProjectMember::create([
            'project_id' => $invitation->project_id,
            'user_id' => $user->id,
            'role_id' => $invitation->role_id,
            'status' => 'active',
            'joined_at' => now(),
        ]);
        
        return redirect()->route('projects.show', $invitation->project_id)
            ->with('success', 'You have successfully joined the project.');
    }

    /**
     * Reject an invitation.
     */
    public function reject(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->firstOrFail();
        
        $user = Auth::user();
        
        // Check if the invitation is for the authenticated user
        if ($invitation->recipient_email !== $user->email) {
            return redirect()->route('dashboard')
                ->with('error', 'This invitation is not for you.');
        }
        
        // Update the invitation status
        $invitation->update([
            'status' => 'rejected',
            'recipient_id' => $user->id,
        ]);
        
        return redirect()->route('dashboard')
            ->with('success', 'You have rejected the project invitation.');
    }

    /**
     * Cancel an invitation.
     */
    public function cancel(Project $project, Invitation $invitation)
    {
        $user = Auth::user();
        
        // Check if user has access to cancel this invitation
        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            return redirect()->route('projects.invitations.index', $project)
                ->with('error', 'You do not have permission to cancel this invitation.');
        }
        
        // Only pending invitations can be cancelled
        if ($invitation->status !== 'pending') {
            return redirect()->route('projects.invitations.index', $project)
                ->with('error', 'Only pending invitations can be cancelled.');
        }
        
        $invitation->update([
            'status' => 'cancelled',
        ]);
        
        return redirect()->route('projects.invitations.index', $project)
            ->with('success', 'Invitation cancelled successfully.');
    }

    /**
     * Resend an invitation.
     */
    public function resend(Project $project, Invitation $invitation)
    {
        $user = Auth::user();
        
        // Check if user has access to resend this invitation
        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            return redirect()->route('projects.invitations.index', $project)
                ->with('error', 'You do not have permission to resend this invitation.');
        }
        
        // Only expired or pending invitations can be resent
        if (!in_array($invitation->status, ['pending', 'expired'])) {
            return redirect()->route('projects.invitations.index', $project)
                ->with('error', 'Only pending or expired invitations can be resent.');
        }
        
        $invitation->update([
            'status' => 'pending',
            'token' => Str::random(60),
            'expires_at' => now()->addDays(7),
        ]);
        
        // TODO: Send invitation email
        // This would typically use Laravel's Mail facade to send an email
        // Mail::to($invitation->recipient_email)->send(new ProjectInvitation($invitation));
        
        return redirect()->route('projects.invitations.index', $project)
            ->with('success', 'Invitation resent successfully.');
    }
}
