@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-shield-lock-fill me-2"></i>Two-Factor Authentication (2FA)</h5>
                </div>
                <div class="card-body p-4">
                    @if($enabled)
                        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-check-circle-fill fs-3 me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Two-Factor Authentication is currently active on your account</h6>
                                <small>Your account is protected with RFC 6238 TOTP verification.</small>
                            </div>
                        </div>

                        <div class="card border-danger mb-3">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Disable Two-Factor Authentication</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">Disabling 2FA will reduce your account security. Password confirmation is required.</p>
                                <form method="POST" action="{{ route('2fa.disable') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Current Account Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-shield-x me-1"></i>Disable 2FA
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="mb-4">
                            <h6 class="fw-bold">Step 1: Link Authenticator Application</h6>
                            <p class="text-muted small">
                                Open your mobile authenticator app (Google Authenticator, Microsoft Authenticator, Authy, or 1Password),
                                and add a new account using the manual setup key below:
                            </p>

                            <div class="bg-light p-3 rounded mb-3 border">
                                <label class="small text-muted d-block fw-bold mb-1">Manual Entry Secret Key:</label>
                                <code class="fs-5 user-select-all fw-bold text-primary">{{ chunk_split($secret, 4, ' ') }}</code>
                            </div>

                            <div class="alert alert-info py-2 small">
                                <i class="bi bi-info-circle me-1"></i> Account identifier: <strong>{{ $user->email }}</strong> &bull; Issuer: <strong>Gambella University RPMS</strong>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="mb-3">
                            <h6 class="fw-bold">Step 2: Verify Setup & Activate</h6>
                            <p class="text-muted small">Enter the current 6-digit verification code displayed in your authenticator app to confirm and activate 2FA.</p>

                            <form method="POST" action="{{ route('2fa.confirm') }}">
                                @csrf
                                <div class="mb-3" style="max-width: 250px;">
                                    <label for="code" class="form-label">6-Digit Code</label>
                                    <input type="text" class="form-control form-control-lg text-center letter-spacing-2 fw-bold @error('code') is-invalid @enderror"
                                           id="code" name="code" maxlength="6" placeholder="000000" required autofocus autocomplete="off">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check2-circle me-1"></i>Confirm & Activate 2FA
                                </button>
                                <a href="{{ route('profile') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
