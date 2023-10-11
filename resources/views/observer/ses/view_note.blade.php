@extends('observer.layouts.template')
@section('title', $title.' - '.config('app.name'))
@section('breadcrumbs', $title.' - '.config('app.name'))

@section('content')
<div class="collapse">
  <div class="col-12">
    <div class="card my-4">
      <div class="card-body">
        <div class="row"><div class="col-md-4">Judul</div><div class="col-md-8 font-weight-bold">{{ $note->name}}</div></div>
        <div class="row"><div class="col-md-4">Tanggal</div><div class="col-md-8 font-weight-bold">{{ $note->date}}</div></div>
        <div class="row"><div class="col-md-4">Issues</div><div class="col-md-8 font-weight-bold">{{ $note->issues}}</div></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card my-4">
      <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-info shadow-info border-radius-lg pt-2 pb-2 d-flex align-items-center">
          <h6 class="text-white text-capitalize ps-3">{{$title.' - '.$note->name}}</h6>
        </div>
      </div>
      <div class="card-body vh-100">
        <iframe width="100%" height="100%" src="{{ $note->file_notulen == NULL ? $note->link_drive_notulen : asset('notulensi/'.$note->file_notulen)}}">
        </iframe>
        
      </div>
    </div>
  </div>
</div>


@endsection

@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{asset('assets/js/dynamic-form.js')}}"></script>
<script>
  var api = '{{ url("api/act_pic/") }}/';

  $(document).ready(function(){    
    $('.existing').each(function(index){
      var id = $( this ).data('id');
      $( this ).select2({
        placeholder: 'Pilih PIC',
      });
      getExisting(id);
      
    
    });
    
    $('#date_first').val(getSeminggu());
    $('.baru-data').last().find('.btn-hapus').css("display","none");
    $('.baru-data').last().find('.btn-tambah').css("display","");
  });

  async function getExisting(id){
    const result = await $.ajax({
    type: 'GET',
    url: api+id
    }).then(function (data) {
      var list = JSON.parse(data);
      var idselected = [];
      list.results.forEach(item =>idselected.push(item.id));
      var selector = '#'+id
      $(selector).val(idselected).trigger('change');
    });

    return result;
  }
    
</script>
@endsection