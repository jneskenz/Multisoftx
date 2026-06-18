@extends('layouts.blankLayout')

@section('title', __('Reset password'))

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">
                <!-- Reset Password Card -->
                <div class="card" style="width: 400px;">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand text-center mb-4">
                            <a href="{{ url('/') }}" class="app-brand-link">
                                <span class="app-brand-text fw-bold" style="font-size: 1.5rem;">{{ config('app.name', 'CRM') }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->

                        <h4 class="mb-1">{{ __('Reset password') }}</h4>
                        <p class="mb-4 text-muted">{{ __('Please enter your new password below') }}</p>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Reset Password Form -->
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <!-- Token -->
                            <input type="hidden" name="token" value="{{ request()->route('token') }}">

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label class="form-label" for="email">{{ __('Email address') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ request('email') }}" placeholder="john@example.com" required />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label class="form-label" for="password">{{ __('Password') }}</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label class="form-label" for="password_confirmation">{{ __('Confirm password') }}</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required />
                            </div>

                            <!-- Submit Button -->
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">
                                    {{ __('Reset password') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Reset Password Card -->
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
