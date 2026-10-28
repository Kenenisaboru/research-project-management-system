<form action="{{ route('evaluations.submit', $evaluation->eval_id) }}" method="POST" id="rubricEvaluationFormFinal">
    @csrf

    <div class="p-3 bg-light rounded-3 border mb-3">
        <h6 class="fw-bold mb-3"><i class="bi bi-mortarboard me-1 text-success"></i>Final Project Defence Examination</h6>

        <div class="mb-3">
            <label class="form-label small fw-bold mb-1">Final Defence Score (0 - 100, Optional)</label>
            <input type="number" step="0.5" min="0" max="100" name="score" class="form-control" placeholder="0 - 100">
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold mb-2">Examiner Final Decision <span class="text-danger">*</span></label>
            <div class="d-flex flex-column gap-2">
                <div class="form-check p-2 border rounded bg-white">
                    <input class="form-check-input ms-1 me-2" type="radio" name="final_decision" id="decision_accepted" value="Accepted" required>
                    <label class="form-check-label fw-bold text-success" for="decision_accepted">
                        <i class="bi bi-check-circle-fill me-1"></i> Accepted (Pass without condition)
                    </label>
                </div>
                <div class="form-check p-2 border rounded bg-white">
                    <input class="form-check-input ms-1 me-2" type="radio" name="final_decision" id="decision_minor" value="AcceptedWithMinorModification" required>
                    <label class="form-check-label fw-bold text-warning" for="decision_minor">
                        <i class="bi bi-pencil-square me-1"></i> Accepted with Minor Modification
                    </label>
                </div>
                <div class="form-check p-2 border rounded bg-white">
                    <input class="form-check-input ms-1 me-2" type="radio" name="final_decision" id="decision_rejected" value="Rejected" required>
                    <label class="form-check-label fw-bold text-danger" for="decision_rejected">
                        <i class="bi bi-x-circle-fill me-1"></i> Rejected (Fail / Significant Deficiency)
                    </label>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Required Revisions / Corrections (if any)</label>
            <textarea name="revision_comments" rows="3" class="form-control" placeholder="Specify any corrections or revisions the researcher must complete before final archiving..."></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Examiner General Comments &amp; Remarks</label>
            <textarea name="comments" rows="3" class="form-control" placeholder="Overall appraisal of the project defence presentation, deliverables, and contributions..."></textarea>
        </div>
    </div>

    <button type="button" class="btn btn-success w-100 fw-bold confirm-btn" data-confirm-title="Submit Final Defence Evaluation" data-confirm-message="Are you sure you want to finalize this defence decision? This decision will be permanently recorded." data-confirm-icon="bi-award" data-confirm-color="text-success" data-confirm-btn-text="Yes, Finalize" data-confirm-btn-class="btn-success">
        <i class="bi bi-award me-1"></i> Submit Final Defence Verdict
    </button>
</form>
