@extends('user.layouts.template')
@section('title', $title.' - '.config('app.name'))
@section('breadcrumbs', $title.' - '.config('app.name'))

@section('content')
<div class="row">
<div class="col-md-8">
  <div class="row">
  <div class="col-12">
    <div class="card my-2">
      <div class="card-body">
        <div class="row"><div class="col-md-4 font-weight-bold">What</div><div class="col-md-8"><?= $action->what ?></div></div>
        <div class="row"><div class="col-md-4 font-weight-bold">How</div><div class="col-md-8"><?= $action->how ?></div></div>
        <div class="row"><div class="col-md-4 font-weight-bold">Dateline</div><div class="col-md-8">{{ $action->due_date}}</div></div>
        <div class="row"><div class="col-md-4 font-weight-bold">Status</div><div class="col-md-8 font-weight-bold">
          <span class="badge badge-sm bg-gradient-secondary" >{{ $action->status}}</span>
          <button type="button" class="btn badge badge-sm bg-gradient-info" data-bs-toggle="collapse" data-bs-target="#pic-list" aria-expanded="false" aria-controls="pic-list">PIC</button>
          <button data-url="{{route('user.notes.action', $action->note->id)}}" class="btn badge badge-sm bg-gradient-primary"onclick="handleBack(event)" >Back to Action Items</button>
        </div></div>
      </div>
    </div>
    <div class="card mb-4">
      <div class="card-body collapse" id="pic-list">
        <div class="row"><div class="col-md-4 font-weight-bold">PIC</div><div class="col-md-8 font-weight-bold">
          @foreach ($pics as $item)
          <li>{{ $item->user->name}}</li>
          @endforeach
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
            <a href="{{ route('user.notes.evidence.add', [$action->id])}}" class="btn btn-success shadow-dark mb-0 ms-auto me-3">Tambah</a>
          </div>
        </div>
        <div class="card-body pb-2">
          <div class="table-responsive">
            <table class="table table-sm align-items-center mb-0">
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
                <tr class="">
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
                    @if($item->uploaded_by == auth()->user()->id)
                    <a href="{{ route('user.evidences.edit', [$item->id] ) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Edit Evidence">
                      <button class="btn btn-sm btn-success mb-0"><i class="fa fa-edit"></i></button>
                    </a>
                    <a href="#" onclick="handleDestroy('{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Hapus Evidence">
                      <button class="btn btn-sm btn-danger mb-0"><i class="fa fa-trash"></i></button>
                    </a>
                    @endif
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
  </div>
  <div class="col-md-4 {{ $comments == 0 ? "collapse" : "" }} collapse-horizontal" id="commentsSection">
    <div class="row pe-3">
      <div class="card mt-2 mb-2 p-2 min-height-500 max-height-500" id="comments-card" >
        <div class="card-body" >
        </div>
      </div>
      <div class="card">
        <hr class="dark horizontal my-0">
        <div class="card-footer row p-0 align-items-center">
          <div class="col-10">
            <form id="comment-form" action="" method="post">
              @method("POST")
              @csrf
              <div class="input-group input-group-outline my-2">
                <label class="form-label">Tulis Komentar</label>
                <input type="text" id="message" name="message" class="form-control">
              </div>
            </form>
          </div>
          <div class="col-2 justify-content-center">
            <button class="btn btn-sm btn-success mb-0" onclick="sendComment('{{ $action->id}}')"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('assets/js/comments.js') }}"></script>

<script>
  $(document).ready(function() {
    refreshComments();
  });

  const ps = new PerfectScrollbar('#comments-card');
  const container = document.getElementById('comments-card');

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
            var link = "{{url('/user/evidences/')}}/" + id;
              $("#delete-form").attr("action", link);
              $("#delete-form").submit();
          }
      });

  const handleBack = (event) => {
    window.location.href = event.target.dataset.url;
  }

    
</script>
@endsection