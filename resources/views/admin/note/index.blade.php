@extends('admin.layouts.template')
@section('title', $title . ' - ' . config('app.name'))
@section('breadcrumbs', $title . ' - ' . config('app.name'))

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header position-relative mt-n4 z-index-2 mx-3 p-0">
          <div class="bg-gradient-info shadow-info border-radius-lg d-flex align-items-center pt-2 pb-2">
            <h6 class="text-capitalize ps-3 text-white">{{ $title }}</h6>
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
                      <span
                        class="badge badge-sm bg-gradient-{{ $item->type == 'public' ? 'success' : 'info' }}">{{ $item->type }}</span>
                      @if ($item->agenda != null)
                        <span class="badge badge-sm bg-gradient-secondary">{{ $item->agenda->name }}</span>
                      @endif
                    </td>
                    <td class="align-middle text-sm">
                      {{ $item->date }}
                    </td>
                    <td class="align-middle text-sm">
                      <a href="{{ route('admin.notes.action', [$item->id]) }}" class="btn btn-sm bg-gradient-info">Action
                        Items</a>
                    </td>
                    <td class="align-middle text-sm">
                      <span class="badge badge-sm bg-gradient-{{ $item->status == 'open' ? 'success' : 'danger' }} btn"
                        onclick="handleView('{{ $item->id }}')" data-toggle="tooltip"
                        title="Lihat Notulensi">{{ $item->status }} <div class="fa fa-eye"></div></span>
                    </td>

                    <td class="align-middle">
                      @if ($item->status != 'lock')
                        <a href="{{ route('admin.notes.edit', [$item->id]) }}"
                          class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Edit Notulensi">
                          <button class="btn btn-sm btn-success"><i class="fa fa-edit"></i></button>
                        </a>
                        @if ($item->link_drive_notulen == '-')
                          <a href="{{ route('api.gdocs', [$item->id]) }}" class="text-secondary font-weight-bold text-xs"
                            data-toggle="tooltip" title="Generate File Notulen">
                            <button class="btn btn-sm btn-secondary"><i class="fab fa-google-drive"></i></button>
                          </a>
                        @endif
                        <a href="{{ route('admin.notes.qrcode', [$item->id]) }}" target="_blank"
                          class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="QR Join Meeting">
                          <button class="btn btn-sm btn-dark"><i class="fa fa-qrcode"></i></button>
                        </a>
                      @endif
                      <a href="#" onclick="handleLock('{{ $item->id }}')"
                        class="text-secondary font-weight-bold text-xs" data-toggle="tooltip"
                        title="{{ $item->status == 'lock' ? 'Buka' : 'Kunci' }} Notulensi">
                        <button class="btn btn-sm btn-{{ $item->status == 'lock' ? 'primary' : 'warning' }}"><i
                            class="fa fa-lock"></i></button>
                      </a>
                      {{-- <a href="#" onclick="handleMoM('{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Kirim MoM">
                      <button class="btn btn-sm btn-info"><i class="fa fa-file"></i></button>
                    </a> --}}
                      @if ($item->status == 'lock')
                        @if($item->file_notulen == NULL)
                          <a href="{{ route('admin.export.docs', [$item->id]) }}" target="_blank" class="text-secondary font-weight-bold text-xs"
                            data-toggle="tooltip" title="Generate PDF">
                            <button class="btn btn-sm btn-danger"><i class="fa fa-file-pdf"></i></button>
                          </a>
                        @endif
                        <a href="#" onclick="handleSend('{{ $item->id }}')"
                          class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Kirim MoM">
                          <button class="btn btn-sm btn-info"><i class="fa fa-file"></i></button>
                        </a>
                        <a href="{{ route('admin.notes.absensi', $item->id)}}" target="_blank"
                          class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Daftar Hadir">
                          <button class="btn btn-sm btn-warning"><i class="fa fa-list"></i></button>
                        </a>
                        @else
                        <a href="#" onclick="handleDestroy('{{ $item->id }}')"
                          class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Hapus Agenda">
                          <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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

  <script>
    $(document).ready(function() {
      $('#tableNotulensi').DataTable({
        ordering: false
      });
    });
  </script>
@endsection