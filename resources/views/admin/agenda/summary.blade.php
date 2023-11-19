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
          
          <form action={{ route('admin.agenda.summary.save', $agenda->id)}} method="POST">
            @csrf
            @method('post')
            <div class="row mb-1 align-items-center">
              <div class="col-md-4">
                Nama Agenda
              </div>
              <div class="col-md-8">
                <input type="text" id="name" class="form-control border px-1 @error('name') is-invalid @enderror" name="name" value="{{ $agenda->name }}"  disabled>
              </div>
            </div>            
            <div class="row">
              <div class="col-md-12">
                <textarea name="summary" id="summary" rows="5" class="form-control border px-2 textarea" placeholder="Tuliskan summary agenda disini">{{ $agenda->summary }}</textarea>
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
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

<script>
  $(document).ready(function(){
    $('#attendants').select2({
      placeholder: 'Pilih Peserta Rapat'
    });

    $(".textarea").each(function(){
    ClassicEditor
      .create( this ,{
        height: "300px",
        toolbar: {
            items: [
                'undo', 'redo', 'alignment',
                '|', 'heading',
                '|', 'outdent', 'indent',
                '|', 'bold', 'italic',
                '|', 'link', 'uploadImage', 'insertTable', 'mediaEmbed',
                '|', 'bulletedList', 'numberedList', 'outdent', 'indent'
            ]
        },
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
  })

</script>
@endsection