<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
    <div class="container-fluid py-1 px-3">
      <nav aria-label="breadcrumb">
        <h6 class="font-weight-bolder mb-0">@yield('breadcrumbs')</h6>
      </nav>
      <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
          {{-- <div class="input-group input-group-outline">
            <label class="form-label">Type here...</label>
            <input type="text" class="form-control">
          </div> --}}
        </div>
        <li class="nav-item d-xl-none pe-3 d-flex align-items-center">
          <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
          <div class="sidenav-toggler-inner">
          <i class="sidenav-toggler-line"></i>
          <i class="sidenav-toggler-line"></i>
          <i class="sidenav-toggler-line"></i>
          </div>
          </a>
        </li>
       
        
        <li class="nav-item dropdown pe-2 d-flex align-items-center">
            <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-user me-sm-1 cursor-pointer"></i>
                <span class="d-sm-inline d-none">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu  dropdown-menu-end  px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
              <div class="text-center font-size-sm"><small>{{ auth()->user()->level->name}}</small></div>
              <li class="mb-2">
                <a class="dropdown-item border-radius-md" href="{{ auth()->user()->level_id < 3 ? route('admin.users.password'): route('user.password') }}">
                  <div class="d-flex py-1">
                    <div class="my-auto">
                      <img src="{{ asset('assets/img/password.png') }}" class="avatar avatar-sm  me-3 ">
                    </div>
                    <div class="d-flex flex-column justify-content-center">    
                      <h6 class="text-sm font-weight-normal mb-1">
                        <span class="font-weight-bold">
                        {{ __('Change Password') }}
                        </span>                        
                      </h6>
                    </div>
                  </div>
                </a>
              </li>
              <li class="mb-2">
                <a class="dropdown-item border-radius-md" href="javascript:;">
                  <div class="d-flex py-1" onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();">
                    <div class="my-auto">
                      <img src="{{ asset('assets/img/user.png') }}" class="avatar avatar-sm  me-3 ">
                    </div>
                    <div class="d-flex flex-column justify-content-center">    
                      <h6 class="text-sm font-weight-normal mb-1">
                        <span class="font-weight-bold" href="{{ route('logout') }}"                                       >
                                        {{ __('Logout') }}
                      </span>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                        
                      </h6>
                    </div>
                  </div>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>