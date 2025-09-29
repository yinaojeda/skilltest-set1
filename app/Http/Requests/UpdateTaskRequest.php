<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title'       => 'sometimes|string',
            'description' => 'sometimes|nullable|string',
            'status'      => 'sometimes|in:pending,in-progress,done,completed',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'due_date'    => 'sometimes|date',
        ];
    }
}
