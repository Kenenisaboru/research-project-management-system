<form action="{{ route('evaluations.submit', $evaluation->eval_id) }}" method="POST" id="rubricEvaluationFormProgress">
    @csrf

    <div class="p-3 bg-light rounded-3 border mb-3">
        <h6 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow me-1 text-info"></i>Progress Report Review</h6>
        
        <div class="mb-3">
            <label class="form-label small fw-bold mb-1">Performance Score (0 - 100, Optional)</label>
            <input type="number" step="0.5" min="0" max="100" name="score" class="form-control" placeholder="Optional score (e.g. 85)">
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold mb-1">Progress Assessment Verdict <span class="text-danger">*</span></label>
            <select name="decision" class="form-select fw-bold" required>
                <option value="Satisfactory">Satisfactory (On Track)</option>
                <option value="NeedsImprovement">Needs Improvement (Action Plan Required)</option>
                <option value="Unsatisfactory">Unsatisfactory (At Risk)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Evaluation Remarks &amp; Milestones Review <span class="text-danger">*</span></label>
            <textarea name="comments" rows="5" class="form-control" required placeholder="Assess progress against deliverables, timeline compliance, and milestone outputs (minimum 10 characters)..."></textarea>
        </div>
    </div>

    <button type="button" class="btn btn-info text-white w-100 fw-bold confirm-btn" data-confirm-title="Submit Progress Review" data-confirm-message="Submit this progress evaluation for project records?" data-confirm-icon="bi-check2-circle" data-confirm-color="text-info" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-info text-white">
        <i class="bi bi-send-check me-1"></i> Submit Progress Evaluation
    </button>
</form>
