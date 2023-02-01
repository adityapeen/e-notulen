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
          
          <form action={{ route('admin.agendas.update', $agenda->id)}} method="POST">
            @csrf
            @method('put')
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Nama Agenda
              </div>
              <div class="col-md-8">
                <input type="text" id="name" class="form-control border px-1 @error('name') is-invalid @enderror" name="name" value="{{ $agenda->name }}" placeholder="Rapat Anggaran" required>
              </div>
            </div>            
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Group
              </div>
              <div class="col-md-8">
                <select id="group_id" class="form-select border px-1 @error('group_id') is-invalid @enderror" value="{{ $agenda->group_id }}" name="group_id" required>
                  @foreach ($groups as $item)
                      <option value="{{ $item->id }}" {{ $item->id == $agenda->group_id ? 'selected' : ''}}>{{ $item->name }}</option>
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

@endsection