@extends('admin.layouts.template')
@section('title', $title . ' - ' . config('app.name'))
@section('breadcrumbs', $title . ' - ' . config('app.name'))

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header position-relative mt-n4 z-index-2 mx-3 p-0">
          <div class="bg-gradient-info shadow-info border-radius-lg d-flex align-items-center pt-2 pb-2">
            <h6 class="text-capitalize ps-3 text-white mb-0">{{ $title }}</h6>
            <select id="satker_code" class="ms-3 rounded p-2 bg-gradient-light text-xxs font-weight-bolder">
              <option value="ALL">ALL</option>
              <option value="BPS">BPS</option>
                @foreach($satkers as $item)
                <option value="{{ $item->id_hash() }}">{{ $item->code}}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-light shadow-dark ms-3 mb-0" onclick="filterNote()">Filter</button>
            <a href="{{ route('admin.notes.create') }}" class="btn btn-success shadow-dark ms-auto me-3 mb-0">Tambah</a>
          </div>
        </div>
        <div class="card-body pb-2">
          {{-- {{ $dataTable->table() }} --}}
          <table class="table row-border" id="users-table">
            <thead>
                <tr>
                    <th>Nama Rapat</th>
                    <th>Tanggal</th>
                    <th>Action Items</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
        </table>
            <form id="delete-form" action="" method="post">
              @method('DELETE')
              @csrf
            </form>
            <form id="lock-form" action="" method="post">
              @method('post')
              @csrf
            </form>
        </div>
      </div>
    </div>
  </div>
  @include('admin.note.modal_detail')
  

@endsection

@section('script')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{asset('assets/js/notes-function.js')}}"></script>
  <script src="{{asset('assets/js/notes-function-admin.js')}}"></script>

  <script>
    $(document).ready(function() {
      $('#tableNotulensi').DataTable({
        ordering: false
      });
      prepareDropdown()
    });

    $(function() {
      var id_satker = $('#satker_code').val();
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! url("admin/notes/satker" ) !!}'+'/'+id_satker,
                columns: [{
                        data: 'name_b',
                        name: 'name'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'action_item',
                        name: 'action_item'
                    },
                    {
                        data: 'status_b',
                        name: 'status_b'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ]
            });
        });  
  </script>
@endsection