<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLevelRequest;
use App\Models\Level;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LevelController extends Controller
{
    // GET /admin/levels
    public function index(Request $request)
    {
        $q = $request->get('q');
        $query = Level::query();
        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }
        $levels = $query->orderBy('order')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Levels/Index', [
            'levels' => $levels,
            'filters' => ['q' => $q],
        ]);
    }

    // GET /admin/levels/create
    public function create()
    {
        return Inertia::render('Admin/Levels/Create');
    }

    // POST /admin/levels
    public function store(StoreLevelRequest $request)
    {
        Level::create($request->validated());

        return redirect()->route('admin.levels.index')
            ->with('success', __('messages.item_created', ['item' => __('common.level')]));
    }

    // GET /admin/levels/{level}/edit
    public function edit(Level $level)
    {
        return Inertia::render('Admin/Levels/Edit', ['level' => $level]);
    }

    // PUT /admin/levels/{level}
    public function update(StoreLevelRequest $request, Level $level)
    {
        $level->update($request->validated());

        return redirect()->route('admin.levels.index')
            ->with('success', __('messages.item_updated', ['item' => __('common.level')]));
    }

    // DELETE /admin/levels/{level}
    public function destroy(Level $level)
    {
        $level->delete(); // subjects.level_id will be nulled due to migration FK ->nullOnDelete
        return redirect()->route('admin.levels.index')
            ->with('success', __('messages.item_removed', ['item' => __('common.level')]));
    }
}
