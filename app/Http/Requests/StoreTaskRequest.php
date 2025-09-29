<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request class for validating and authorizing task creation.
 */
class StoreTaskRequest extends FormRequest
{
    /**
     * Only managers can create tasks (route also has role:manager middleware,
     * this is a second safety net).
     */
    public function authorize(): bool
    {
        return (bool) ($this->user()?->role === 'manager');
    }

    /**
     * Validation rules for creating a task.
     * Project comes from the route (projects/{project}/tasks),
     * so we don't validate project_id here.
     */
    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'status'       => ['nullable', 'in:pending,in-progress,done'], // default is pending if omitted
            'due_date'     => ['nullable', 'date'],
            'assigned_to'  => ['nullable', 'exists:users,id'],
        ];
    }

    /**
     * Optional: nice names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'assigned_to' => 'assignee',
        ];
    }

    /**
     * Optional: trim strings before validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('title')) {
            $this->merge(['title' => trim((string) $this->input('title'))]);
        }
        if ($this->has('description')) {
            $this->merge(['description' => trim((string) $this->input('description'))]);
        }
    }
}
