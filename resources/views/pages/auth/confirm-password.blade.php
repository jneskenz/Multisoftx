@extends('layouts.blankLayout')

@section('title', __('Confirm password'))

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">
                <!-- Confirm Password Card -->
                <div class="card" style="width: 400px;">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand text-center mb-4">
                            <a href="{{ url('/') }}" class="app-brand-link">
                                <span class="app-brand-text fw-bold" style="font-size: 1.5rem;">{{ config('app.name', 'CRM') }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->

                        <h4 class="mb-1">{{ __('Confirm password') }}</h4>
                        <p class="mb-4 text-muted">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>

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

                        <!-- Confirm Password Form -->
                        <form method="POST" action="{{ route('password.confirm.store') }}">
                            @csrf

                            <!-- Password -->
                            <div class="mb-3">
                                <label class="form-label" for="password">{{ __('Password') }}</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">
                                    {{ __('Confirm') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Confirm Password Card -->
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
