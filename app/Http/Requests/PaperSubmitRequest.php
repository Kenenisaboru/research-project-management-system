<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaperSubmitRequest extends FormRequest
{
    public function authorize()
    {
        return in_array($this->user()->role, ['pi', 'tm', 'admin']);
    }

    public function rules()
    {
        return [
            'title'             => ['required', 'string', 'max:500'],
            'abstract_text'     => ['required', 'string'],
            'authors'           => ['required', 'string', 'max:1000'],
            'project_id'        => ['nullable', 'exists:projects,project_id'],
            'journal_name'      => ['nullable', 'string', 'max:255'],
            'journal_index'     => ['nullable', 'in:SCIE,SSCI,Scopus,ESCI,National,None'],
            'publication_date'  => ['nullable', 'date', 'before_or_equal:today'],
            'paper_file'        => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ];
    }
}