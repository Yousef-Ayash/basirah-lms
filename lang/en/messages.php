<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Message Lines
    |--------------------------------------------------------------------------
    |
    | These lines are used for various success, error, and info messages
    | that are flashed to the session from your controllers.
    |
    */

    // General
    'item_created' => ':Item created successfully.',
    'item_updated' => ':Item updated successfully.',
    'item_deleted' => ':Item deleted successfully.',
    'item_removed' => ':Item removed successfully.',
    'item_uploaded' => ':Item uploaded successfully.',

    // Bookmarks
    'bookmarked' => 'Bookmarked.',
    'bookmark_removed' => 'Bookmark removed.',

    // Exams
    'exam_created' => 'Exam created.',
    'exam_updated' => 'Exam updated.',
    'exam_deleted' => 'Exam deleted.',
    'exam_not_open_yet' => 'Exam not open yet.',
    'exam_is_closed' => 'Exam is already closed.',
    'exam_no_questions' => 'This exam has no questions and cannot be started.',
    'exam_submitted_successfully' => 'Your exam was submitted. Score: :score%',

    // Attempts
    'max_attempts_reached' => 'Maximum attempts reached.',
    'attempt_already_submitted' => 'Attempt already submitted.',
    'time_limit_exceeded' => 'Time limit exceeded.',

    // Questions
    'question_added' => 'Question added.',
    'question_updated' => 'Question updated.',
    'question_removed' => 'Question removed.',
    'questions_added' => 'Questions added successfully.',
    'questions_all_deleted' => 'All questions have been deleted.',
    'questions_save_failed' => 'Unable to save questions. Please check your data.',

    // Materials
    'material_uploaded' => 'Material uploaded.',
    'material_deleted' => 'Material deleted.',

    // Students
    'student_created' => 'Student created.',
    'student_updated' => 'Student updated.',
    'student_removed' => 'Student removed.',
    'student_approved' => 'Student approved.',
    'student_denied' => 'Student denied.',
    'student_advanced' => 'Student advanced to level: :level',
    'no_next_level' => 'No next level found.',

    // Marks
    'mark_added' => 'Mark added.',
    'mark_updated' => 'Mark updated.',
    'mark_deleted' => 'Mark deleted.',
    'marks_imported' => 'Marks imported successfully.',

    // Users / Auth
    'user_approved' => 'User approved.',
    'user_deleted' => 'User deleted.',
    'account_pending_approval' => 'Your account is still pending admin approval.',
    'verification_link_sent' => 'A new verification link has been sent to the email address you provided during registration.',

    // Import / Export
    'import_preview_expired' => 'Import preview not found or expired. Please re-upload.',
    'import_commit_failed' => 'Import commit failed. See logs.',
    'import_has_errors_students' => 'Preview contains errors or existing users (choose duplicate policy "skip" or "update" to proceed).',
    'import_has_errors' => 'Preview contains errors. Fix them before committing.',

];