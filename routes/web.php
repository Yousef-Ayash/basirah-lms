<?php

use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\ExamLogController;
use App\Http\Controllers\Admin\MarkController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ViewController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ExamAttemptController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\Admin\LevelController as AdminLevelController;
use App\Models\Subject;
use App\Models\Teacher;
use Inertia\Inertia;


// Public and Foundational Routes
// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });


// #####################################################################
// ## New FrontEnd
// #####################################################################

Route::get('/', function () {
    $teachers = \App\Models\Teacher::orderBy('order')->get();
    $subjects = \App\Models\Subject::orderBy('id')->get(['title', 'description']);
    return Inertia::render('General/Home', [
        'teachers' => $teachers,
        'subjects' => $subjects
    ]);
})->name('home');

Route::get('/about', function () {
    return Inertia::render('General/About');
})->name('about');

Route::get('/internal-system', function () {
    return Inertia::render('General/InternalSystem');
})->name('i-sys');

Route::get('/join-us', function () {
    return Inertia::render('General/JoinUs');
})->name('join');

// Route::get('/teachers-new', function () {
//     return Inertia::render('General/Teachers');
// })->name('teachers');

Route::get('/main-subjects', function () {
    return Inertia::render('General/MainSubjects');
})->name('subjects');

// ## Teachers
Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers');


// #####################################################################
// ## Phase 1: Authentication, RBAC, and Profile Management
// #####################################################################
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'approved'])
    ->name('dashboard');

// Page for users whose accounts are pending approval
Route::get('/approval-pending', [RegisteredUserController::class, 'approvalPending'])
    ->name('auth.approval.pending');

// User profile management (edit, update, delete)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// #####################################################################
// ## Authenticated Student Routes (Non-Admin)
// #####################################################################
Route::middleware(['auth', 'approved'])->group(function () {

    // ## Subjects, Materials & Bookmarks
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');
    Route::get('materials/{material}', [MaterialController::class, 'show'])->name('materials.show');

    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/bookmarks/{material}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');

    // ## Exam Lifecycle for Students
    Route::get('exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
    Route::post('exams/{exam}/start', [ExamAttemptController::class, 'start'])->name('exams.start');
    Route::get('my-attempts', [ExamAttemptController::class, 'index'])->name('attempts.index');
    Route::post('attempts/{attempt}/autosave', [ExamAttemptController::class, 'autosave'])->name('attempts.autosave');
    Route::post('attempts/{attempt}/submit', [ExamAttemptController::class, 'submit'])->name('attempts.submit');
    Route::post('/attempts/{attempt}/abort', [ExamAttemptController::class, 'abort'])->name('attempts.abort');

    // ## Export and download attempts
    Route::get('/attempts/export.xlsx', [ExamAttemptController::class, 'exportAttemptsExcel'])->name('attempts.export.xlsx');
    Route::get('/attempts/export_ar.pdf', [ExportController::class, 'exportAttemptsPdf'])->name('attempts.export.pdf');

});

// #####################################################################
// ## Admin Panel Routes
// #####################################################################
Route::prefix('admin')->name('admin.')->middleware(['auth', 'approved', 'role:admin'])->group(function () {

    // ## Initial User Approval
    Route::get('/users/pending', [UserController::class, 'index'])->name('users.pending');
    Route::post('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/deny', [UserController::class, 'deny'])->name('users.deny');

    // ## Subjects & Materials Management
    Route::resource('subjects', AdminSubjectController::class)->except(['show']);
    Route::post('subjects/{subject}/materials', [AdminMaterialController::class, 'store'])->name('subjects.materials.store');
    Route::delete('materials/{material}', [AdminMaterialController::class, 'destroy'])->name('materials.destroy');

    // ## Exam, Question, and Attempt Management
    Route::resource('exams', AdminExamController::class);
    Route::get('exams/{exam}/questions/export', [AdminExamController::class, 'exportQuestions'])->name('exams.questions.export');
    Route::get('exams/{exam}/questions', [AdminQuestionController::class, 'index'])->name('exams.questions.index');
    Route::get('exams/{exam}/questions/create', [AdminQuestionController::class, 'create'])->name('exams.questions.create');
    Route::post('exams/{exam}/questions', [AdminQuestionController::class, 'store'])->name('exams.questions.store');
    Route::post('exams/{exam}/questions/batch-store', [AdminQuestionController::class, 'storeBatch'])->name('exams.questions.store.batch');
    Route::get('exams/{exam}/questions/{question}/edit', [AdminQuestionController::class, 'edit'])->name('exams.questions.edit');
    Route::put('exams/{exam}/questions/{question}', [AdminQuestionController::class, 'update'])->name('exams.questions.update');
    Route::delete('exams/{exam}/questions/destroy-all', [AdminQuestionController::class, 'destroyAll'])->name('exams.questions.destroy.all');
    Route::delete('exams/{exam}/questions/{question}', [AdminQuestionController::class, 'destroy'])->name('exams.questions.destroy');
    Route::post('exams/{exam}/questions/import/preview', [AdminQuestionController::class, 'importPreview'])->name('exams.questions.import.preview');
    Route::post('exams/{exam}/questions/import/commit', [AdminQuestionController::class, 'importCommit'])->name('exams.questions.import.commit');
    Route::get('exams/{exam}/attempts', [AdminExamController::class, 'attempts'])->name('exams.attempts.index');
    Route::get('exams/{exam}/attempts/{attempt}', [AdminExamController::class, 'attemptShow'])->name('exams.attempts.show');
    // Route::delete('exams/')

    // ## Exam Logging & Auditing
    Route::get('logs/exam', [ExamLogController::class, 'index'])->name('logs.exam.index');
    Route::get('logs/exam/export', [ExamLogController::class, 'export'])->name('logs.exam.export');

    // ## Marks & Reporting
    Route::resource('marks', MarkController::class)->except(['show']);
    Route::post('marks/import/preview', [MarkController::class, 'importPreview'])->name('marks.import.preview');
    Route::post('marks/import/commit', [MarkController::class, 'importCommit'])->name('marks.import.commit');
    Route::get('marks/import', [MarkController::class, 'importForm'])->name('marks.import.form');
    Route::get('marks/export', [MarkController::class, 'export'])->name('marks.export');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/student-exam', [ReportController::class, 'perStudentExam'])->name('reports.student_exam');
    Route::get('reports/subject', [ReportController::class, 'perSubjectAggregate'])->name('reports.subject');
    Route::get('reports/level', [ReportController::class, 'perLevelSummary'])->name('reports.level');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('reports/exams-for-student/{user}', [ReportController::class, 'getExamsForStudent'])->name('reports.exams_for_student');

    // ## Student Management
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    Route::resource('students', StudentController::class)->except(['show']);
    Route::post('students/{user}/approve', [StudentController::class, 'approve'])->name('students.approve');
    Route::post('students/{user}/deny', [StudentController::class, 'deny'])->name('students.deny');
    Route::post('students/{user}/advance', [StudentController::class, 'advance'])->name('students.advance');
    Route::get('students/import', [StudentController::class, 'importForm'])->name('students.import.form');
    Route::post('students/import/preview', [StudentController::class, 'importPreview'])->name('students.import.preview');
    Route::post('students/import/commit', [StudentController::class, 'importCommit'])->name('students.import.commit');

    // ## View as student
    Route::get('view-as-student', [ViewController::class, 'viewAsStudent'])->name('view-as-student');
    Route::get('view-as-admin', [ViewController::class, 'viewAsAdmin'])->name('view-as-admin');

    // ## Teacher routes
    Route::resource('teachers', AdminTeacherController::class);

    // ## Levels routes
    Route::resource('levels', AdminLevelController::class)->except(['show']);
});

require __DIR__ . '/auth.php';
