<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResearchCallRequest extends FormRequest
{
    public function authorize()
    {
        return in_array($this->user()->role, ['coordinator', 'dh', 'admin']);
    }

    public function rules()
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string'],
            'call_type'         => ['required', 'in:project,abstract,community_service'],
            'dept_id'           => ['nullable', 'exists:departments,id'],
            'thematic_id'       => ['nullable', 'exists:thematic_areas,id'],
            'funding_organization' => ['nullable', 'string', 'max:255'],
            'budget_ceiling'    => ['nullable', 'numeric', 'min:0'],
            'submission_deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'status'            => ['nullable', 'in:Draft,Open,Closed'],
        ];
    }
}