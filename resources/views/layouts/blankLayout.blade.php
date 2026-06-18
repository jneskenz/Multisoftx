@extends('layouts.commonMaster')

@section('layoutContent')
  <div class="layout-wrapper layout-without-navbar layout-without-menu">
    <div class="layout-container">
      <!-- Layout page -->
      <div class="layout-page">
        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            @yield('content')
            {{ $slot ?? '' }}
          </div>
          <!-- / Content -->
        </div>
        <!-- / Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>
  </div>
@endsection
