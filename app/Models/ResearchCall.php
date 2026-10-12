<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'call_type',
        'dept_id',
        'thematic_id',
        'funding_organization',
        'budget_ceiling',
        'submission_deadline',
        'status',
        'posted_by',
    ];

    protected $casts = [
        'budget_ceiling'      => 'decimal:2',
        'submission_deadline' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function thematicArea()
    {
        return $this->belongsTo(ThematicArea::class, 'thematic_id');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Open')
            ->where(function ($q) {
                $q->whereNull('submission_deadline')
                    ->orWhereDate('submission_deadline', '>=', now()->toDateString());
            });
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    public function isManageable(): bool
    {
        return in_array(\Auth::user()->role, ['coordinator', 'dh', 'admin']);
    }
}