<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\User;
use App\Models\Project;
use App\Models\ActivityLog;
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
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of the ratings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Rating::with(['ratedUser', 'ratedBy', 'project']);
        
        // Filter by project
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }
        
        // Filter by rated user
        if ($request->has('rated_user_id') && $request->rated_user_id) {
            $query->where('rated_user_id', $request->rated_user_id);
        }
        
        // Filter by rating value
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }
        
        $ratings = $query->latest()->paginate(15);
        
        // Get data for filters
        $projects = Project::all();
        $users = User::all();
        $ratingValues = [1, 2, 3, 4, 5];
        
        return view('admin.ratings.index', compact('ratings', 'projects', 'users', 'ratingValues'));
    }

    /**
     * Show the form for creating a new rating.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $projects = Project::all();
        $users = User::all();
        return view('admin.ratings.create', compact('projects', 'users'));
    }

    /**
     * Store a newly created rating in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'rated_user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        // Check if rating already exists
        $existingRating = Rating::where('project_id', $request->project_id)
            ->where('rated_user_id', $request->rated_user_id)
            ->where('rated_by', Auth::id())
            ->first();
            
        if ($existingRating) {
            return redirect()->route('admin.ratings.index')
                ->with('error', 'You have already rated this user for this project.');
        }

        $rating = Rating::create([
            'project_id' => $request->project_id,
            'rated_user_id' => $request->rated_user_id,
            'rated_by' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Update user's average rating
        $this->updateUserAverageRating($request->rated_user_id);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'description' => 'Created rating for user ID: ' . $request->rated_user_id,
            'model_type' => 'Rating',
            'model_id' => $rating->id,
        ]);

        return redirect()->route('admin.ratings.index')
            ->with('success', 'Rating created successfully.');
    }

    /**
     * Display the specified rating.
     *
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\View\View
     */
    public function show(Rating $rating)
    {
        $rating->load(['ratedUser', 'ratedBy', 'project']);
        return view('admin.ratings.show', compact('rating'));
    }

    /**
     * Show the form for editing the specified rating.
     *
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\View\View
     */
    public function edit(Rating $rating)
    {
        $projects = Project::all();
        $users = User::all();
        return view('admin.ratings.edit', compact('rating', 'projects', 'users'));
    }

    /**
     * Update the specified rating in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Rating $rating)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'rated_user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $rating->update([
            'project_id' => $request->project_id,
            'rated_user_id' => $request->rated_user_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Update user's average rating
        $this->updateUserAverageRating($request->rated_user_id);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'description' => 'Updated rating for user ID: ' . $request->rated_user_id,
            'model_type' => 'Rating',
            'model_id' => $rating->id,
        ]);

        return redirect()->route('admin.ratings.index')
            ->with('success', 'Rating updated successfully.');
    }

    /**
     * Remove the specified rating from storage.
     *
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Rating $rating)
    {
        $ratedUserId = $rating->rated_user_id;

        // Log activity before deletion
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'description' => 'Deleted rating for user ID: ' . $ratedUserId,
            'model_type' => 'Rating',
            'model_id' => $rating->id,
        ]);

        $rating->delete();

        // Update user's average rating
        $this->updateUserAverageRating($ratedUserId);

        return redirect()->route('admin.ratings.index')
            ->with('success', 'Rating deleted successfully.');
    }

    /**
     * Update a user's average rating.
     *
     * @param  int  $userId
     * @return void
     */
    private function updateUserAverageRating($userId)
    {
        $user = User::find($userId);
        
        if ($user) {
            $averageRating = $user->receivedRatings()->avg('rating') ?: 0;
            $user->update(['average_rating' => $averageRating]);
        }
    }
}
