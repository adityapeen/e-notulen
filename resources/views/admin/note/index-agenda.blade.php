@extends('admin.layouts.template')
@section('title', $title.' - '.config('app.name'))
@section('breadcrumbs', $title.' - '.config('app.name'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card my-4">
      <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-info shadow-info border-radius-lg pt-2 pb-2 d-flex align-items-center">
          <h6 class="text-white text-capitalize ps-3">Summary Progress</h6>
          <a href="{{ route('admin.agenda.summary', $agenda->id) }}" class="btn btn-sm btn-success shadow-dark mb-0 ms-auto me-3">
            <i class="fas fa-chart-line" aria-hidden="true"></i></a>
        </div>
      </div>
      <div class="card-body pb-2">
        <?php echo $agenda->summary ?>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card my-4">
      <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-info shadow-info border-radius-lg pt-2 pb-2 d-flex align-items-center">
          <h6 class="text-white text-capitalize ps-3">Pending Action Items</h6>
        </div>
      </div>
      <div class="card-body pb-2">
        <table class="table align-items-center mb-0" id="tableAction">
          <thead>
            <tr>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">What</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Due Date</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">PIC</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
              <th class="text-secondary opacity-7"></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($pending_actions as $item)
            <tr>
              <td class="text-sm">
                <?php  echo $item->what ?>
              </td>
              <td class="text-sm">
                {{ $item->due_date }}
              </td>
              <td class="text-sm">
                <a href="#" data-id="{{ $item->id }}" onclick="handleDetail('{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Lihat Notulensi">
                  <button class="btn btn-sm btn-info"><i class="fa fa-eye"></i></button>
                </a>
              </td>
              <td class="text-sm">
                <span
                class="badge badge-sm bg-gradient-{{ $item->status == 'done' ? 'success' : ($item->status == 'onprogress' ? 'info' : 'danger') }}">{{ $item->status }}</span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-info shadow-info border-radius-lg pt-2 pb-2 d-flex align-items-center">
            <h6 class="text-white text-capitalize ps-3">{{$title}}</h6>
            <a href="{{ route('admin.notes.create', 'agenda='.$agenda->id)}}" class="btn btn-success shadow-dark mb-0 ms-auto me-3">Tambah</a>
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
                    <a href="{{ route('admin.notes.action', [$item->id] ) }}" class="btn btn-sm bg-gradient-info mb-0">Action Items 
                      <span class="badge bg-gradient-light text-dark ms-2">{{ $item->action_items_count }}</span></a>
                  </td>
                  <td class="align-middle text-sm">
                    <span class="badge badge-sm bg-gradient-{{ $item->status == "open" ? "success":"danger" }}">{{ $item->status }}</span>
                  </td>
                  
                  <td class="align-middle">
                    <a href="#" onclick="handleView('admin','{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Lihat Notulensi">
                      <button class="btn btn-sm btn-info"><i class="fa fa-eye"></i></button>
                    </a>
                    @if($item->status != 'lock')
                    <a href="{{ route('admin.notes.edit', [$item->id] ) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Edit Agenda">
                      <button class="btn btn-sm btn-success"><i class="fa fa-edit"></i></button>
                    </a>
                    @endif
                    <a href="#" onclick="handleLock('admin','{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="{{ $item->status == 'lock'? 'Buka':'Kunci' }} Notulensi">
                      <button class="btn btn-sm btn-{{ $item->status == 'lock'? 'primary':'warning' }}"><i class="fa fa-lock"></i></button>
                    </a>
                    {{-- <a href="#" onclick="handleMoM('{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Kirim MoM">
                      <button class="btn btn-sm btn-info"><i class="fa fa-file"></i></button>
                    </a> --}}
                    @if($item->status == 'lock')
                    <a href="#" onclick="handleSend('admin','{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Kirim MoM">
                      <button class="btn btn-sm btn-info"><i class="fa fa-file"></i></button>
                    </a>
                    <a href="{{ route('admin.notes.absensi', $item->id)}}" target="_blank"
                      class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Daftar Hadir">
                      <button class="btn btn-sm btn-warning"><i class="fa fa-list"></i></button>
                    </a>
                    @else
                    <a href="#" onclick="handleDestroy('admin','{{$item->id}}')" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Hapus Agenda">
                      <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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

  const handleDetail = (id) => {
      $.ajax({
        type: 'GET',
        url: "{{ url('/api/action_detail') }}" + "/"+id,
        context: document.body
      }).done(function(data) {  
        var html = '<b>What</b><br>' + data.action_item.what +
                   '<br><b>How</b><br>' + data.action_item.how +
                   '<b>Due Date<br>'+data.action_item.due_date +'</b>';
        var pic = '<br><br><b>PIC</b> : ' + data.pics.map(function(item) { return ' ' + item['name']; });

        Swal.fire({
              title: `${data.action_item.name} \n ${data.action_item.date}` ,
              html : html + pic
            });
      }).always(function(data) {
        // console.log(JSON.stringify(data));
      });;
  }
    
</script>
@endsection