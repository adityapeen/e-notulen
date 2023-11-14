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
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Pegawai</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tasks</th>
                  <th class="text-uppercase text-secondary opacity-7 text-xxs font-weight-bolder opacity-7">Performance</th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($data as $item)
                <tr>
                  <td>
                    <div class="d-flex">
                      <div class="col-md-1 text-center">
                        <i class="fa fa-user-tag"></i>
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ $item->user->name }}</h6>
                        
                      </div>
                    </div>
                  </td>
                  <td class="align-middle text-sm">
                    <span class="badge badge-sm bg-gradient-danger" title="To Do">{{ $item->todo_count}}</span>
                    <span class="badge badge-sm bg-gradient-info" title="On Progress">{{ $item->onprogress_count}}</span>
                    <span class="badge badge-sm bg-gradient-success" title="Done">{{ $item->done_count}}</span>
                    <span class="badge badge-sm bg-gradient-dark" title="Total">{{ $item->todo_count+$item->onprogress_count+$item->done_count}}</span>
                </td>
                <td>
                      <span class="badge badge-sm bg-gradient-warning" title="Total">{{ round($item->performance_avg,2) }}%</span>
                  </td>
                  
                  <td class="align-middle">
                    <a href="{{ route('admin.performance.detail', [$item->user_hash()] ) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Edit group">
                      <button class="btn btn-sm btn-dark"><i class="fas fa-chart-line"></i> Detail</button>
                    </a>
                  </td>
                </tr>
                @endforeach
                @if(sizeof($data) == 0)
                <tr>
                  <td colspan="2" class="text-center">
                    Belum ada Group
                  </td>
                </tr>
                @endif                                
              </tbody>
            </table>
            <form id="delete-form" action="" method="post">
              @method("DELETE")
              @csrf
            </form>
          </div>
          <div class="p-3">
            {{ $data->links('vendor.pagination.bootstrap-5') }}
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>

</script>
@endsection