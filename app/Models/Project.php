<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_id';

    protected $fillable = [
        'title',
        'abstract_text',
        'thematic_id',
        'pi_id',
        'dept_id',
        'requested_budget',
        'approved_budget',
        'status',
        'current_stage',
        'ethical_cleared',
        'proposal_document_url',
        'contract_signed_at',
        'pi_signature_date',
        'vp_signature_date',
        'feedback',
        'dh_screened_at',
        'under_review_at',
        'approved_at',
        'activated_at',
        'completed_at',
        'cancelled_at',
        'cancelled_by_pi',
        'cancellation_reason',
        'admin_cancel_notes',
        'project_code',
    ];

    protected $casts = [
        'ethical_cleared' => 'boolean',
        'contract_signed_at' => 'datetime',
        'pi_signature_date' => 'datetime',
        'vp_signature_date' => 'datetime',
        'dh_screened_at' => 'datetime',
        'under_review_at' => 'datetime',
        'approved_at' => 'datetime',
        'activated_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Bootstrap model event hooks.
     *
     * Auto-generates a unique project_code in the format UNI-PRJ-{YEAR}-{NNNNN}
     * whenever a new Project record is created without one already set.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $project): void {
            if (empty($project->project_code)) {
                $year      = now()->format('Y');
                $lastCode  = static::where('project_code', 'like', "UNI-PRJ-{$year}-%")
                    ->orderByDesc('project_code')
                    ->value('project_code');

                $seq = $lastCode
                    ? ((int) substr($lastCode, -5)) + 1
                    : 1;

                $project->project_code = sprintf('UNI-PRJ-%s-%05d', $year, $seq);
            }
        });
    }

    public function pi()
    {
        return $this->belongsTo(User::class, 'pi_id');
    }

    public function thematicArea()
    {
        return $this->belongsTo(ThematicArea::class, 'thematic_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'project_id', 'project_id');
    }

    public function teamMembers()
    {
        return $this->members();
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'project_id', 'project_id');
    }

    public function irercClearance()
    {
        return $this->hasOne(IRERCClearance::class, 'project_id', 'project_id');
    }

    public function budgetRequests()
    {
        return $this->hasMany(BudgetRequest::class, 'project_id', 'project_id');
    }

    public function budgetAmendments()
    {
        return $this->hasMany(BudgetAmendment::class, 'project_id', 'project_id');
    }

    public function milestoneReports()
    {
        return $this->hasMany(MilestoneReport::class, 'project_id', 'project_id');
    }

    public function extensions()
    {
        return $this->hasMany(ProjectExtension::class, 'project_id', 'project_id');
    }

    public function piTransfers()
    {
        return $this->hasMany(PITransfer::class, 'project_id', 'project_id');
    }

    public function termination()
    {
        return $this->hasOne(ProjectTermination::class, 'project_id', 'project_id');
    }

    public function procurementRequests()
    {
        return $this->hasMany(ProcurementRequest::class, 'project_id', 'project_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'project_id', 'project_id');
    }

    public function documents()
    {
        return $this->hasMany(ProjectDocument::class, 'project_id', 'project_id');
    }
}

