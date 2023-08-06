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
            <table class="table align-items-center mb-0" id="tableNotulen">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Rapat</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Due Date</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                  <th class="text-secondary opacity-7"></th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($actions as $item)
                <tr>
                  <td class="text-sm">
                    <h6 class="mb-0">{{ $item->note->name }}</h6>
                    <span class="badge badge-sm bg-gradient-{{ $item->type == "public" ? "success":"info" }}" >{{ $item->note->date }}</span>
                  </td>
                  <td class="align-middle text-sm">
                    {{ $item->due_date }}
                  </td>
                  <td class="align-middle text-sm">
                    <span class="badge badge-sm bg-gradient-{{ $item->status == "done" ? "success":( $item->status == "onprogress" ? "info" : "danger") }}">{{ $item->status }}</span>
                  </td>
                  <td class="align-middle text-sm">
                    <a href="#" class="btn btn-sm bg-gradient-secondary mb-0" onclick="viewAction('{{ $item->id }}')">Detail</a>
                    <a href="{{ route('satker.notes.evidence', $item->id)}}" class="btn btn-sm bg-gradient-info mb-0">Evidences</a>
                  </td>                  
                  <td class="align-middle text-sm">
                    <a href="#" onclick="handleView('{{$item->note->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Detail Notulensi">
                      <button class="btn btn-sm btn-info mb-0"><i class="fa fa-eye"></i></button>
                    </a>
                    @if($item->note->status == 'lock' && $item->note->file_notulen !== NULL)
                    <a href="{{ route('satker.notes.show', $item->note->id) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Lihat Notulensi">
                      <button class="btn btn-sm btn-success mb-0">Lihat Notulen</button>
                    </a>
                    @endif
          
                  </td>
                </tr>
                @endforeach
                @if(sizeof($actions) == 0)
                <tr>
                  <td colspan="4" class="text-center">
                    Belum ada Action Item
                  </td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>
          {{ $actions->links('vendor.pagination.bootstrap-5') }}
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
          <div class="row"><div class="col-md-4">Notulensi</div><div class="col-md-8" ><a href="#" id="note-file" class="btn btn-sm btn-info mb-1">Lihat File</a></div></div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $(document).ready(function(){
    $('#tableNotulensi').DataTable({
      ordering:  false
    });
  });

    const handleView = id =>{
      var link = "{{url('/api/notes/')}}/" + id;
      var url = "{{ url('/notulensi') }}/";
      $.ajax({
              url: link,
              context: document.body
            }).done(function(res) {
              if(res.note.file_notulen !== null ) {
                url=url+res.note.file_notulen;
                $('#note-file').html('Lihat File');
              }
              else {
                url='#';
                $('#note-file').html('Proses Finalisasi');
              }
              $('#modal-title').html(res.note.name + " === "+res.note.date);
              $('#note-issues').html(res.note.issues);
              $('#note-file').attr('href',url);
              $('#note-attendant').empty();
              res.attendants.forEach(item => {
                $('#note-attendant').append('<li>'+item+'</li>');
              });
              $("#modal-detail").modal('show');
            });
    }

    const viewAction = id => {
      $.ajax({
        type: 'GET',
        url: "{{ url('/api/action_detail') }}" + "/"+id,
        context: document.body
      }).done(function(data) {  
        var html = '<b>What</b><br>' + data.action_item.what +
                   '<br><b>How</b><br>' + data.action_item.how +
                   '<b>Due Date<br>'+data.action_item.due_date +'</b>';
        var pic = '<br><br><b>PIC</b> : ' + data.pics.map(function(item) { return ' ' + item['name']; });

        Swal.fire({
              title: data.action_item.name,
              html : html + pic
            });
      }).always(function(data) {
        // console.log(JSON.stringify(data));
      });;
    };
    
</script>
@endsection