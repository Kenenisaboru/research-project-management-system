@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 border-top border-4 border-warning">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-check text-success fs-1"></i>
                        <h4 class="mt-2 fw-bold">Two-Factor Authentication Activated!</h4>
                        <p class="text-muted">Save your emergency recovery codes in a secure location.</p>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Important:</strong> Each recovery code can only be used once. If you lose access to your authenticator app, these codes are the only way to recover access to your account.
                    </div>

                    <div class="bg-light p-3 rounded mb-4 border">
                        <div class="row g-2">
                            @foreach($recoveryCodes as $code)
                                <div class="col-6">
                                    <code class="fs-6 d-block py-1 text-center bg-white border rounded user-select-all fw-bold text-dark">{{ $code }}</code>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('profile') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-lg me-1"></i>I Have Saved These Codes &mdash; Return to Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
