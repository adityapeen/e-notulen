@extends('satker.layouts.template')
@section('title', $title.' - '.config('app.name'))
@section('breadcrumbs', $title.' - '.config('app.name'))

@section('content')
  <div class="row">
    <?php $i=0; ?>
    @foreach($agendas as $item)
    <div class="col-xl-3 col-sm-6 mb-xl-4 mb-4">
      <a class="card" href="{{ route('satker.notes.agenda', $item->id) }}">
        <div class="card-header p-3 pt-2 pb-1">
          <div
            class="icon icon-lg icon-shape bg-gradient-{{$color[$i]}} shadow-dark border-radius-xl mt-n4 position-absolute text-center">
            <i class="material-icons opacity-10">{{ $item->icon_material}}</i>
          </div>
          <div class="pt-1 ps-6 text-end">
            <p class="text-capitalize mb-0 font-weight-bold">{{ $item->name}}</p>
            <h4 class="mb-0">{{ $item->notes_count}}</h4>
          </div>
        </div>
        <hr class="horizontal dark my-0">
        <div class="card-footer ps-3 py-1">
          <p class="mb-0"><small>Rapat terakhir</small> <span class="text-success font-weight-bolder text-sm position-absolute end-5">{{$item->last_note_date;}} </span></p>
        </div>
      </a>
    </div>
    <?php $i++; if($i==sizeof($color)) $i=0; ?>
    @endforeach
    
  </div>
  
  <div class="row mt-3">
    <div class="col-12">
      <button id="checkApi" class="btn btn-outline-primary">API Status</button>
    </div>
  </div>

@endsection

@section('script')
  <script>
    $('#checkApi').on('click', function() {
      $.ajax({
        type: 'POST',
        url: "{{ env('API_URL') == null ? 'http://localhost:8000' : env('API_URL') }}" + "/check",
        context: document.body
      }).done(function(data) {
        alert(data.message)
      }).always(function(data) {
        console.log(JSON.stringify(data));
      });;

    })
  </script>
@endsection
