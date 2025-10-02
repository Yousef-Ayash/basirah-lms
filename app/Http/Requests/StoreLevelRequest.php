<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Level;

class StoreLevelRequest extends FormRequest
{
    public function authorize()
    {
        // keep the admin check you already use elsewhere
        return $this->user() && $this->user()->hasRole('admin');
    }

    /**
     * Prepare the input for validation.
     *
     * - Treat NULL, empty string or 0 as "auto-assign".
     * - On create: auto-assign to (max(order) + 1) or 1 if none.
     * - On update: if order left blank/0 we keep the current level->order (so editing without changing order won't move it).
     */
    protected function prepareForValidation()
    {
        $order = $this->input('order');

        // detect route model / id for update (if any)
        $routeLevel = $this->route('level');

        // normalize numeric id to Level model if possible
        $currentLevel = null;
        if ($routeLevel instanceof Level) {
            $currentLevel = $routeLevel;
        } elseif (is_numeric($routeLevel)) {
            $currentLevel = Level::find($routeLevel);
        }

        if ($order === null || $order === '' || intval($order) === 0) {
            if ($currentLevel) {
                // update: keep the existing order if possible (avoid moving it)
                $assigned = $currentLevel->order ?? (Level::max('order') ?? 0) + 1;
            } else {
                // create: assign to end (max + 1)
                $max = Level::max('order');
                $assigned = is_null($max) ? 1 : ($max + 1);
            }

            $this->merge(['order' => $assigned]);
        } else {
            // ensure stored as integer
            $this->merge(['order' => intval($order)]);
        }
    }

    /**
     * Validation rules.
     * - name: required
     * - order: required integer >= 1, unique in levels.order (when updating we ignore the current model id)
     */
    public function rules()
    {
        $routeLevel = $this->route('level');
        $ignoreId = null;

        if ($routeLevel instanceof Level) {
            $ignoreId = $routeLevel->id;
        } elseif (is_numeric($routeLevel)) {
            $ignoreId = (int) $routeLevel;
        }

        $uniqueRule = Rule::unique('levels', 'order');
        if ($ignoreId) {
            $uniqueRule = $uniqueRule->ignore($ignoreId);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'order' => ['required', 'integer', 'min:1', $uniqueRule],
        ];
    }

    /**
     * Optional: custom messages (uncomment/edit for translation)
     */
    // public function messages()
    // {
    //     return [
    //         'order.unique' => 'This order value is already taken. Choose another or leave blank to auto-assign.',
    //         'order.min'    => 'Order must be at least 1.',
    //     ];
    // }
}
