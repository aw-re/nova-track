<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
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
     * Display a listing of the activity logs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');
        
        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        // Filter by model type
        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', $request->model_type);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $activityLogs = $query->latest()->paginate(20);
        
        // Get unique values for filters
        $users = ActivityLog::select('user_id')->distinct()->with('user')->get()->pluck('user');
        $actions = ActivityLog::select('action')->distinct()->pluck('action');
        $modelTypes = ActivityLog::select('model_type')->distinct()->pluck('model_type');
        
        // Generate activity over time data for chart
        $activityOverTime = ActivityLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count
                ];
            });
            
        // Generate action distribution data for chart
        $actionDistribution = [
            'created' => ActivityLog::where('action', 'created')->count(),
            'updated' => ActivityLog::where('action', 'updated')->count(),
            'deleted' => ActivityLog::where('action', 'deleted')->count(),
            'logged_in' => ActivityLog::where('action', 'logged_in')->count(),
            'logged_out' => ActivityLog::where('action', 'logged_out')->count(),
            'other' => ActivityLog::whereNotIn('action', ['created', 'updated', 'deleted', 'logged_in', 'logged_out'])->count()
        ];
        
        return view('admin.activity-logs.index', compact(
            'activityLogs', 
            'users', 
            'actions', 
            'modelTypes', 
            'activityOverTime',
            'actionDistribution'
        ));
    }

    /**
     * Display the specified activity log.
     *
     * @param  \App\Models\ActivityLog  $activityLog
     * @return \Illuminate\View\View
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        return view('admin.activity-logs.show', compact('activityLog'));
    }

    /**
     * Clear all activity logs.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearAll()
    {
        // Create a final log entry about clearing logs
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'cleared',
            'description' => 'Cleared all activity logs',
            'model_type' => 'ActivityLog',
            'model_id' => null,
        ]);
        
        // Delete all logs except the one we just created
        ActivityLog::where('id', '!=', ActivityLog::latest()->first()->id)->delete();
        
        return redirect()->route('admin.activity-logs.index')
            ->with('success', 'All activity logs have been cleared.');
    }
}
