<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Message Lines (Arabic)
    |--------------------------------------------------------------------------
    |
    | These lines are used for various success, error, and info messages
    | that are flashed to the session from your controllers.
    |
    */

    // General
    'item_created' => 'تم إنشاء :Item بنجاح.',
    'item_updated' => 'تم تحديث :Item بنجاح.',
    'item_deleted' => 'تم حذف :Item بنجاح.',
    'item_removed' => 'تمت إزالة :Item بنجاح.',
    'item_uploaded' => 'تم رفع :Item بنجاح.',

    // Bookmarks
    'bookmarked' => 'تمت الإشارة المرجعية.',
    'bookmark_removed' => 'تمت إزالة الإشارة المرجعية.',

    // Exams
    'exam_created' => 'تم إنشاء الاختبار.',
    'exam_updated' => 'تم تحديث الاختبار.',
    'exam_deleted' => 'تم حذف الاختبار.',
    'exam_not_open_yet' => 'الاختبار لم يفتح بعد.',
    'exam_is_closed' => 'الاختبار مغلق بالفعل.',
    'exam_no_questions' => 'هذا الاختبار لا يحتوي على أسئلة ولا يمكن البدء به.',
    'exam_submitted_successfully' => 'تم تسليم اختبارك. الدرجة: :score%',

    // Attempts
    'max_attempts_reached' => 'تم الوصول إلى الحد الأقصى من المحاولات.',
    'attempt_already_submitted' => 'تم تسليم المحاولة بالفعل.',
    'time_limit_exceeded' => 'تم تجاوز الحد الزمني.',

    // Questions
    'question_added' => 'تمت إضافة السؤال.',
    'question_updated' => 'تم تحديث السؤال.',
    'question_removed' => 'تمت إزالة السؤال.',
    'questions_added' => 'تمت إضافة الأسئلة بنجاح.',
    'questions_all_deleted' => 'تم حذف جميع الأسئلة.',
    'questions_save_failed' => 'تعذر حفظ الأسئلة. يرجى التحقق من بياناتك.',

    // Materials
    'material_uploaded' => 'تم رفع المادة.',
    'material_deleted' => 'تم حذف المادة.',

    // Students
    'student_created' => 'تم إنشاء الطالب.',
    'student_updated' => 'تم تحديث الطالب.',
    'student_removed' => 'تمت إزالة الطالب.',
    'student_approved' => 'تمت الموافقة على الطالب.',
    'student_denied' => 'تم رفض الطالب.',
    'student_advanced' => 'تمت ترقية الطالب إلى المستوى: :level',
    'no_next_level' => 'لم يتم العثور على مستوى تالٍ.',

    // Marks
    'mark_added' => 'تمت إضافة الدرجة.',
    'mark_updated' => 'تم تحديث الدرجة.',
    'mark_deleted' => 'تم حذف الدرجة.',
    'marks_imported' => 'تم استيراد الدرجات بنجاح.',

    // Users / Auth
    'user_approved' => 'تمت الموافقة على المستخدم.',
    'user_deleted' => 'تم حذف المستخدم.',
    'account_pending_approval' => 'حسابك لا يزال في انتظار موافقة المسؤول.',
    'verification_link_sent' => 'تم إرسال رابط تحقق جديد إلى عنوان البريد الإلكتروني الذي قدمته أثناء التسجيل.',

    // Import / Export
    'import_preview_expired' => 'لم يتم العثور على معاينة الاستيراد أو انتهت صلاحيتها. يرجى إعادة رفع الملف.',
    'import_commit_failed' => 'فشل تأكيد الاستيراد. راجع السجلات.',
    'import_has_errors_students' => 'تحتوي المعاينة على أخطاء أو مستخدمين حاليين (اختر سياسة التكرار "تخطي" أو "تحديث" للمتابعة).',
    'import_has_errors' => 'تحتوي المعاينة على أخطاء. يرجى إصلاحها قبل التأكيد.',

];