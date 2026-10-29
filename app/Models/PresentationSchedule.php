<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresentationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'type',
        'scheduled_at',
        'venue',
        'notes',
        'arranged_by',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function arranger()
    {
        return $this->belongsTo(User::class, 'arranged_by');
    }

    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'InitialDefence'  => 'Initial Project Defence',
            'BudgetApproval'  => 'RCSC Budget Approval Meeting',
            'ProgressReport'  => 'Project Progress Report',
            'FinalDefence'    => 'Final Project Defence',
            default           => $this->type,
        };
    }

    public function getTypeBadgeColorAttribute(): string
    {
        return match($this->type) {
            'InitialDefence'  => 'primary',
            'BudgetApproval'  => 'warning',
            'ProgressReport'  => 'info',
            'FinalDefence'    => 'success',
            default           => 'secondary',
        };
    }
}
