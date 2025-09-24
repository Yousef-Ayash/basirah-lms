<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\ImportStudentsRequest;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use App\Exports\StudentsExport;

class StudentController extends Controller
{
    // GET /admin/students
    public function index(Request $request)
    {
        $q = $request->input('q');
        $levelId = $request->input('level_id');
        $approved = $request->input('is_approved'); // '1' or '0' or null

        $query = User::role('student')->with('level');

        if ($q) {
            $query->where(function ($s) use ($q) {
                $s->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($levelId !== null) {
            $query->where('level_id', $levelId);
        }

        if ($approved !== null && $approved !== '') {
            $query->where('is_approved', (bool) $approved);
        }

        $students = $query->orderBy('name')->paginate(25)->withQueryString();
        $levels = Level::orderBy('order')->get();

        return Inertia::render('Admin/Students/Index', [
            'students' => $students,
            'levels' => $levels,
            'filters' => ['q' => $q, 'level_id' => $levelId, 'is_approved' => $approved],
        ]);
    }

    // GET /admin/students/create
    public function create()
    {
        $levels = Level::orderBy('order')->get();
        return Inertia::render('Admin/Students/Create', ['levels' => $levels]);
    }

    // POST /admin/students
    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();
        $password = $data['password'] ?? Str::random(10);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'level_id' => $data['level_id'] ?? null,
            'is_approved' => $data['is_approved'] ?? false,
        ]);

        // ensure student role exists and assign
        $role = Role::firstOrCreate(['name' => 'student']);
        $user->assignRole($role);

        // return redirect()->route('admin.students.index')->with('success', 'Student created.');
        return redirect()->route('admin.students.index')->with('success', __('messages.student_created'));
    }

    // GET /admin/students/{user}/edit
    public function edit(User $student)
    {
        $levels = Level::orderBy('order')->get();
        return Inertia::render('Admin/Students/Edit', [
            'student' => $student,
            'levels' => $levels,
        ]);
    }

    // PUT /admin/students/{user}
    public function update(StoreStudentRequest $request, User $student)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $student->password = Hash::make($data['password']);
        }

        $student->name = $data['name'];
        $student->email = $data['email'];
        $student->level_id = $data['level_id'] ?? null;
        $student->is_approved = $data['is_approved'] ?? false;
        $student->save();

        // return redirect()->route('admin.students.index')->with('success', 'Student updated.');
        return redirect()->route('admin.students.index')->with('success', __('messages.student_updated'));
    }

    // DELETE /admin/students/{user}
    public function destroy(User $student)
    {
        $student->removeRole('student');
        $student->delete();

        // return redirect()->route('admin.students.index')->with('success', 'Student removed.');
        return redirect()->route('admin.students.index')->with('success', __('messages.student_removed'));
    }

    // POST /admin/students/{user}/approve
    public function approve(User $user)
    {
        $user->is_approved = true;
        $user->save();

        // return redirect()->back()->with('success', 'Student approved.');
        return redirect()->back()->with('success', __('messages.student_approved'));
    }

    // POST /admin/students/{user}/deny
    public function deny(User $user)
    {
        $user->is_approved = false;
        $user->save();

        // return redirect()->back()->with('success', 'Student denied.');
        return redirect()->back()->with('success', __('messages.student_denied'));
    }

    // POST /admin/students/{user}/advance
    public function advance(User $user)
    {
        // find current level order and find next level by order
        $current = $user->level;
        $next = null;
        if ($current) {
            $next = Level::where('order', '>', $current->order)->orderBy('order')->first();
        } else {
            // if user has no level, pick the first
            $next = Level::orderBy('order')->first();
        }

        if (!$next) {
            // return redirect()->back()->withErrors(['advance' => 'No next level found.']);
            return redirect()->back()->withErrors(['advance' => __('messages.no_next_level')]);
        }

        $user->level_id = $next->id;
        $user->save();

        // return redirect()->back()->with('success', 'Student advanced to level: ' . $next->name);
        return redirect()->back()->with('success', __('messages.student_advanced', ['level' => $next->name]));
    }

    // GET /admin/students/import
    public function importForm()
    {
        return Inertia::render('Admin/Students/Import');
    }

    /**
     * POST /admin/students/import/preview
     * Parse uploaded file, validate rows, detect duplicates (in file and DB),
     * cache preview for single-use commit.
     */
    public function importPreview(ImportStudentsRequest $request)
    {
        $arr = Excel::toArray(null, $request->file('file'));
        $rows = $arr[0] ?? [];

        // detect header row (supports named columns)
        $hasHeader = false;
        $headers = [];
        if (isset($rows[0]) && is_array($rows[0])) {
            $first = array_map('strtolower', array_map('trim', $rows[0]));
            $headerKeys = ['name', 'full name', 'email', 'level', 'level_id', 'level_name', 'approved', 'is_approved', 'password'];
            foreach ($first as $c) {
                if (in_array($c, $headerKeys) || str_contains($c, 'email') || str_contains($c, 'name')) {
                    $hasHeader = true;
                }
            }
            if ($hasHeader) {
                $headers = $first;
                array_shift($rows);
            }
        }

        $preview = [];
        $seen = []; // emails seen in file
        $rowNum = 1;
        foreach ($rows as $row) {
            $rowNum++;
            // map cols: if headers exist, map by header name; else use conventional indexes
            $col = function ($i) use ($row) {
                return isset($row[$i]) ? trim((string) $row[$i]) : null;
            };

            $data = [
                'name' => null,
                'email' => null,
                'level' => null,
                'password' => null,
                'is_approved' => null,
            ];

            if ($headers) {
                // find header indexes
                foreach ($headers as $idx => $h) {
                    $k = strtolower($h);
                    $val = isset($row[$idx]) ? trim((string) $row[$idx]) : null;
                    if (str_contains($k, 'email'))
                        $data['email'] = $val;
                    elseif (str_contains($k, 'name'))
                        $data['name'] = $val;
                    elseif (str_contains($k, 'level'))
                        $data['level'] = $val;
                    elseif (str_contains($k, 'pass'))
                        $data['password'] = $val;
                    elseif (str_contains($k, 'approve'))
                        $data['is_approved'] = in_array(strtolower($val), ['1', 'true', 'yes', 'approved', 'y']);
                }
            } else {
                // conventional ordering: name, email, level, is_approved, password
                $data['name'] = $col(0);
                $data['email'] = $col(1);
                $data['level'] = $col(2);
                $data['is_approved'] = $col(3);
                $data['password'] = $col(4);
                // normalize is_approved
                $data['is_approved'] = in_array(strtolower((string) $data['is_approved']), ['1', 'true', 'yes', 'approved', 'y']);
            }

            $errors = [];

            // email required & valid
            if (!$data['email'] || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email required.';
            }

            // name required
            if (!$data['name']) {
                $errors[] = 'Name is required.';
            }

            // detect duplicate in file
            $emailKey = strtolower($data['email'] ?? '');
            if ($emailKey) {
                if (isset($seen[$emailKey])) {
                    $errors[] = 'Duplicate email in file (row ' . $seen[$emailKey] . ').';
                } else {
                    $seen[$emailKey] = $rowNum;
                }
            }

            // detect existing user in DB
            $existing = null;
            if ($emailKey) {
                $existing = User::whereRaw('lower(email) = ?', [$emailKey])->first();
            }

            // resolve level id if provided (by name or id)
            $levelResolved = null;
            if ($data['level']) {
                $lvl = null;
                if (is_numeric($data['level'])) {
                    $lvl = Level::find((int) $data['level']);
                } else {
                    $lvl = Level::where('name', $data['level'])->first();
                }
                if ($lvl)
                    $levelResolved = ['id' => $lvl->id, 'name' => $lvl->name];
                else
                    $errors[] = 'Level not found: ' . $data['level'];
            }

            // Build preview row
            $preview[] = [
                'row' => $rowNum,
                'name' => $data['name'],
                'email' => $data['email'],
                'level' => $levelResolved,
                'is_approved' => (bool) ($data['is_approved']),
                'password_provided' => (bool) ($data['password']),
                'existing_user_id' => $existing?->id,
                'existing_user_name' => $existing?->name,
                'errors' => $errors,
            ];
        }

        $previewId = (string) Str::uuid();
        Cache::put("students_import_{$previewId}", [
            'rows' => $preview,
            'uploaded_by' => auth()->id(),
        ], now()->addHour());

        return Inertia::render('Admin/Students/ImportPreview', [
            'preview' => $preview,
            'preview_id' => $previewId,
        ]);
    }

    /**
     * POST /admin/students/import/commit
     * Accepts preview_id and param 'on_duplicate' => 'fail'|'skip'|'update'
     */
    public function importCommit(Request $request)
    {
        $request->validate([
            'preview_id' => 'required|string',
            'on_duplicate' => 'nullable|in:fail,skip,update',
        ]);

        $cacheKey = "students_import_{$request->preview_id}";
        $cached = Cache::pull($cacheKey);
        if (!$cached || !isset($cached['rows'])) {
            // return redirect()->back()->withErrors(['import' => 'Import preview not found or expired.']);
            return redirect()->back()->withErrors(['import' => __('messages.import_preview_expired')]);
        }

        $rows = $cached['rows'];
        $policy = $request->input('on_duplicate', 'fail');

        // Check for preview errors first
        $hasErrors = false;
        foreach ($rows as $r) {
            if (!empty($r['errors'])) {
                $hasErrors = true;
                break;
            }
            if ($r['existing_user_id'] && $policy === 'fail') {
                // treat existing users as errors when policy=fail
                $hasErrors = true;
                break;
            }
        }

        if ($hasErrors && $policy === 'fail') {
            // return redirect()->back()->withErrors(['import' => 'Preview contains errors or existing users (choose duplicate policy "skip" or "update" to proceed).']);
            return redirect()->back()->withErrors(['import' => __('messages.import_has_errors_students')]);
        }

        DB::beginTransaction();
        try {
            $role = Role::firstOrCreate(['name' => 'student']);
            foreach ($rows as $r) {
                if ($r['existing_user_id']) {
                    if ($policy === 'skip') {
                        continue;
                    } elseif ($policy === 'update') {
                        $u = User::find($r['existing_user_id']);
                        if (!$u)
                            continue;
                        $u->name = $r['name'] ?? $u->name;
                        $u->is_approved = $r['is_approved'] ?? $u->is_approved;
                        if ($r['level'])
                            $u->level_id = $r['level']['id'];
                        $u->save();
                        if (!$u->hasRole('student'))
                            $u->assignRole($role);
                        continue;
                    }
                    // if policy=fail, we wouldn't be here
                }

                // create new user
                $randomPassword = Str::random(10);
                $user = User::create([
                    'name' => $r['name'],
                    'email' => $r['email'],
                    'password' => Hash::make($randomPassword), // if you want to expose password for admin, you can store it in logs
                    'level_id' => $r['level']['id'] ?? null,
                    'is_approved' => $r['is_approved'] ?? false,
                ]);
                $user->assignRole($role);
            }

            DB::commit();
            // return redirect()->route('admin.students.index')->with('success', 'Students imported successfully.');
            return redirect()->route('admin.students.index')->with('success', __('messages.students_imported'));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Students import commit failed: ' . $e->getMessage());
            // return redirect()->back()->withErrors(['import' => 'Import commit failed. See logs.']);
            return redirect()->back()->withErrors(['import' => __('messages.import_commit_failed')]);
        }
    }

    public function export(Request $request)
    {
        $filters = $request->only(['q', 'level_id', 'is_approved']);
        $fileName = 'students-' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new StudentsExport($filters), $fileName);
    }
}
