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
          
          <form action={{ route('admin.agendas.store')}} method="POST">
            @csrf
            @method('post')
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Nama Agenda
              </div>
              <div class="col-md-8">
                <input type="text" id="name" class="form-control border px-1 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Rapat Anggaran" required>
              </div>
            </div>            
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Icon
              </div>
              <div class="col-md-8">
                <input type="text" id="icon_material" class="form-control border px-1 @error('icon_material') is-invalid @enderror" name="icon_material" value="{{ old('icon_material') }}" placeholder="card_membership">
              </div>
            </div>            
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Group
              </div>
              <div class="col-md-8">
                <select id="group_id" class="form-select border px-1 @error('group_id') is-invalid @enderror" value="{{ old('group_id') }}" name="group_id">
                  <option value="">Pilih Group</option>
                  @foreach ($groups as $item)
                      <option value="{{ $item->id }}">{{ $item->name }}</option>
                  @endforeach
              </select>
              </div>
            </div>
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Prioritas
              </div>
              <div class="col-md-8">
                <select id="priority" class="form-select border px-1 @error('priority') is-invalid @enderror" value="{{ old('priority') }}" name="priority">
                  @foreach ($priorities as $item)
                      <option value="{{ $item->id }}">{{ $item->name }}</option>
                  @endforeach
              </select>
              </div>
            </div>
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Peserta Rapat
              </div>
              <div class="col-md-8">
                <select id="attendants" multiple="multiple"  class="form-select border px-1 @error('attendants') is-invalid @enderror" value="{{ old('attendants[]') }}" name="attendants[]">
                  @foreach ($users as $item)
                      <option value="{{ $item->id_hash() }}">{{ $item->name.' - '.$item->satker->code }}</option>
                  @endforeach
              </select>
              </div>
            </div>
            <div class="mt-3 d-flex" >         
                <button type="submit" class="btn btn-info me-2">Simpan</button>
                <button type="button" onclick="history.back()" class="btn btn-light">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  
@endsection

@section('script')
<script>
  $(document).ready(function(){
    $('#attendants').select2({
      placeholder: 'Pilih Peserta Rapat'
    });
  })
</script>
@endsection