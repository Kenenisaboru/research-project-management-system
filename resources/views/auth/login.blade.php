@extends('layouts.app')

@section('content')
<section class="login-frame" aria-labelledby="login-title">
    <div class="login-intro">
        <div class="login-brand">
            <img class="institution-logo" src="{{ asset('images/gambella-university-logo.jpg') }}" alt="Gambella University logo">
            <span>
                <strong>RPMS</strong>
                <small>Research Project Management System</small>
            </span>
        </div>

        <div class="login-intro-copy">
            <span class="login-eyebrow"><i class="bi bi-stars me-2"></i>Research, made manageable</span>
            <h1>Ideas into impact.<br><span>Every step, connected.</span></h1>
            <p>One secure workspace for research proposals, reviews, funding, and project progress.</p>

            <div class="login-benefits">
                <div><i class="bi bi-check2-circle"></i><span>Follow projects from proposal to completion</span></div>
                <div><i class="bi bi-shield-check"></i><span>Keep reviews and approvals in one place</span></div>
                <div><i class="bi bi-graph-up-arrow"></i><span>See progress and funding at a glance</span></div>
            </div>
        </div>

        <div class="login-intro-footer">
            <i class="bi bi-lock-fill me-2"></i>Secure access for the research community
        </div>
    </div>

    <div class="login-panel">
        <div class="login-panel-inner">
            <span class="login-form-kicker">Welcome back</span>
            <h2 id="login-title">Sign in to your account</h2>
            <p class="login-form-description">Enter your university account details to continue.</p>

            @if($errors->any())
            <div class="alert alert-danger login-error" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ $errors->first('email') ?: $errors->first() }}</span>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" autocomplete="on" id="loginForm">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="email">Email address</label>
                    <div class="login-input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               required autofocus placeholder="name@university.edu"
                               value="{{ old('email') }}" autocomplete="username">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label mb-0" for="password">Password</label>
                        <span class="login-help">Need help? Contact your system administrator.</span>
                    </div>
                    <div class="login-input-wrap login-password-wrap mt-2">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password" class="form-control"
                               required placeholder="Enter your password" autocomplete="current-password">
                        <button class="password-toggle" type="button" id="togglePassword"
                                title="Show or hide password" aria-label="Show password">
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="form-check login-remember mb-0" for="remember">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="form-check-label">Remember me</span>
                    </label>
                    <span class="login-secure"><i class="bi bi-shield-lock me-1"></i>Secure sign in</span>
                </div>

                <button type="submit" id="loginBtn" class="btn btn-success w-100 login-submit">
                    <span id="loginText">Sign in</span>
                    <span id="loginSpinner" class="d-none">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Signing in...
                    </span>
                    <i class="bi bi-arrow-right ms-auto" id="loginArrow"></i>
                </button>
            </form>

            @if(app()->environment('local'))
            <div class="login-demo">
                <div class="login-divider"><span>Workflow test accounts</span></div>
                <p class="small text-muted mb-2">Sign in with each role separately, then sign out before continuing to the next approval step.</p>
                <div class="workflow-account-list">
                    <div><span>Principal Investigator</span><code>pi@rpms.local</code></div>
                    <div><span>Team Member</span><code>tm@rpms.local</code></div>
                    <div><span>Department Head</span><code>dh@rpms.local</code></div>
                    <div><span>Research Coordinator</span><code>coordinator@rpms.local</code></div>
                    <div><span>Peer Reviewer</span><code>reviewer@rpms.local</code></div>
                    <div><span>Peer Reviewer 2</span><code>reviewer2@rpms.local</code></div>
                    <div><span>College Dean</span><code>dean@rpms.local</code></div>
                    <div><span>IRERC Ethics</span><code>irerc@rpms.local</code></div>
                    <div><span>Vice President</span><code>vp@rpms.local</code></div>
                    <div><span>RCSC Chair</span><code>rcsc@rpms.local</code></div>
                    <div><span>Finance Office</span><code>finance@rpms.local</code></div>
                    <div><span>System Administrator</span><code>admin@rpms.local</code></div>
                </div>
                <p class="small text-muted mt-2 mb-0">Local test password for these seeded accounts: <code>UNI@Demo1</code>. Do not use demo accounts or passwords in production.</p>
            </div>
            @endif

            <p class="login-legal">By signing in, you agree to use this system in accordance with university policies.</p>
        </div>
    </div>
</section>

<script>
    const passwordToggle = document.getElementById('togglePassword');
    passwordToggle.addEventListener('click', function () {
        const password = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');
        const showing = password.type === 'password';

        password.type = showing ? 'text' : 'password';
        icon.classList.toggle('bi-eye', !showing);
        icon.classList.toggle('bi-eye-slash', showing);
        passwordToggle.setAttribute('aria-label', showing ? 'Hide password' : 'Show password');
    });

    document.getElementById('loginForm').addEventListener('submit', function () {
        document.getElementById('loginText').classList.add('d-none');
        document.getElementById('loginArrow').classList.add('d-none');
        document.getElementById('loginSpinner').classList.remove('d-none');
        document.getElementById('loginBtn').disabled = true;
    });
</script>
@endsection

@section('styles')
<style>
    .login-frame {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(420px, 0.95fr);
        width: min(1120px, calc(100% - 3rem));
        min-height: min(680px, calc(100vh - 3rem));
        margin: 1.5rem auto;
        overflow: hidden;
        border: 1px solid #e2e9e5;
        border-radius: 1.6rem;
        background: #fff;
        box-shadow: 0 28px 80px rgba(20, 52, 42, 0.13);
    }
    .login-intro {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100%;
        overflow: hidden;
        padding: clamp(2rem, 5vw, 4rem);
        color: #fff;
        background:
            radial-gradient(circle at 84% 14%, rgba(113, 201, 156, 0.22), transparent 15rem),
            linear-gradient(145deg, #174e3d 0%, #103b32 58%, #0c302b 100%);
    }
    .login-intro::after {
        position: absolute;
        right: -8rem;
        bottom: -11rem;
        width: 27rem;
        height: 27rem;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        box-shadow: 0 0 0 34px rgba(255, 255, 255, 0.025), 0 0 0 72px rgba(255, 255, 255, 0.02);
        content: "";
        pointer-events: none;
    }
    .login-brand {
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .login-brand strong,
    .login-brand small {
        display: block;
    }
    .login-brand strong {
        font-size: 1.1rem;
        letter-spacing: 0.09em;
    }
    .login-brand small {
        margin-top: 0.1rem;
        color: rgba(255, 255, 255, 0.68);
        font-size: 0.72rem;
    }
    .login-intro-copy {
        z-index: 1;
        max-width: 34rem;
        padding: 3rem 0;
    }
    .login-eyebrow,
    .login-form-kicker {
        display: inline-flex;
        align-items: center;
        color: #c8e5d4;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .login-intro h1 {
        margin: 1.3rem 0 1rem;
        color: #fff;
        font-size: clamp(2.35rem, 4vw, 3.6rem);
        font-weight: 750;
        letter-spacing: -0.055em;
        line-height: 1.08;
    }
    .login-intro h1 span {
        color: #c8e5d4;
    }
    .login-intro-copy > p {
        max-width: 28rem;
        color: rgba(255, 255, 255, 0.72);
        font-size: 1rem;
        line-height: 1.7;
    }
    .login-benefits {
        display: grid;
        gap: 1rem;
        margin-top: 2rem;
    }
    .login-benefits > div {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.9rem;
    }
    .login-benefits i {
        color: #c8e5d4;
        font-size: 1.15rem;
    }
    .login-intro-footer {
        z-index: 1;
        color: rgba(255, 255, 255, 0.63);
        font-size: 0.78rem;
    }
    .login-panel {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(2rem, 5vw, 4.5rem);
        background: #fff;
    }
    .login-panel-inner {
        width: 100%;
        max-width: 390px;
    }
    .login-form-kicker {
        color: #398365;
        letter-spacing: 0.11em;
    }
    .login-panel h2 {
        margin: 0.7rem 0 0.45rem;
        color: #19372e;
        font-size: 1.85rem;
        font-weight: 750;
        letter-spacing: -0.04em;
    }
    .login-form-description {
        margin-bottom: 2rem;
        color: #76837d;
        font-size: 0.91rem;
    }
    .login-panel .form-label {
        margin-bottom: 0.5rem;
        color: #344c42;
        font-size: 0.82rem;
        font-weight: 700;
    }
    .login-input-wrap {
        position: relative;
    }
    .login-input-wrap > i {
        position: absolute;
        z-index: 2;
        top: 50%;
        left: 0.95rem;
        color: #8a9b92;
        transform: translateY(-50%);
    }
    .login-input-wrap .form-control {
        min-height: 50px;
        padding-left: 2.7rem;
        border-color: #dfe7e3;
        border-radius: 0.75rem;
        background: #fbfcfb;
        font-size: 0.88rem;
    }
    .login-input-wrap .form-control:focus {
        background: #fff;
    }
    .login-help {
        max-width: 12rem;
        color: #829088;
        font-size: 0.68rem;
        text-align: right;
    }
    .login-password-wrap .form-control {
        padding-right: 3rem;
    }
    .password-toggle {
        position: absolute;
        z-index: 3;
        top: 50%;
        right: 0.8rem;
        padding: 0.35rem;
        border: 0;
        color: #74847c;
        background: transparent;
        transform: translateY(-50%);
    }
    .login-remember {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        color: #52635a;
        font-size: 0.82rem;
    }
    .login-remember .form-check-input {
        float: none;
        margin: 0;
    }
    .login-secure {
        color: #75857c;
        font-size: 0.75rem;
    }
    .login-submit {
        display: flex;
        min-height: 52px;
        align-items: center;
        padding: 0.75rem 1.05rem;
        border: 0 !important;
        border-radius: 0.78rem;
        background: #17624b !important;
        font-size: 0.92rem;
        box-shadow: 0 8px 18px rgba(23, 98, 75, 0.2);
    }
    .login-submit:hover {
        background: #104b3a !important;
        box-shadow: 0 10px 22px rgba(23, 98, 75, 0.27);
    }
    .login-submit:disabled {
        opacity: 0.75;
    }
    .login-error {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        font-size: 0.83rem;
    }
    .login-demo {
        margin-top: 1.55rem;
    }
    .workflow-account-list {
        display: grid;
        gap: 0.35rem;
        max-height: 190px;
        overflow-y: auto;
        padding: 0.65rem;
        border: 1px solid #e5ebe8;
        border-radius: 0.75rem;
        background: #f8faf9;
    }
    .workflow-account-list > div {
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
        color: #435a4f;
        font-size: 0.72rem;
    }
    .workflow-account-list code {
        color: #17624b;
        font-size: 0.7rem;
        white-space: nowrap;
    }
    .login-divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        color: #92a098;
        font-size: 0.7rem;
    }
    .login-divider::before,
    .login-divider::after {
        height: 1px;
        flex: 1;
        background: #e8eeeb;
        content: "";
    }
    .login-legal {
        margin: 1.6rem 0 0;
        color: #98a49e;
        font-size: 0.68rem;
        line-height: 1.6;
        text-align: center;
    }
    @media (max-width: 767.98px) {
        .login-frame {
            grid-template-columns: 1fr;
            width: min(520px, calc(100% - 1.5rem));
            min-height: 0;
            margin: 0.75rem auto;
            border-radius: 1.2rem;
        }
        .login-intro {
            min-height: 0;
            padding: 1.5rem;
        }
        .login-intro-copy {
            padding: 2rem 0 0.5rem;
        }
        .login-intro h1 {
            margin: 0.9rem 0 0.6rem;
            font-size: 2rem;
        }
        .login-intro-copy > p {
            margin-bottom: 0;
            font-size: 0.87rem;
        }
        .login-benefits,
        .login-intro-footer {
            display: none;
        }
        .login-panel {
            padding: 1.8rem 1.5rem 2rem;
        }
        .login-panel h2 {
            font-size: 1.55rem;
        }
        .login-form-description {
            margin-bottom: 1.5rem;
        }
    }
    @media (max-width: 390px) {
        .login-help {
            max-width: 9rem;
            font-size: 0.63rem;
        }
    }
</style>
@endsection
