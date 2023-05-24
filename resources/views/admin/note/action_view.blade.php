@extends('admin.layouts.template')
@section('title', $title.' - '.config('app.name'))
@section('breadcrumbs', $title.' - '.config('app.name'))

@section('content')
<div class="row">
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
          <h6 class="text-white text-capitalize ps-3">{{$title}}</h6>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="input-group">
            <div class="col-md-6 me-1">
             What
            </div>
            <div class="col-md-3 me-1">
              Who
            </div>
            <div class="col-md-1 me-1">
              Dateline
            </div>
          </div>
        </div>
        <div class="action-items">
		        <div class="row" id="dynamic_form">
              <?php $idx = 0; ?>
              @foreach ($actions as $item)
              <div class="input-group baru-data mb-1">
                <input type="hidden" name="action_id[]" value="{{ $item->id }}">
                <div class="col-md-6 me-1">
                  <div class="row col-12">
                  <div class="input-group input-group-dynamic border rounded p-1">
                    <?= $item->what ?>
                  </div>
                  </div>
                  <div class="row ps-3">How</div>
                  <div class="row col-12">
                    <div class="input-group input-group-dynamic border rounded p-1">
                    <?= $item->how ?>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 me-1">
                  <select class="form-control existing" id="{{ $item->id }}" data-id="{{ $item->id }}" name="who[{{ $idx++ }}][]" multiple="multiple">
                    @foreach($attendants as $a)
                    <option value="{{ $a->user->id_hash() }}">{{ $a->user->name.' - '.$a->user->satker->code }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-1 me-1">
                  <input type="date" id="date_first" class="form-control border px-1" name="due_date[]" value="{{ $item->due_date}}">
                </div>
                <div class="col me-1 text-center">
                  <span class="badge badge-sm bg-gradient-{{ $item->status == "done" ? "success" : "secondary" }}" >{{ $item->status}}</span>
                </div>
                <div class="col me-1">
                  <a href="{{ route('admin.notes.evidence', [$item->id] ) }}" class="btn btn-info btn-sm" title="Evidences"><i class="fa fa-tasks"></i><span class="badge text-bg-light ms-2">{{ $item->evidences_count }}</span></a>
                </div>
              </div>
              <hr class="border border-bottom border-info">
              @endforeach
              @if(sizeof($actions) == 0)
                <div class="col text-center">
                  Action Item Kosong
                </div>
                @endif

            </div>
            <button type="button" onclick="history.back()" class="btn btn-light">Batal</button>
        </div>
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