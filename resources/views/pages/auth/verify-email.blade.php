@extends('layouts.blankLayout')

@section('title', __('Email verification'))

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">
                <!-- Email Verification Card -->
                <div class="card" style="width: 400px;">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand text-center mb-4">
                            <a href="{{ url('/') }}" class="app-brand-link">
                                <span class="app-brand-text fw-bold" style="font-size: 1.5rem;">{{ config('app.name', 'CRM') }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->

                        <h4 class="mb-1">{{ __('Email verification') }}</h4>
                        <p class="mb-4 text-muted">{{ __('Please verify your email address by clicking on the link we just emailed to you.') }}</p>

                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="d-flex flex-column gap-3">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    {{ __('Resend verification email') }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary w-100">
                                    {{ __('Log out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /Email Verification Card -->
            </div>
        </div>
    </div>

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .authentication-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .authentication-inner {
            width: 100%;
        }

        .card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            margin: 0 auto;
        }
    </style>
@endsection
