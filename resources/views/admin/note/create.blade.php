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
          </div>
        </div>
        <div class="card-body pb-2">
          
          <form action={{ route('admin.notes.store')}} method="POST" enctype="multipart/form-data">
            @csrf
            @method('post')
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Agenda Rapat
              </div>
              <div class="col-md-8">
                <select id="agenda_id" class="form-select border px-1 @error('agenda_id') is-invalid @enderror" value="{{ old('agenda_id') }}" name="agenda_id">
                  <option value=>Pilih Agenda Rapat</option>
                  @foreach ($agendas as $item)
                      <option value="{{ $item->id }}">{{ $item->name }}</option>
                  @endforeach
              </select>
              </div>
            </div>
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Tipe Notulen
              </div>
              <div class="col-md-8">
                <select id="type" class="form-select border px-1 @error('type') is-invalid @enderror" value="{{ old('type') }}" name="type" required>
                  @foreach ($types as $item)
                      <option value="{{ $item->id }}">{{ $item->name }}</option>
                  @endforeach
              </select>
              </div>
            </div>
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Nama Rapat
              </div>
              <div class="col-md-8">
                <input type="text" id="name" class="form-control border px-1 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Rapat Anggaran" required>
              </div>
            </div>            
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Tanggal Rapat
              </div>
              <div class="col-md-4">
                <input type="date" id="date" class="form-control border px-1 @error('date') is-invalid @enderror" name="date" value="{{ old('date') }}" required>
              </div>
              <div class="col-md-2">
                <input type="time" id="start_time" class="form-control border px-1 @error('start_time') is-invalid @enderror" name="start_time" value="{{ old('start_time') }}" required>
              </div>
              <div class="col-md-2">
                <input type="time" id="end_time" class="form-control border px-1 @error('end_time') is-invalid @enderror" name="end_time" value="{{ old('end_time') }}" required>
              </div>
            </div>    
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Max Execute
              </div>
              <div class="col-md-8">
                <input type="date" id="max_execute" class="form-control border px-1 @error('max_execute') is-invalid @enderror" name="max_execute" value="{{ old('max_execute') }}" required>
              </div>
            </div>  
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Issues
              </div>
              <div class="col-md-8">
                <input type="text" id="issues" class="form-control border px-1 @error('issues') is-invalid @enderror" name="issues" value="{{ old('issues') }}" required>
              </div>
            </div>  
            <div class="row mb-1 align-items-center">
              <div class="col-md-4 align-top">
                Link Drive
              </div>
              <div class="col-md-8">
                <input type="text" id="link_drive_notulen" class="form-control border px-1 @error('link_drive_notulen') is-invalid @enderror" name="link_drive_notulen" value="{{ old('link_drive_notulen') }}">
                <div class="input-group input-group-outline my-1">
                  <input type="file" class="form-control @error('file_notulen') is-invalid @enderror" name="file_notulen" value="{{ old('file_notulen') }}">
                </div>
              </div>
            </div>  
            
            <div class="mt-3 d-flex" >         
                <button type="submit" class="btn btn-info me-2">Simpan</button>
                <button type="button" onclick="history.back()" class="btn btn-light">Batal</button>
            </div>
          
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-info shadow-info border-radius-lg pt-2 pb-2 d-flex align-items-center">
            <h6 class="text-white text-capitalize ps-3">Peserta Rapat</h6>
          </div>
        </div>
        <div class="card-body pb-2">
          <div class="row mb-1 align-items-center">
            <div class="col-md-4">
              Daftar Peserta Rapat
            </div>
            <div class="col-md-8">
              <select id="attendants" multiple="multiple"  class="form-select border px-1 @error('attendants') is-invalid @enderror" value="{{ old('attendants[]') }}" name="attendants[]">
                @foreach ($users as $item)
                    <option value="{{ $item->id_hash() }}">{{ $item->name.' - '.$item->satker->code }}</option>
                @endforeach
            </select>
            </div>
          </form>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')
<script src="{{asset('assets/js/dynamic-form.js')}}"></script>
<script>
  var api = '{{ url('api/g_attendants') }}/';
  $(document).ready( function() {
    $('#agenda_id').select2({
      placeholder: 'Pilih Agenda Rapat'
    });
    autoDate();
    attendants();
    getExisting($('#agenda_id').val());
  });

  function autoDate(){
    var now = new Date();
    var month = (now.getMonth() + 1);               
    var day = now.getDate();
    var h = now.getHours();
    if (month < 10) 
        month = "0" + month;
    if (day < 10) {
      day = "0" + day;
    }
    var today = now.getFullYear() + '-' + month + '-' + day;
    var seminggu = getSeminggu();
    $('#date').val(today);
    $('#max_execute').val(seminggu);
    $('input[type="time"]').each(function(){ 
      $(this).attr({'value': h + ':00'});
    });
  }

  function attendants(){
    $('#attendants').select2({
      placeholder: 'Pilih Peserta Rapat'
    });
  }

  $('#agenda_id').change(function(){
    
    getExisting($(this).val());
  })

  function getExisting(id){
    $.ajax({
    type: 'GET',
    url: api+id
    }).then(function (data) {
      var list = JSON.parse(data);
      var idselected = [];
      list.results.forEach(item =>idselected.push(item.id));
      $('#attendants').val(idselected).trigger('change')
    });
  }


</script>
@endsection