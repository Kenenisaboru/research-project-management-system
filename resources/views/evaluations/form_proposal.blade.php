<form action="{{ route('evaluations.submit', $evaluation->eval_id) }}" method="POST" id="rubricEvaluationFormProposal">
    @csrf

    {{-- 5-Dimension Rubric Inputs --}}
    <div class="p-3 bg-light rounded-3 border mb-3">
        <h6 class="fw-bold mb-3"><i class="bi bi-ui-checks me-1"></i>Proposal Evaluation Rubric</h6>
        <div class="row g-2">
            <div class="col-12">
                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                    <span>1. Methodology &amp; Design</span>
                    <span class="text-muted">(Max 25)</span>
                </label>
                <input type="number" step="0.5" min="0" max="25" name="rubric_methodology" id="rubric_m_sa"
                       class="form-control form-control-sm" required placeholder="0 - 25" oninput="calculateTotalScoreSA()">
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                    <span>2. Literature Review</span>
                    <span class="text-muted">(Max 20)</span>
                </label>
                <input type="number" step="0.5" min="0" max="20" name="rubric_literature" id="rubric_l_sa"
                       class="form-control form-control-sm" required placeholder="0 - 20" oninput="calculateTotalScoreSA()">
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                    <span>3. Feasibility</span>
                    <span class="text-muted">(Max 20)</span>
                </label>
                <input type="number" step="0.5" min="0" max="20" name="rubric_feasibility" id="rubric_f_sa"
                       class="form-control form-control-sm" required placeholder="0 - 20" oninput="calculateTotalScoreSA()">
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                    <span>4. Regional Relevance</span>
                    <span class="text-muted">(Max 20)</span>
                </label>
                <input type="number" step="0.5" min="0" max="20" name="rubric_relevance" id="rubric_r_sa"
                       class="form-control form-control-sm" required placeholder="0 - 20" oninput="calculateTotalScoreSA()">
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                    <span>5. Budget Justification</span>
                    <span class="text-muted">(Max 15)</span>
                </label>
                <input type="number" step="0.5" min="0" max="15" name="rubric_budget" id="rubric_b_sa"
                       class="form-control form-control-sm" required placeholder="0 - 15" oninput="calculateTotalScoreSA()">
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label small fw-bold mb-1">Total Score (0 - 100) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" max="100" name="score" id="totalScoreInputSA"
                   class="form-control fw-bold bg-white text-dark" required placeholder="0.00" readonly>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-bold mb-1">Recommendation <span class="text-danger">*</span></label>
            <select name="decision" class="form-select fw-bold" required>
                <option value="Accepted">Accepted (Fund)</option>
                <option value="AcceptedWithMinorMods">Accepted (Minor Mods)</option>
                <option value="AcceptedWithMajorMods">Accepted (Major Mods)</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold">Constructive Comments &amp; Critique <span class="text-danger">*</span></label>
        <textarea name="comments" rows="4" class="form-control" required placeholder="Provide technical feedback, strengths, and areas of improvement..."></textarea>
    </div>

    <button type="button" class="btn btn-dark w-100 fw-bold confirm-btn" data-confirm-title="Submit Proposal Evaluation" data-confirm-message="This action cannot be undone. Your score and recommendation will be final." data-confirm-icon="bi-send-check" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-dark">
        <i class="bi bi-send-check me-1"></i> Submit Evaluation Score
    </button>
</form>

<script>
function calculateTotalScoreSA() {
    const m = parseFloat(document.getElementById('rubric_m_sa').value) || 0;
    const l = parseFloat(document.getElementById('rubric_l_sa').value) || 0;
    const f = parseFloat(document.getElementById('rubric_f_sa').value) || 0;
    const r = parseFloat(document.getElementById('rubric_r_sa').value) || 0;
    const b = parseFloat(document.getElementById('rubric_b_sa').value) || 0;
    const total = Math.min(100, Math.max(0, m + l + f + r + b));
    document.getElementById('totalScoreInputSA').value = total.toFixed(2);
}
</script>
