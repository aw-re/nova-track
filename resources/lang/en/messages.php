<?php

return [
    'success' => [
        'created' => ':model created successfully.',
        'updated' => ':model updated successfully.',
        'deleted' => ':model deleted successfully.',
        'started' => ':model started successfully.',
        'completed' => ':model completed successfully.',
        'progress_updated' => 'Progress updated successfully.',
    ],
    'error' => [
        'unauthorized' => 'You are not authorized to perform this action.',
        'invalid_status_start' => 'Task cannot be started. It must be in Backlog or To Do.',
        'invalid_status_complete' => 'Task cannot be completed. It must be in Progress.',
        'invalid_status_progress' => 'Progress can only be updated for tasks in Progress.',
    ],
];
