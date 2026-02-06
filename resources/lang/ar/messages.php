<?php

return [
    'success' => [
        'created' => 'تم إنشاء :model بنجاح.',
        'updated' => 'تم تحديث :model بنجاح.',
        'deleted' => 'تم حذف :model بنجاح.',
        'started' => 'تم بدء :model بنجاح.',
        'completed' => 'تم إكمال :model بنجاح.',
        'progress_updated' => 'تم تحديث التقدم بنجاح.',
    ],
    'error' => [
        'unauthorized' => 'ليس لديك الصلاحية للقيام بهذا الإجراء.',
        'invalid_status_start' => 'لا يمكن بدء المهمة. يجب أن تكون في حالة الانتظار أو للقيام به.',
        'invalid_status_complete' => 'لا يمكن إكمال المهمة. يجب أن تكون قيد التنفيذ.',
        'invalid_status_progress' => 'يمكن تحديث التقدم فقط للمهام قيد التنفيذ.',
    ],
];
