@extends('observer.layouts.template')
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
          <div class="table-responsive">
            <table class="table align-items-center mb-0" id="tableNotulen">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Rapat</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action Items</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($notes as $item)
                <tr>
                  <td class="text-sm">
                    <h6 class="mb-0">{{ $item->name }}</h6>
                    <span class="badge badge-sm bg-gradient-{{ $item->type == "public" ? "success":"info" }}" >{{ $item->type }}</span>
                    @if ($item->team != null)
                      <span class="badge badge-sm bg-gradient-light text-dark">{{ $item->team->satker->code }}</span>
                    @endif
                    @if($item->agenda != NULL)
                      <span class="badge badge-sm bg-gradient-secondary" >{{ $item->agenda->name }}</span>
                    @endif
                  </td>
                  <td class="align-middle text-sm">
                    {{ $item->date }}
                  </td>
                  <td class="align-middle text-sm">
                    <a href="{{ route('ses.notes.action', [$item->id] ) }}" class="btn btn-sm bg-gradient-info">Action Items</a>
                  </td>
                  <td class="align-middle text-sm">
                    <span class="badge badge-sm bg-gradient-{{ $item->status == "open" ? "success":"danger" }}">{{ $item->status }}</span>
                  </td>
                  
                  <td class="align-middle">
                    <a href="#" onclick="handleView('ses','{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Lihat Notulensi">
                      <button class="btn btn-sm btn-info"><i class="fa fa-eye"></i></button>
                    </a>
                    @if($item->status == 'lock')
                    <a href="{{ route('ses.notes.absensi', $item->id)}}" target="_blank"
                      class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Daftar Hadir">
                      <button class="btn btn-sm btn-warning"><i class="fa fa-list"></i></button>
                    </a>
                    @endif
                  </td>
                </tr>
                @endforeach

              </tbody>
            </table>
            <form id="delete-form" action="" method="post">
              @method("DELETE")
              @csrf
            </form>
            <form id="lock-form" action="" method="post">
              @method("post")
              @csrf
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  @include('admin.note.modal_detail')

@endsection

@section('script')
{{-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{asset('assets/js/notes-function.js')}}"></script>


<script>
  $(document).ready(function(){
    $('#tableNotulensi').DataTable({
      ordering:  false
    });
  });
    
</script>
@endsection