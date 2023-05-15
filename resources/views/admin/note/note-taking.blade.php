@extends('admin.layouts.template')
@section('title', $title.' - '.config('app.name'))
@section('breadcrumbs', $title.' - '.config('app.name'))

@section('content')
<div class="">
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

<div class="row">
  <div class="col-12">
    <div class="card my-4">
      <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-info shadow-info border-radius-lg pt-2 pb-2 d-flex align-items-center">
          <h6 class="text-white text-capitalize ps-3">Tambah Poin Meeting</h6>
        </div>
      </div>
      <div class="card-body">
        <textarea class="form-control textarea" name="what[]" rows="7" placeholder="What" spellcheck="false" required></textarea>
      </div>
      <div class="card-footer">
        <button type="button" class="btn btn-sm btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card my-4">
      <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-info shadow-info border-radius-lg pt-2 pb-2 d-flex align-items-center">
          <h6 class="text-white text-capitalize ps-3">{{'Summary - '.$note->name}}</h6>
        </div>
      </div>
      <div class="card-body">
        <div id="note-list">
        </div>
      </div>
    </div>
  </div>
</div>


@endsection

@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
@vite('resources/js/app.js')

{{-- Generate Text Editor --}}
<script> 
  $(".textarea").each(function(){
    ClassicEditor
      .create( this ,{
        height: "300px",
        removePlugins: [ 'Heading' ],
        toolbar: [ 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote' , 'link', 'undo', 'redo']
      })
      .then(editor => {
      editor.model.document.on('change:data', () => {
        this.value = editor.getData();
      });
    })
      .catch( error => {
          console.error( error );
    } )
});
</script>

{{-- WebSocket --}}
<script>
  document.addEventListener("DOMContentLoaded", function(event){
    console.log("Run")
    Echo.channel('testing-channel')
    .listen('helloEvent', (e)=> {
      console.group('Event From Testing');
      console.log(e);
      console.groupEnd();
    }) 
  });
</script>
@endsection