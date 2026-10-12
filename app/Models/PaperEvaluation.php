<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaperEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_id',
        'evaluator_id',
        'decision',
        'score',
        'comments',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}