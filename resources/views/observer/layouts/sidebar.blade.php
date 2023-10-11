<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" href="{{ url('/') }}" target="_blank">
      <img src="{{ asset('assets/img/logo-ct.png')}}" class="navbar-brand-img h-100" alt="main_logo">
      <span class="ms-1 font-weight-bold text-white">E-Notulen</span>
    </a>
  </div>
  <hr class="horizontal light mt-0 mb-2">
  <div class="collapse navbar-collapse  w-auto h-75" id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link text-white {{ Request::is('ses/dashboard*') ? 'active bg-gradient-primary' : '' }}" href="{{ route('home') }}">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">dashboard</i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white {{ Request::is('ses/agenda') ? 'active bg-gradient-primary' : '' }}" href="{{ route('ses.agenda') }}">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">question_answer</i>
          </div>
          <span class="nav-link-text ms-1">Rapat</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white {{ Request::is('ses/notes*') ? 'active bg-gradient-primary' : '' }}" href="{{ route('ses.notes', 'ALL') }}">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">table_view</i>
          </div>
          <span class="nav-link-text ms-1">Notulen</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white {{ Request::is('ses/action-items*') ? 'active bg-gradient-primary' : '' }}" href="{{ route('ses.action_items','BPS') }}">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">engineering</i>
          </div>
          <span class="nav-link-text ms-1">Action Items</span>
        </a>
      </li>      
    </ul>
  </div>
  {{-- <div class="sidenav-footer position-absolute w-100 bottom-0 ">
    <div class="mx-3">
      <a class="btn bg-gradient-primary mt-4 w-100" href="https://www.creative-tim.com/product/material-dashboard-pro?ref=sidebarfree" type="button">Upgrade to pro</a>
    </div>
  </div> --}}
</aside>