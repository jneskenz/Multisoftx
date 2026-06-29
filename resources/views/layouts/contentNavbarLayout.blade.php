@extends('layouts.commonMaster')

@php
  $isMenu = $isMenu ?? true;
  $isNavbar = $isNavbar ?? true;
  $isFooter = $isFooter ?? true;
  $containerNav = $containerNav ?? 'container-xxl';
@endphp

@section('layoutContent')
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      @if ($isMenu)
        @include('layouts.sections.menu.verticalMenu')
      @endif

      <!-- Layout page -->
      <div class="layout-page">

        <!-- BEGIN: Navbar -->
        @if ($isNavbar)
          @include('layouts.sections.navbar.navbar')
        @endif
        <!-- END: Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="{{ $containerNav }} flex-grow-1 container-p-y">
            @yield('content')
          </div>
          <!-- / Content -->

          <!-- Footer -->
          @if ($isFooter)
            @include('layouts.sections.footer.footer')
          @endif
          <!-- / Footer -->

        </div>
        <!-- / Content wrapper -->

      </div>
      <!-- / Layout page -->

    </div>
  </div>
  <div class="layout-overlay layout-menu-toggle"></div>
  <div class="drag-target"></div>
@endsection
