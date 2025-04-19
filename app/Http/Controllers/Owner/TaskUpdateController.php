<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskUpdateController extends Controller
{
    public function store(Request $request, Task $task)
    {
        // التحقق من أن المستخدم هو مالك المشروع
        if ($task->project->owner_id !== Auth::id()) {
            return back()->with('error', 'You are not authorized to add updates to this task');
        }

        $request->validate([
            'comment' => 'required|string|max:1000',
            'hours_spent' => 'nullable|numeric|min:0',
            'status_change' => 'nullable|boolean',
            'new_status' => 'nullable|in:backlog,todo,in_progress,review,completed'
        ]);

        $data = [
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'hours_spent' => $request->hours_spent,
            'status_change' => $request->status_change == '1',
        ];

        // إذا كان هناك تغيير في الحالة
        if ($request->status_change == '1' && $request->new_status) {
            $data['old_status'] = $task->status;
            $data['new_status'] = $request->new_status;
            
            // تحديث حالة المهمة
            $task->update(['status' => $request->new_status]);
        }

        // إنشاء التحديث
        $task->updates()->create($data);

        return redirect()->route('owner.tasks.show', $task)
               ->with('success', 'Task update added successfully');
    }
}