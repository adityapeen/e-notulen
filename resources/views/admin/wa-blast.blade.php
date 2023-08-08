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
          <div class="alert alert-secondary text-white" role="alert">
            <strong>Info!</strong> Fitur ini berfungsi untuk mengirim pesan WhatsApp kepada penerima terpilih
          </div>
          
          <form action={{ route('admin.wa-blast.send')}} method="POST">
            @csrf
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Pesan
              </div>
              <div class="col-md-8">
                <textarea name="message" id="message" rows="5" class="form-control border px-2" placeholder="Tuliskan pesan anda disini"></textarea>
              </div>
            </div>
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Penerima
              </div>
              <div class="col-md-8">
                <select id="recipients" multiple="multiple"  class="form-select border px-1 @error('recipients') is-invalid @enderror" value="{{ old('recipients') }}" name="recipients">
                  @foreach ($recipients as $item)
                      <option value="{{ $item->id_hash() }}">{{ $item->name.' - '.$item->satker->code }}</option>
                  @endforeach
              </select>
              </div>
            </div>
            <div class="mt-3 d-flex" >         
                <button type="button" onclick="sendWA('admin', '{{csrf_token()}}')" class="btn btn-info me-2">Simpan</button>
                <button type="button" onclick="history.back()" class="btn btn-light">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{asset('assets/js/wa-blast.js')}}"></script>
<script>
  $(document).ready( function() {
    $('#recipients').select2({
      placeholder: 'Pilih Penerima Pesan'
    });
  });
</script>
@endsection