<?php

namespace App\Http\Controllers\Rating;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
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
     * Display a listing of the ratings.
     */
    public function index(Request $request, Project $project = null)
    {
        $user = Auth::user();
        
        if ($project) {
            // Check if user has access to this project
            if (!$user->isAdmin() && 
                $project->owner_id !== $user->id && 
                !$project->projectMembers()->where('user_id', $user->id)->exists()) {
                return redirect()->route('projects.index')
                    ->with('error', 'You do not have permission to view ratings for this project.');
            }
            
            $ratings = $project->ratings()
                ->with(['ratedBy', 'ratedUser'])
                ->latest()
                ->paginate(10);
                
            return view('projects.ratings.index', compact('project', 'ratings'));
        } else {
            // User's ratings (received or given)
            $tab = $request->get('tab', 'received');
            
            if ($tab === 'received') {
                $ratings = Rating::where('rated_user_id', $user->id)
                    ->with(['ratedBy', 'project'])
                    ->latest()
                    ->paginate(10);
            } else {
                $ratings = Rating::where('rated_by', $user->id)
                    ->with(['ratedUser', 'project'])
                    ->latest()
                    ->paginate(10);
            }
            
            return view('ratings.index', compact('ratings', 'tab'));
        }
    }

    /**
     * Show the form for creating a new rating.
     */
    public function create(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to create ratings for this project.');
        }
        
        // Check if project is completed
        if ($project->status !== 'completed') {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Ratings can only be created for completed projects.');
        }
        
        // Get project members that can be rated
        $projectMembers = $project->projectMembers()
            ->with(['user', 'role'])
            ->get();
            
        // Filter out the current user
        $projectMembers = $projectMembers->filter(function ($member) use ($user) {
            return $member->user_id !== $user->id;
        });
        
        // Check if already rated all members
        $ratedUserIds = Rating::where('project_id', $project->id)
            ->where('rated_by', $user->id)
            ->pluck('rated_user_id')
            ->toArray();
            
        $unratedMembers = $projectMembers->filter(function ($member) use ($ratedUserIds) {
            return !in_array($member->user_id, $ratedUserIds);
        });
        
        if ($unratedMembers->isEmpty()) {
            return redirect()->route('projects.ratings.index', $project)
                ->with('error', 'You have already rated all members of this project.');
        }
        
        return view('projects.ratings.create', compact('project', 'unratedMembers'));
    }

    /**
     * Store a newly created rating in storage.
     */
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to create ratings for this project.');
        }
        
        // Check if project is completed
        if ($project->status !== 'completed') {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Ratings can only be created for completed projects.');
        }
        
        $request->validate([
            'rated_user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        
        // Check if the rated user is a member of the project
        $isMember = $project->projectMembers()
            ->where('user_id', $request->rated_user_id)
            ->exists();
            
        if (!$isMember) {
            return redirect()->route('projects.ratings.create', $project)
                ->with('error', 'The selected user is not a member of this project.')
                ->withInput();
        }
        
        // Check if already rated this user for this project
        $existingRating = Rating::where('project_id', $project->id)
            ->where('rated_by', $user->id)
            ->where('rated_user_id', $request->rated_user_id)
            ->first();
            
        if ($existingRating) {
            return redirect()->route('projects.ratings.create', $project)
                ->with('error', 'You have already rated this user for this project.')
                ->withInput();
        }
        
        // Create the rating
        $rating = Rating::create([
            'project_id' => $project->id,
            'rated_by' => $user->id,
            'rated_user_id' => $request->rated_user_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        // Update user's average rating
        $this->updateUserAverageRating($request->rated_user_id);
        
        return redirect()->route('projects.ratings.index', $project)
            ->with('success', 'Rating submitted successfully.');
    }

    /**
     * Display the specified rating.
     */
    public function show(Project $project, Rating $rating)
    {
        $user = Auth::user();
        
        // Check if rating belongs to the project
        if ($rating->project_id !== $project->id) {
            return redirect()->route('projects.ratings.index', $project)
                ->with('error', 'The rating does not belong to this project.');
        }
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists() &&
            $rating->rated_by !== $user->id &&
            $rating->rated_user_id !== $user->id) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to view this rating.');
        }
        
        $rating->load(['ratedBy', 'ratedUser']);
        
        return view('projects.ratings.show', compact('project', 'rating'));
    }

    /**
     * Show the form for editing the specified rating.
     */
    public function edit(Project $project, Rating $rating)
    {
        $user = Auth::user();
        
        // Check if rating belongs to the project
        if ($rating->project_id !== $project->id) {
            return redirect()->route('projects.ratings.index', $project)
                ->with('error', 'The rating does not belong to this project.');
        }
        
        // Only the rating creator can edit ratings
        if ($rating->rated_by !== $user->id && !$user->isAdmin()) {
            return redirect()->route('projects.ratings.index', $project)
                ->with('error', 'You do not have permission to edit this rating.');
        }
        
        $rating->load('ratedUser');
        
        return view('projects.ratings.edit', compact('project', 'rating'));
    }

    /**
     * Update the specified rating in storage.
     */
    public function update(Request $request, Project $project, Rating $rating)
    {
        $user = Auth::user();
        
        // Check if rating belongs to the project
        if ($rating->project_id !== $project->id) {
            return redirect()->route('projects.ratings.index', $project)
                ->with('error', 'The rating does not belong to this project.');
        }
        
        // Only the rating creator can update ratings
        if ($rating->rated_by !== $user->id && !$user->isAdmin()) {
            return redirect()->route('projects.ratings.index', $project)
                ->with('error', 'You do not have permission to update this rating.');
        }
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        
        $rating->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        // Update user's average rating
        $this->updateUserAverageRating($rating->rated_user_id);
        
        return redirect()->route('projects.ratings.show', [$project, $rating])
            ->with('success', 'Rating updated successfully.');
    }

    /**
     * Remove the specified rating from storage.
     */
    public function destroy(Project $project, Rating $rating)
    {
        $user = Auth::user();
        
        // Check if rating belongs to the project
        if ($rating->project_id !== $project->id) {
            return redirect()->route('projects.ratings.index', $project)
                ->with('error', 'The rating does not belong to this project.');
        }
        
        // Only the rating creator and admin can delete ratings
        if ($rating->rated_by !== $user->id && !$user->isAdmin()) {
            return redirect()->route('projects.ratings.index', $project)
                ->with('error', 'You do not have permission to delete this rating.');
        }
        
        $ratedUserId = $rating->rated_user_id;
        
        $rating->delete();
        
        // Update user's average rating
        $this->updateUserAverageRating($ratedUserId);
        
        return redirect()->route('projects.ratings.index', $project)
            ->with('success', 'Rating deleted successfully.');
    }

    /**
     * Update a user's average rating.
     *
     * @param int $userId
     * @return void
     */
    private function updateUserAverageRating($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return;
        }
        
        $averageRating = Rating::where('rated_user_id', $userId)
            ->avg('rating');
            
        $user->update([
            'average_rating' => $averageRating,
        ]);
    }
}
