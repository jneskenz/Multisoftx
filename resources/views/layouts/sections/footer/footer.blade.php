<!-- Footer -->
@php
  $currentTeam = request()->route('current_team') ?? Auth::user()?->currentTeam;
  $dashboardUrl = $currentTeam ? route('dashboard', $currentTeam) : url('/');
@endphp

<footer class="content-footer footer bg-footer-theme">
  <div class="container-xxl">
    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
      <div class="text-body">
        &copy;
        {{ date('Y') }}
        {{ config('app.name', 'CRM') }}. {{ __('All rights reserved') }}.
      </div>
      <div class="d-none d-lg-inline-block">
        <a href="{{ $dashboardUrl }}" class="footer-link me-3">{{ __('Dashboard') }}</a>
        @if (Route::has('profile.edit'))
          <a href="{{ route('profile.edit') }}" class="footer-link">{{ __('Settings') }}</a>
        @endif
      </div>
    </div>
  </div>
</footer>
<!-- / Footer -->
