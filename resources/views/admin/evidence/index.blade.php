@extends('admin.layouts.template')
@section('title', $title.' - '.config('app.name'))
@section('breadcrumbs', $title.' - '.config('app.name'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card my-4">
      <div class="card-body">
        <div class="row"><div class="col-md-4 font-weight-bold">What</div><div class="col-md-8"><?= $action->what ?></div></div>
        <div class="row"><div class="col-md-4 font-weight-bold">How</div><div class="col-md-8"><?= $action->how ?></div></div>
        <div class="row"><div class="col-md-4 font-weight-bold">Dateline</div><div class="col-md-8">{{ $action->due_date}}</div></div>
        <div class="row"><div class="col-md-4 font-weight-bold">PIC</div><div class="col-md-8 font-weight-bold">
          @foreach ($pics as $item)
          <li class="d-flex align-items-center mb-1">
            @if($item->status != "done")
              <a href="{{ route('admin.notes.pic.done', $item->id) }}" class="btn badge badge-sm bg-gradient-info mb-0 mr-2" title="Action Item selesai dikerjakan">Done</a> &nbsp; 
            @else
            <div class="btn badge badge-sm bg-gradient-success mb-0 mr-2" title="Selesai {{ $item->done_date}}"><i class="fas fa-check"></i></div> &nbsp; 
            @endif
             {{ $item->user->name}}
          </li>
          @endforeach
        </div></div>
        <div class="row"><div class="col-md-4 font-weight-bold">Status</div><div class="col-md-8 font-weight-bold">
          <span class="badge badge-sm bg-gradient-secondary" >{{ $action->status}}</span>
          @if($action->status != "done")
          <button class="btn badge badge-sm bg-gradient-{{ $action->status == "todo" ? "info" : "success"}}" onclick="handleStatus()">Mark as {{ $action->status == "todo" ? "on progress" : "done"}}</button>
          @endif
          <button data-url="{{route('admin.notes.action', $action->note->id)}}" class="btn badge badge-sm bg-gradient-primary"onclick="handleBack(event)" >Back to Action Items</button>
        </div></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-info shadow-info border-radius-lg pt-2 pb-2 d-flex align-items-center">
            <h6 class="text-white text-capitalize ps-3">{{$title}}</h6>
            <a href="{{ route('admin.notes.evidence.add', [$action->id])}}" class="btn btn-success shadow-dark mb-0 ms-auto me-3">Tambah</a>
          </div>
        </div>
        <div class="card-body pb-2">
          <div class="table-responsive">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Deskripsi</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Owner</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Uploaded</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">File</th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($evidences as $item)
                <tr>
                  <td class="">
                    <h6 class="mb-0">{{ $item->description }}</h6>
                  </td>
                  <td class="">
                    <h6 class="mb-0">{{ $item->user->name }}</h6>
                  </td>
                  <td class="">
                    <span class="badge badge-sm bg-gradient-secondary" >{{ substr($item->created_at,0,10) }}</span>
                  </td>
                  <td class="align-middle text-sm">
                    <a href="{{ url('/eviden', $item->file )}}" target="_blank" class="btn btn-sm btn-primary mb-0" data-file="{{ $item->file }}"> Lihat File </a>
                    
                  </td>
                  
                  <td class="align-middle">
                    <a href="{{ route('admin.evidences.edit', [$item->id] ) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Edit Agenda">
                      <button class="btn btn-sm btn-success mb-0"><i class="fa fa-edit"></i></button>
                    </a>
                    <a href="#" onclick="handleDestroy('{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Hapus Agenda">
                      <button class="btn btn-sm btn-danger mb-0"><i class="fa fa-trash"></i></button>
                    </a>
                  </td>
                </tr>
                @endforeach
                @if(sizeof($evidences) == 0)
                <tr>
                  <td colspan="2" class="text-center">
                    Belum ada Eviden Action Item
                  </td>
                </tr>
                @endif

              </tbody>
            </table>
            <form id="delete-form" action="" method="post">
              @method("DELETE")
              @csrf
            </form>
            <form id="status-form" action="" method="post">
              @method("POST")
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
            var link = "{{url('/admin/evidences/')}}/" + id;
              $("#delete-form").attr("action", link);
              $("#delete-form").submit();
          }
      });

  const handleStatus = () => swal({
          title: "Apakah anda yakin mengubah status data ini ?",
          icon: "warning",
          buttons: true,
          dangerMode: true,
      })
      .then(willChange => {
          if (willChange) {
            var link = "{{route('admin.notes.action.status', $action->id)}}";
              $("#status-form").attr("action", link);
              $("#status-form").submit();
          }
      });

  const handleBack = (event) => {
    window.location.href = event.target.dataset.url;
  }

    
</script>
@endsection