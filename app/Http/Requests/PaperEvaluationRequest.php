<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaperEvaluationRequest extends FormRequest
{
    public function authorize()
    {
        return in_array($this->user()->role, ['reviewer', 'coordinator', 'vparttcs', 'rcsc', 'admin']);
    }

    public function rules()
    {
        return [
            'decision' => ['required', 'in:RecommendAccept,RecommendAcceptWithRevisions,Reject'],
            'score'    => ['required', 'numeric', 'min:0', 'max:100'],
            'comments' => ['required', 'string'],
        ];
    }
}