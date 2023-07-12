@extends('admin.layouts.template')
@section('title', $title.' - '.config('app.name'))
@section('breadcrumbs', $title.' - '.config('app.name'))

@section('content')
<div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-info shadow-info border-radius-lg pt-2 pb-2 d-flex align-items-center">
            <h6 class="text-white text-capitalize ps-3">{{$title}}</h6>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Level</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($levels as $item)
                <tr>
                  <td>
                    <h6 class="mb-0 text-sm ps-3">{{ $item->id }}</h6>
                  </td>
                  <td>
                    <h6 class="mb-0 text-sm">{{ $item->name }}</h6>
                  </td>                  
                  <td class="align-middle">
                    <a href="{{ route('admin.levels.edit', [$item->id] ) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Edit group">
                      <button class="btn btn-sm btn-success"><i class="fa fa-edit"></i></button>
                    </a>
                  </td>
                </tr>
                @endforeach
                @if(sizeof($levels) == 0)
                <tr>
                  <td colspan="2" class="text-center">
                    Belum ada Level
                  </td>
                </tr>
                @endif                                
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@endsection