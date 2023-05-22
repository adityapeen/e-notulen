@extends('user.layouts.template_join')
@section('title', $title.' - '.config('app.name'))
@section('breadcrumbs', $title.' - '.config('app.name'))

@section('content')
  <div class="row justify-content-center mt-3">
    <div class="col-md-6">
      <div class="card my-4">
        <div class="card-body px-0 pb-2">
          <div class="row align-items-center mb-3">
            <div class="col text-center"><img src="{{ asset('assets/img/logo-esdm.png') }}" class="img-fluid w-25"></div>
          </div>
          <div class="row">
            <div class="col font-weight-bold text-info h5 mb-0 text-center">{{ $note->name }}</div>
          </div>
          <div class="row mb-3">
            <div class="col font-weight-bold text-center">{{ $note->date }}</div>
          </div>
          <form action={{ route('join_meeting', $note->id)}} method="POST" enctype="multipart/form-data">
            @csrf
            @method('post')
          <div class="row mb-3 justify-content-center p-1">
            <div class="col-md-6">
              <input id="nip" type="number" class="form-control border px-1" placeholder="Masukan NIP Anda"
                name="nip" value="" required autofocus>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-md-6 text-center">
              <button type="submit" class="btn btn-info me-2">Submit</button>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('script')
@endsection
