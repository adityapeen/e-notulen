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
            <a href="{{ route('admin.agendas.create')}}" class="btn btn-success shadow-dark mb-0 ms-auto me-3">Tambah</a>
          </div>
        </div>
        <div class="card-body pb-2">
          <div class="table-responsive">
            <table class="table align-items-center mb-0" id="myTable">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Group</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Prioritas</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Count</th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($agendas as $item)
                <tr>
                  <td class="">
                    <h6 class="mb-0">{{ $item->name }}</h6>
                  </td>
                  <td class="align-middle text-sm">
                    {{ $item->group != NULL ? $item->group->name : ""  }}
                  </td>
                  <td class="align-middle text-sm">
                    {{ $item->priority != NULL ? $item->priority->name : ""  }}
                  </td>
                  <td class="align-middle text-sm">
                    <h6 class="mb-0">{{ $item->notes_count }}</h6>
                  </td>
                  <td class="align-middle">
                    <a href="{{ route('admin.agendas.edit', [$item->id] ) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Edit Agenda">
                      <button class="btn btn-sm btn-success"><i class="fa fa-edit"></i></button>
                    </a>
                    <a href="#" onclick="handleDestroy('{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Hapus Agenda">
                      <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                    </a>
                  </td>
                </tr>
                @endforeach
                @if(sizeof($agendas) == 0)
                <tr>
                  <td colspan="2" class="text-center">
                    Belum ada Agenda
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
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
  const handleDestroy = id =>
      swal({
          title: "Apakah anda yakin menghapus data ini ?",
          // text: "Once deleted, you will not be able to recover this item!",
          icon: "warning",
          buttons: true,
          dangerMode: true,
      })
      .then(willDelete => {
          if (willDelete) {
            var link = "{{url('/admin/agendas/')}}/" + id;
              $("#delete-form").attr("action", link);
              $("#delete-form").submit();
          }
      });
</script>
@endsection