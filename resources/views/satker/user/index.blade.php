@extends('satker.layouts.template')
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
        <div class="card-body pb-2">
          <div class="table-responsive">
            <table class="table align-items-center mb-0" id="myTable">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Level</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Bidang</th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($users as $item)
                <tr>
                  <td class="">
                    <h6 class="mb-0">{{ $item->name }}
                      @if($item->phone != NULL)
                      <i class="fa fa-whatsapp text-success"></i>
                      @endif
                    </h6>
                  </td>
                  <td class="align-middle text-sm">
                    {{ $item->email }}
                  </td>
                  <td class="align-middle text-sm">
                    {{ $item->level->name }}
                  </td>
                  <td class="align-middle text-sm">
                    {{ $item->team == null ? "" : $item->team->name }}
                  </td>
                  
                  <td class="align-middle">
                    <a href="{{ route('satker.users.edit', [$item->id_hash()] ) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Edit user">
                      <button class="btn btn-sm btn-success"><i class="fa fa-edit"></i> Edit</button>
                    </a>
                  </td>
                </tr>
                @endforeach

              </tbody>
            </table>
            <form id="delete-form" action="" method="post">
              @method("DELETE")
              @csrf
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
</script>
@endsection