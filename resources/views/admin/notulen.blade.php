@extends('admin.layouts.template')
@section('title', 'Tabel - '.config('app.name'))
@section('breadcrumbs', 'Tabel - '.config('app.name'))

@section('content')
<div class="row">
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
  <div class="card">
  <div class="card-header p-3 pt-2">
  <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
  <i class="material-icons opacity-10">supervisor_account</i>
  </div>
  <div class="text-end pt-1">
  <p class="text-sm mb-0 text-capitalize">Users</p>
  <h4 class="mb-0">{{ $users }}</h4>
  </div>
  </div>
  <hr class="dark horizontal my-0">
  <div class="card-footer p-3">
  <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than lask week</p>
  </div>
  </div>
  </div>
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
  <div class="card">
  <div class="card-header p-3 pt-2">
  <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
  <i class="material-icons opacity-10">file_copy</i>
  </div>
  <div class="text-end pt-1">
  <p class="text-sm mb-0 text-capitalize">Notulen</p>
  <h4 class="mb-0">{{ $notes }}</h4>
  </div>
  </div>
  <hr class="dark horizontal my-0">
  <div class="card-footer p-3">
  <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+3% </span>than lask month</p>
  </div>
  </div>
  </div>
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
  <div class="card">
  <div class="card-header p-3 pt-2">
  <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
  <i class="material-icons opacity-10">calendar_month</i>
  </div>
  <div class="text-end pt-1">
  <p class="text-sm mb-0 text-capitalize">Agenda</p>
  <h4 class="mb-0">{{ $agendas }}</h4>
  </div>
  </div>
  <hr class="dark horizontal my-0">
  <div class="card-footer p-3">
  <p class="mb-0"><span class="text-danger text-sm font-weight-bolder">-2%</span> than yesterday</p>
  </div>
  </div>
  </div>
  <div class="col-xl-3 col-sm-6">
  <div class="card">
  <div class="card-header p-3 pt-2">
  <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
  <i class="material-icons opacity-10">engineering</i>
  </div>
  <div class="text-end pt-1">
  <p class="text-sm mb-0 text-capitalize">Action Item</p>
  <h4 class="mb-0">{{ $actions }}</h4>
  </div>
  </div>
  <hr class="dark horizontal my-0">
  <div class="card-footer p-3">
  <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+5% </span>than yesterday</p>
  </div>
  </div>
  </div>
  </div>

@endsection

@section('script')

@endsection