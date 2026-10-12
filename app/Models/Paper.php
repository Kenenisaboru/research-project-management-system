<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'submitter_id',
        'title',
        'abstract_text',
        'authors',
        'journal_name',
        'journal_index',
        'paper_file_path',
        'status',
        'evaluation_summary',
        'publication_date',
        'award_status',
    ];

    protected $casts = [
        'publication_date' => 'date',
    ];

    protected $appends = ['average_score'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    public function evaluations()
    {
        return $this->hasMany(PaperEvaluation::class, 'paper_id');
    }

    public function awards()
    {
        return $this->hasMany(Award::class, 'paper_id');
    }

    public function getAverageScoreAttribute()
    {
        $scores = $this->evaluations->pluck('score')->filter();

        return $scores->isEmpty() ? null : round($scores->avg(), 1);
    }
}