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
            <table class="table align-items-center mb-0" id="tableNotulen">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Rapat</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action Items</th>
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
                    <a href="{{ route('admin.notes.action', [$item->id] ) }}" class="btn btn-sm bg-gradient-info">Action Items</a>
                  </td>
                  <td class="align-middle text-sm">
                    <span class="badge badge-sm bg-gradient-{{ $item->status == "open" ? "success":"danger" }}">{{ $item->status }}</span>
                  </td>
                  
                  <td class="align-middle">
                    <a href="#" onclick="handleView('{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Lihat Notulensi">
                      <button class="btn btn-sm btn-info"><i class="fa fa-eye"></i></button>
                    </a>
                    @if($item->status != 'lock')
                    <a href="{{ route('admin.notes.edit', [$item->id] ) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Edit Agenda">
                      <button class="btn btn-sm btn-success"><i class="fa fa-edit"></i></button>
                    </a>
                    @endif
                    <a href="#" onclick="handleLock('{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="{{ $item->status == 'lock'? 'Buka':'Kunci' }} Notulensi">
                      <button class="btn btn-sm btn-{{ $item->status == 'lock'? 'primary':'warning' }}"><i class="fa fa-lock"></i></button>
                    </a>
                    {{-- <a href="#" onclick="handleMoM('{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Kirim MoM">
                      <button class="btn btn-sm btn-info"><i class="fa fa-file"></i></button>
                    </a> --}}
                    @if($item->status == 'lock')
                    <a href="#" onclick="handleSend('{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Kirim MoM">
                      <button class="btn btn-sm btn-info"><i class="fa fa-file"></i></button>
                    </a>
                    @endif
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
  <div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modal-default" aria-hidden="true">
    <div class="modal-dialog modal- modal-dialog-centered modal-" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title font-weight-normal" id="modal-title"></h6>
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row"><div class="col-md-4">Issues</div><div class="col-md-8 font-weight-bold" id="note-issues"></div></div>
          <div class="row"><div class="col-md-4">Link</div><div class="col-md-8" id="note-link"></div></div>
          <div class="row"><div class="col-md-4"></div><div class="col-md-8" ><a href="#" id="note-file" class="btn btn-sm btn-info mb-1">Lihat File</a></div></div>
          <div class="row"><div class="col-md-4">Peserta</div><div class="col-md-8" id="note-attendant"></div></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link  ml-auto" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
  $(document).ready(function(){
    $('#tableNotulensi').DataTable({
      ordering:  false
    });
  });
  
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

    const handleSend = id =>
    swal({
        title: "Apakah anda akan mengirimkan notulensi ini ?",
        icon: "info",
        allowEscapeKey: false,
        allowOutsideClick: false,
        buttons: true,
    })
    .then(willSend => {
        if (willSend) {
          var link = "{{url('/admin/notes/send-mom')}}/" + id;
          return fetch(link);
        }
    }).then(results=>{
      return results.json();
    }).then(json=>{
      const status = json.status;
      if (!status) {
        return swal({
          title: "Gagal mengirim notulen",
          icon: "error",
        });
      }
      swal({
          title: "Berhasil mengirim notulen",
          icon: "success",
      })
    }).catch(err => {
      if (err) {
        swal("Oh noes!", "The AJAX request failed!", "error");
      } else {
        swal.close();
      }
    });

    const handleView = id =>{
      var link = "{{url('/admin/notes/view')}}/" + id;
      var url = "{{ url('/notulensi') }}/";
      $.ajax({
              url: link,
              context: document.body
            }).done(function(res) {
              if(res.note.file_notulen !== null ) url=url+res.note.file_notulen;
              else url='#';
              $('#modal-title').html(res.note.name + " === "+res.note.date);
              $('#note-issues').html(res.note.issues);
              $('#note-link').html(res.note.link_drive_notulen);
              $('#note-file').attr('href',url);
              $('#note-attendant').empty();
              res.attendants.forEach(item => {
                $('#note-attendant').append('<li>'+item+'</li>');
              });
              $("#modal-detail").modal('show');
            });
    }
    
</script>
@endsection