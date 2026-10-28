@extends('layouts.app')

@section('content')
<section class="login-frame" aria-labelledby="challenge-title">
    <div class="login-intro">
        <div class="login-brand">
            <img class="institution-logo" src="{{ asset('images/gambella-university-logo.jpg') }}" alt="Gambella University logo">
            <span>
                <strong>RPMS</strong>
                <small>Institutional Security Gateway</small>
            </span>
        </div>

        <div class="login-intro-copy">
            <span class="login-eyebrow"><i class="bi bi-shield-check me-2"></i>Two-Factor Authentication</span>
            <h1>Protecting Academic &<br><span>Financial Records.</span></h1>
            <p>Institutional policy enforces Two-Factor Authentication for administrative and financial operations to protect research grants.</p>
        </div>

        <div class="login-intro-footer">
            <i class="bi bi-lock-fill me-2"></i>RFC 6238 TOTP Security Protocol
        </div>
    </div>

    <div class="login-panel">
        <div class="login-panel-inner">
            <span class="login-form-kicker">Identity Verification</span>
            <h2 id="challenge-title">Enter Security Code</h2>
            <p class="login-form-description">Enter the 6-digit verification code from your authenticator application (Google Authenticator, Microsoft Authenticator, Authy).</p>

            @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('2fa.verify') }}" autocomplete="off">
                @csrf

                <div class="mb-3" id="codeSection">
                    <label class="form-label" for="code">6-Digit Authenticator Code</label>
                    <div class="login-input-wrap">
                        <i class="bi bi-phone"></i>
                        <input type="text" id="code" name="code"
                               class="form-control text-center fs-4 fw-bold letter-spacing-2"
                               maxlength="6" autofocus placeholder="123456"
                               autocomplete="one-time-code">
                    </div>
                </div>

                <div class="mb-3 d-none" id="recoverySection">
                    <label class="form-label" for="recovery_code">Emergency Recovery Code</label>
                    <div class="login-input-wrap">
                        <i class="bi bi-key"></i>
                        <input type="text" id="recovery_code" name="recovery_code"
                               class="form-control text-center font-monospace"
                               placeholder="XXXXX-XXXXX">
                    </div>
                    <small class="text-muted d-block mt-1">Single-use emergency recovery codes can be used if you lost access to your device.</small>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-shield-check me-2"></i>Verify & Continue
                    </button>
                </div>

                <div class="text-center">
                    <button type="button" class="btn btn-link btn-sm text-decoration-none" id="toggleRecoveryBtn" onclick="toggleRecovery()">
                        Use an emergency recovery code instead
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
function toggleRecovery() {
    const codeSec = document.getElementById('codeSection');
    const recSec = document.getElementById('recoverySection');
    const btn = document.getElementById('toggleRecoveryBtn');

    if (recSec.classList.contains('d-none')) {
        recSec.classList.remove('d-none');
        codeSec.classList.add('d-none');
        btn.innerText = 'Use 6-digit authenticator code instead';
        document.getElementById('recovery_code').focus();
    } else {
        recSec.classList.add('d-none');
        codeSec.classList.remove('d-none');
        btn.innerText = 'Use an emergency recovery code instead';
        document.getElementById('code').focus();
    }
}
</script>
@endsection
