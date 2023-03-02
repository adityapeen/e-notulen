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

<div class="row-template d-none">
  <div class="input-group baru-data mb-1">
    <input type="hidden" name="action_id[]" value="">
    <div class="col-md-4 me-1">
      <div class="input-group input-group-dynamic border rounded p-1">
        <textarea class="form-control" name="what[]" rows="3" placeholder="What" spellcheck="false"></textarea>
      </div>
    </div>
    <div class="col-md-3 me-1">
      <div class="input-group input-group-dynamic border rounded p-1">
        <textarea class="form-control" name="how[]" rows="3" placeholder="How" spellcheck="false"></textarea>
      </div>
    </div>
    <div class="col-md-3 me-1">
      <select class="form-control selection" multiple="multiple" name="who[][]">
        @foreach($attendants as $a)
        <option value="{{ $a->user->id_hash() }}">{{ $a->user->name.' - '.$a->user->satker->code }}</option>
        @endforeach
      </select>
      <span class="btn btn-outline-secondary btn-sm mt-1" onclick="picAll(event)">All Satker</span>
    </div>
    <div class="col-md-1 me-1">
      <input type="date" id="date" class="form-control border px-1" name="due_date[]" value="">
    </div>
    <div class="button-group ms-auto">
        <button type="button" class="btn btn-success btn-tambah"><i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-danger btn-hapus" style="display:none;"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <hr class="border border-bottom border-secondary">
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
            <div class="col-md-4 me-1">
             What
            </div>
            <div class="col-md-3 me-1">
              How
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
          <form method="post" id="item-list" action="{{ route('api.actions.update', $note->id ) }}">
            @csrf
            @method('put')
            <input type="hidden" id="note_id" name="note_id" value="{{ $note->id }}">
		        <div class="row" id="dynamic_form">
              <?php $idx = 0; ?>
              @foreach ($actions as $item)
              <div class="input-group baru-data mb-1">
                <input type="hidden" name="action_id[]" value="{{ $item->id }}">
                <div class="col-md-4 me-1">
                  <div class="input-group input-group-dynamic border rounded p-1">
                    <textarea class="form-control" name="what[]" rows="3" placeholder="What" spellcheck="false">{{ $item->what}}</textarea>
                  </div>
                </div>
                <div class="col-md-3 me-1">
                  <div class="input-group input-group-dynamic border rounded p-1">
                    <textarea class="form-control" name="how[]" rows="3" placeholder="How" spellcheck="false">{{ $item->how}}</textarea>
                  </div>
                </div>
                <div class="col-md-3 me-1">
                  <select class="form-control existing selection" id="{{ $item->id }}" data-id="{{ $item->id }}" name="who[{{ $idx++ }}][]" multiple="multiple">
                    @foreach($attendants as $a)
                    <option value="{{ $a->user->id_hash() }}">{{ $a->user->name.' - '.$a->user->satker->code }}</option>
                    @endforeach
                  </select>
                  <span class="btn btn-outline-secondary btn-sm mt-1" onclick="picAll(event)">All Satker</span>
                </div>
                <div class="col-md-1 me-1">
                  <input type="date" id="date_first" class="form-control border px-1" name="due_date[]" value="{{ $item->due_date}}">
                </div>
                <div class="button-group ms-auto">
                    <button type="button" class="btn btn-success btn-tambah" style="display:none;"><i class="fa fa-plus"></i></button>
                    <button type="button" class="btn btn-danger btn-hapus"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <hr class="border border-bottom border-secondary">
              @endforeach

            </div>
            <button type="submit" class="btn btn-primary btn-simpan"><i class="fa fa-save"></i> Submit</button>
            <button type="button" onclick="history.back()" class="btn btn-light">Batal</button>
	        </form>
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
  var api_exist = '{{ url("api/act_pic/") }}/';
  var api_all_pic = '{{ url("api/all_pic/") }}/';

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
    url: api_exist+id
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