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
          
          <form action={{ route('admin.notes.update', $note->id )}} method="POST" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Agenda Rapat
              </div>
              <div class="col-md-8">
                <select id="agenda_id" class="form-select border px-1 @error('agenda_id') is-invalid @enderror" value="{{ $note->agenda_id }}" name="agenda_id">
                  <option value=>Pilih Agenda Rapat</option>
                  @foreach ($agendas as $item)
                      <option value="{{ $item->id }}" {{ $item->id == $note->agenda_hash() ? 'selected' : ''}}>{{ $item->name }}</option>
                  @endforeach
              </select>
              </div>
            </div>
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Tipe Notulen
              </div>
              <div class="col-md-8">
                <select id="type" class="form-select border px-1 @error('type') is-invalid @enderror" value="{{ $note->type }}" name="type" required>
                  @foreach ($types as $item)
                      <option value="{{ $item->id }}" {{ $item->id == $note->type ? 'selected' : ''}}>{{ $item->name }}</option>
                  @endforeach
              </select>
              </div>
            </div>
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Nama Rapat
              </div>
              <div class="col-md-8">
                <input type="text" id="name" class="form-control border px-1 @error('name') is-invalid @enderror" name="name" value="{{ $note->name }}" placeholder="Rapat Anggaran" required>
              </div>
            </div>            
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Tanggal Rapat
              </div>
              <div class="col-md-4">
                <input type="date" id="date" class="form-control border px-1 @error('date') is-invalid @enderror" name="date" value="{{ $note->date }}" required>
              </div>
              <div class="col-md-2">
                <input type="time" id="start_time" class="form-control border px-1 @error('start_time') is-invalid @enderror" name="start_time" value="{{ $note->start_time }}" required>
              </div>
              <div class="col-md-2">
                <input type="time" id="end_time" class="form-control border px-1 @error('end_time') is-invalid @enderror" name="end_time" value="{{ $note->end_time }}" required>
              </div>
            </div> 
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Tempat
              </div>
              <div class="col-md-8">
                <input type="text" id="place" class="form-control border px-1 @error('place') is-invalid @enderror" name="place" value="{{ $note->place }}" required>
              </div>
            </div>     
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Issues
              </div>
              <div class="col-md-8">
                <input type="text" id="issues" class="form-control border px-1 @error('issues') is-invalid @enderror" name="issues" value="{{ $note->issues }}" required>
              </div>
            </div>  
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                File Notulen
              </div>
              <div class="col-md-8">
                <div class="input-group input-group-outline my-1">
                  <input type="file" class="form-control @error('file_notulen') is-invalid @enderror" name="file_notulen" value="{{ $note->file_notulen }}">
                </div>
                @if ($note->file_notulen != NULL)
                    {{ $note->file_notulen }}
                @endif
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
              <select id="attendants" multiple="multiple"  class="form-select border px-1 @error('attendants') is-invalid @enderror" value="{{ old('attendants') }}" name="attendants[]">
                @foreach ($users as $item)
                    <option value="{{ $item->id_hash() }}">{{ $item->name.' - '.$item->satker->code }}</option>
                @endforeach
            </select>
            </div>
          </div>
          <hr class="horizontal dark my-2">
          <div class="row mb-1 align-items-center">
            <div class="col-md-4">
              Daftar Penerima MoM
            </div>
            <div class="col-md-8">
              <select id="mom_recipients" multiple="multiple"  class="form-select border px-1 @error('mom_recipients') is-invalid @enderror" value="{{ old('mom_recipients[]') }}" name="mom_recipients[]">
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
<script>
  $(document).ready( function() {
    $('#agenda_id').select2({
      placeholder: 'Pilih Agenda Rapat'
    });
    attendants();
    getExisting();
  });
  var api = '{{ route('api.attendants', $note->id) }}';

  function attendants(){
    $('#attendants').select2({
      placeholder: 'Pilih Peserta Rapat',
      });
      $('#mom_recipients').select2({
      placeholder: 'Pilih Penerima MoM'
    });
  };

  function getExisting(){
    $.ajax({
    type: 'GET',
    url: api
    }).then(function (data) {
      var list = JSON.parse(data);
      var idselected = [];
      var idselected_r = [];
      list.results.attendants.forEach(item =>idselected.push(item.id));
      $('#attendants').val(idselected).trigger('change')
      list.results.mom_recipients.forEach(item =>idselected_r.push(item.id));
      $('#mom_recipients').val(idselected_r).trigger('change')
    });
  }
</script>
@endsection