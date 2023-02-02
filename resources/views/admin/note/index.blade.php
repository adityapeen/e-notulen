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
            <a href="{{ route('admin.notes.create')}}" class="btn btn-success shadow-dark mb-0 ms-auto me-3">Tambah</a>
          </div>
        </div>
        <div class="card-body pb-2">
          <div class="table-responsive">
            <table class="table align-items-center mb-0" id="myTable">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Rapat</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($notes as $item)
                <tr>
                  <td class="text-sm">
                    <h6 class="mb-0">{{ $item->name }}</h6>
                    <span class="badge badge-sm bg-gradient-{{ $item->type == "public" ? "success":"info" }}" >{{ $item->type }}</span>
                  </td>
                  <td class="align-middle text-sm">
                    {{ $item->date }}
                  </td>
                  <td class="align-middle text-sm">
                    <span class="badge badge-sm bg-gradient-{{ $item->status == "open" ? "success":"danger" }}">{{ $item->status }}</span>
                  </td>
                  
                  <td class="align-middle">
                    <a href="{{ route('admin.notes.edit', [$item->id] ) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Edit Agenda">
                      <button class="btn btn-sm btn-success"><i class="fa fa-edit"></i></button>
                    </a>
                    <a href="#" onclick="handleLock('{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Kunci Agenda">
                      <button class="btn btn-sm btn-warning"><i class="fa fa-lock"></i></button>
                    </a>
                    <a href="#" onclick="handleDestroy('{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Hapus Agenda">
                      <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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
            <form id="lock-form" action="" method="post">
              @method("post")
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
            var link = "{{url('/admin/notes/')}}/" + id;
              $("#delete-form").attr("action", link);
              $("#delete-form").submit();
          }
      });

  const handleLock = id =>
      swal({
          title: "Apakah anda yakin mengubah status notulensi ini ?",
          icon: "warning",
          buttons: true,
      })
      .then(willLock => {
          if (willLock) {
            var link = "{{url('/admin/notes/lock')}}/" + id;
              $("#lock-form").attr("action", link);
              $("#lock-form").submit();
          }
      });
</script>
@endsection