@extends('layouts.blankLayout')

@section('title', __('Log in'))

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">
                <!-- Login Card -->
                <div class="card" style="width: 400px;">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand text-center mb-4">
                            <a href="{{ url('/') }}" class="app-brand-link">
                                <span class="app-brand-text fw-bold" style="font-size: 1.5rem;">{{ config('app.name', 'CRM') }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->

                        <h4 class="mb-1">{{ __('Welcome back!') }} 👋</h4>
                        <p class="mb-4 text-muted">{{ __('Please sign-in to your account and start the adventure') }}</p>

                        <!-- Session Status -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <div>
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login.store') }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label class="form-label" for="email">{{ __('Email address') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required autofocus />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">{{ __('Password') }}</label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" style="font-size: 0.875rem;">
                                            {{ __('Forgot password?') }}
                                        </a>
                                    @endif
                                </div>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }} />
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember me') }}
                                    </label>
                                </div>
                            </div>

                            <!-- Login Button -->
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">
                                    {{ __('Log in') }}
                                </button>
                            </div>
                        </form>

                        <p class="text-center">
                            <span>{{ __('New on our platform?') }}</span>
                            <a href="{{ route('register') }}">
                                <span>{{ __('Create account') }}</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- /Login Card -->
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
