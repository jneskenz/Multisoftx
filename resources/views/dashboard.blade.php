@extends('layouts.contentNavbarLayout')

@section('title', __('Dashboard'))

@section('content')
    @if (class_exists('App\Models\Team'))
        <livewire:pages::teams.pending-invitations-modal />
    @endif

    <div class="row">
        <div class="col-lg-4 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 text-muted">{{ __('Total Users') }}</h6>
                            <h3 class="card-text mt-2">0</h3>
                        </div>
                        <div class="text-primary">
                            <i class="icon-base ti tabler-users icon-32px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 text-muted">{{ __('Active Teams') }}</h6>
                            <h3 class="card-text mt-2">0</h3>
                        </div>
                        <div class="text-warning">
                            <i class="icon-base ti tabler-briefcase icon-32px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 text-muted">{{ __('Total Invoices') }}</h6>
                            <h3 class="card-text mt-2">0</h3>
                        </div>
                        <div class="text-success">
                            <i class="icon-base ti tabler-receipt icon-32px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Welcome') }}</h5>
                </div>
                <div class="card-body">
                    <p>{{ __('Welcome to your dashboard!') }} {{ Auth::user()->name }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
