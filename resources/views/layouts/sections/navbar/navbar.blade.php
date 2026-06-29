<!-- Navbar -->
@php
  $profileUrl = Route::has('profile.edit') ? route('profile.edit') : 'javascript:void(0);';
@endphp

<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
      <i class="icon-base ti tabler-menu-2 icon-md"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center justify-content-end gap-3" id="navbar-collapse">
    @auth
      <livewire:team-switcher />
    @endauth
    <ul class="navbar-nav flex-row align-items-center ms-auto">
      @auth
        <li x-data="{ open: false }" class="nav-item navbar-dropdown dropdown-user dropdown">
          <a @click="open = !open" @click.away="open = false" class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" role="button">
            <div class="avatar avatar-online">
              <span class="avatar-initial rounded-circle bg-label-primary">{{ Auth::user()->initials() }}</span>
            </div>
          </a>
          <ul x-show="open" x-transition class="dropdown-menu dropdown-menu-end show" style="position:absolute;">
            <li>
              <a class="dropdown-item" href="{{ $profileUrl }}">
                <div class="d-flex">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                      <span class="avatar-initial rounded-circle bg-label-primary">{{ Auth::user()->initials() }}</span>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <span class="fw-medium d-block">{{ Auth::user()->name }}</span>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                  </div>
                </div>
              </a>
            </li>
            <li><div class="dropdown-divider my-1"></div></li>
            @if (Route::has('profile.edit'))
              <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                  <i class="icon-base ti tabler-user me-3 icon-md"></i>
                  <span>{{ __('Perfil') }}</span>
                </a>
              </li>
            @endif
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">
                  <i class="icon-base ti tabler-logout me-3 icon-md"></i>
                  <span>{{ __('Cerrar Sesión') }}</span>
                </button>
              </form>
            </li>
          </ul>
        </li>
      @endauth
    </ul>
  </div>
</nav>
<!-- / Navbar -->
