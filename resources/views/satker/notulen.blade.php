@extends('satker.layouts.template')
@section('title', $title.' - '.config('app.name'))
@section('breadcrumbs', $title.' - '.config('app.name'))

@section('content')
  <div class="row">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div
            class="icon icon-lg icon-shape bg-gradient-dark shadow-dark border-radius-xl mt-n4 position-absolute text-center">
            <i class="material-icons opacity-10">supervisor_account</i>
          </div>
          <div class="pt-1 text-end">
            <p class="text-capitalize mb-0 text-sm">Users</p>
            <h4 class="mb-0">{{ $users }}</h4>
          </div>
        </div>
        <hr class="horizontal dark my-0">
        <div class="card-footer py-2">
          <p class="d-flex text-sm justify-content-between mb-0">Input WA <span class="ms-auto text-success font-weight-bolder text-sm">{{ $wa_ready}} </span> </p>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div
            class="icon icon-lg icon-shape bg-gradient-primary shadow-primary border-radius-xl mt-n4 position-absolute text-center">
            <i class="material-icons opacity-10">file_copy</i>
          </div>
          <div class="pt-1 text-end">
            <p class="text-capitalize mb-0 text-sm">Notulen</p>
            <h4 class="mb-0">{{ $notes }}</h4>
          </div>
        </div>
        <hr class="horizontal dark my-0">
        <div class="card-footer py-2">
          <p class="d-flex text-sm justify-content-between mb-0">Locked <span class="ms-auto text-success font-weight-bolder text-sm">{{ $notes_locked}} </span> </p>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div
            class="icon icon-lg icon-shape bg-gradient-success shadow-success border-radius-xl mt-n4 position-absolute text-center">
            <i class="material-icons opacity-10">calendar_month</i>
          </div>
          <div class="pt-1 text-end">
            <p class="text-capitalize mb-0 text-sm">Agenda</p>
            <h4 class="mb-0">{{ $agendas }}</h4>
          </div>
        </div>
        <hr class="horizontal dark my-0">
        <div class="card-footer p-3">
          {{-- <p class="mb-0"><span class="text-danger font-weight-bolder text-sm">-2%</span> than yesterday</p> --}}
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div
            class="icon icon-lg icon-shape bg-gradient-info shadow-info border-radius-xl mt-n4 position-absolute text-center">
            <i class="material-icons opacity-10">engineering</i>
          </div>
          <div class="pt-1 text-end">
            <p class="text-capitalize mb-0 text-sm">Action Item</p>
            <h4 class="mb-0">{{ $actions }}</h4>
          </div>
        </div>
        <hr class="horizontal dark my-0">
        <div class="card-footer py-2">
          <p class="d-flex text-sm justify-content-between mb-0">To Do <span class="ms-auto text-success font-weight-bolder text-sm">{{ $actions_todo}} </span> </p>
          <p class="d-flex text-sm justify-content-between mb-0">On Progress <span class="ms-auto text-success font-weight-bolder text-sm">{{ $actions_progress}} </span> </p>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-info shadow-info border-radius-lg pt-2 pb-2 d-flex align-items-center">
            <h6 class="text-white text-capitalize ps-3">Action Items</h6>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="table-responsive px-3">
            <table class="align-items-center mb-0 table">
              <thead>
                <tr>
                  <th class="text-secondary opacity-7"></th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Agenda</th>
                  <th class="text-secondary opacity-7">Due Date</th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($undone as $item)
                  <tr>
                    <td>
                      <span class="badge badge-sm bg-gradient-{{ $item->status == "todo" ? "info" : "secondary" }}" >{{ $item->status}}</span>
                    </td>
                    <td style="cursor: pointer" class="clickable-action text-wrap" data-id="{{ $item->id}}">
                      <?= $item->what ?>
                      <p class="text-secondary mb-0 text-xs">{{ $item->note->date.' • '.$item->note->name}}</p>
                    </td>
                    <td class="align-middle">                      
                      {{ $item->due_date}}                     
                    </td>
                    <td class="align-middle">   
                      @if($item->status == 'onprogress')                   
                        <a href="{{ route('satker.notes.evidence', $item->id)}}" class="badge btn-success badge-sm bg-gradient-success" >Eviden</a>                  
                      @endif
                    </td>
                  </tr>
                @endforeach
                @if(sizeof($undone) == 0)
                <tr><td colspan="2" class="text-center">
                  Belum ada Action Items
                  </td></tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-info shadow-info border-radius-lg pt-2 pb-2 d-flex align-items-center">
            <h6 class="text-white text-capitalize ps-3">Today's Agenda</h6>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="table-responsive p-0">
            <table class="align-items-center mb-0 table">
              {{-- <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Agenda</th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead> --}}
              <tbody>
                @foreach ($todays as $item)
                  <tr>
                    <td style="cursor: pointer" class="clickable-row" data-href="{{$item->status == 'lock'? route('admin.notes.show', $item->id) : $item->link_drive_notulen }}">
                      <div class="d-flex px-2 py-1">
                        <div class="me-3">
                          <div
                            class="icon icon-sm icon-shape bg-gradient-info shadow-info border-radius-xl mt-n4 text-center">
                            <i class="material-icons opacity-10">notes</i>
                          </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ $item->name }}</h6>
                          <p class="text-secondary mb-0 text-xs">{{ $item->date}}</p>
                        </div>
                      </div>
                    </td>
                    <td class="align-middle">
                      <a href="{{ route('admin.notes.action', $item->id) }}" class="text-info font-weight-bold text-xs"
                        data-toggle="tooltip" data-original-title="Edit user">
                        Action Items
                      </a>
                    </td>
                  </tr>
                @endforeach
                @if(sizeof($todays) == 0)
                <tr><td colspan="2" class="text-center">
                  Belum ada Agenda hari ini
                  </td></tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12">
      <button id="checkApi" class="btn btn-outline-primary">API Status</button>
    </div>
  </div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(".clickable-row").click(function() {
      var url = $(this).data("href");
        window.open(url);
    });
    $(".clickable-action").click(function() {
      var id = $(this).data("id");
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
              title: data.action_item.name,
              html : html + pic
            });
      }).always(function(data) {
        // console.log(JSON.stringify(data));
      });;
    });
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
