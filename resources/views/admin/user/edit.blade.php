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
          
          <form action={{ route('admin.users.update', $user->id_hash())}} method="POST">
            {{method_field('put')}}
            @csrf
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Nama
              </div>
              <div class="col-md-8">
                <input type="text" id="name" class="form-control border px-1 @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" required>
              </div>
            </div>
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                NIP
              </div>
              <div class="col-md-8">
                <input type="text" id="nip" class="form-control border px-1 " name="nip" value="{{ $user->nip }}">
              </div>
            </div>
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Email
              </div>
              <div class="col-md-8">
                <input type="text" id="email" class="form-control border px-1 " name="email" value="{{ $user->email }}">
              </div>
            </div>
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Nomor WA
              </div>
              <div class="col-md-8">
                <input type="text" id="phone" class="form-control border px-1 @error('phone') is-invalid @enderror" name="phone" value="{{ $user->phone }}" placeholder="(contoh : 088723455677)" required>
              </div>
            </div>
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Satker
              </div>
              <div class="col-md-8">
                <select id="satker_id" class="form-select border px-1 @error('satker_id') is-invalid @enderror" value="{{ $user->satker_id }}" name="satker_id" required>
                  @foreach ($satkers as $item)
                      <option value="{{ $item->id }}" {{ $item->id == $user->satker_id ? 'selected' : ''}}>{{ $item->name }}</option>
                  @endforeach
              </select>
              </div>
            </div>
            {{-- <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Level
              </div>
              <div class="col-md-8">
                <select id="level_id" class="form-select border px-1 @error('level_id') is-invalid @enderror" value="{{ $user->level_id }}" name="level_id" required>
                  @foreach ($levels as $item)
                      <option value="{{ $item->id }}" {{ $item->id == $user->level_id ? 'selected' : ''}}>{{ $item->name }}</option>
                  @endforeach
              </select>
              </div>
            </div> --}}
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Role(s)
              </div>
              <div class="col-md-8">
                <select id="roles" multiple="multiple" class="form-select border px-1 @error('roles') is-invalid @enderror" value="" name="roles[]" required >
                  @foreach ($roles as $item)
                      <option value="{{ $item->id }}">{{ $item->name }}</option>
                  @endforeach
              </select>
              </div>
            </div>
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Team
              </div>
              <div class="col-md-8">
                <select id="team_id" class="form-select border px-1 @error('team_id') is-invalid @enderror" value="{{ $user->team_id }}" name="team_id" required>
                  @foreach ($teams as $item)
                      <option value="{{ $item->id }}" {{ $item->id == $user->team_id_hash() ? 'selected' : ''}}>{{ $item->code.' - '.$item->name }}</option>
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
  $(document).ready( function() {
    $('#roles').select2({
      placeholder: 'Pilih Role User',
      });

    $("#roles").val(<?php echo  $assigned_roles ?>).trigger('change')
  });
</script>
@endsection