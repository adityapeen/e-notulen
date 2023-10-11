@extends('observer.layouts.template')
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
          <div class="table-responsive">
            <table class="align-items-center mb-0 table" id="tableNotulen">
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
                      <span class="badge badge-sm bg-gradient-{{ $item->type == 'public' ? 'success' : 'info' }} px-1">&nbsp;</span>
                      @if ($item->team != null)
                        <span class="badge badge-sm bg-gradient-light text-dark">{{ $item->team->satker->code }}</span>
                      @endif
                      @if ($item->agenda != null)
                        <span class="badge badge-sm bg-gradient-secondary">{{ $item->agenda->name }}</span>
                      @endif
                    </td>
                    <td class="align-middle text-sm">
                      {{ $item->date }}
                    </td>
                    <td class="align-middle text-sm">
                      <a href="{{ route('ses.notes.action', [$item->id]) }}" class="btn btn-sm bg-gradient-info">Action
                        Items <span class="badge bg-gradient-light text-dark ms-2">{{ $item->action_items_count }}</span></a>
                    </td>
                    <td class="align-middle text-sm">
                      <span class="badge badge-sm bg-gradient-{{ $item->status == 'open' ? 'success' : 'danger' }} btn"
                        onclick="handleView('ses','{{ $item->id }}')" data-toggle="tooltip"
                        title="Lihat Notulensi">{{ $item->status }} <div class="fa fa-eye"></div></span>
                    </td>

                    <td class="align-middle">
                      @if ($item->status == 'lock')
                        <a href="#" onclick="handleSend('ses','{{ $item->id }}')"
                          class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Kirim MoM">
                          <button class="btn btn-sm btn-info"><i class="fa fa-file"></i></button>
                        </a>
                        <a href="{{ route('ses.notes.absensi', $item->id)}}" target="_blank"
                          class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Daftar Hadir">
                          <button class="btn btn-sm btn-warning"><i class="fa fa-list"></i></button>
                        </a>
                        <a href="{{ route('ses.notes.show', $item->id)}}" target="_blank"
                          class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Lihat Notulen">
                          <button class="btn btn-sm btn-success">Lihat Notulen</button>
                        </a>

                        @endif
                    </td>
                  </tr>
                @endforeach
                @if(sizeof($notes) == 0)
                <tr>
                  <td colspan="2" class="text-center">
                    Belum ada Notulensi
                  </td>
                </tr>
                @endif
              </tbody>
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
          {{ $notes->links('vendor.pagination.bootstrap-5') }}
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
  <script src="{{asset('assets/js/notes-function-admin.js')}}"></script>

  <script>
    $(document).ready(function() {
      $('#tableNotulensi').DataTable({
        ordering: false
      });
      prepareDropdown()
    });
  
  </script>
@endsection