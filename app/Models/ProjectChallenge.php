<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'pi_id',
        'challenge_type',
        'description',
        'proposed_solution',
        'status',
        'coordinator_response',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function pi()
    {
        return $this->belongsTo(User::class, 'pi_id');
    }
}
