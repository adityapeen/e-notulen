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
          
          <form action={{ route('admin.evidences.update', $evidence->id)}} method="POST" enctype="multipart/form-data">
            @csrf
            @method('put')
            {{-- <input type="hidden" name="action_id" value="{{ $hashed_id }}" > --}}
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Deskripsi / Uraian
              </div>
              <div class="col-md-8">
                <input type="text" id="description" class="form-control border px-1 @error('description') is-invalid @enderror" name="description" value="{{ $evidence->description }}" placeholder="Deskripsi File" required>
              </div>
              
            </div>         
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                File
              </div>
              <div class="col-md-8">
                <input type="file" id="file" class="form-control border px-1 @error('file') is-invalid @enderror" name="file" value="{{ old('file') }}" placeholder="Bukti File" required>
                <small class="font-weight-bold">{{ strlen($evidence->file) > 50 ? substr($evidence->file,0,50)."...".substr($evidence->file,-8) : $evidence->file }}</small>
                <div class="text-danger font-weight-light small">* Mengganti file akan menghapus file yang sebelumnya telah diupload</div>
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