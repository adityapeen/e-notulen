@extends('user.layouts.template')
@section('title', $title.' - '.config('app.name'))
@section('breadcrumbs', $title.' - '.config('app.name'))

@section('content')
@if(auth()->user()->status == 0)
  <div class="row mt-3">
    <div class="col-12">
      <div class="card">
        <div class="card-body font-weight-bold px-2 py-2">
          Silahkan Lengkapi Profile Anda 
          <a href="{{route('user.profile')}}" class="badge bg-gradient-info btn-info ms-3" >Update Profile</a>
        </div>
      </div>
    </div>
  </div>
  @endif
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
                    <td style="cursor: pointer" class="clickable-action" data-id="{{ $item->id}}">
                      <?= $item->note->name ?>
                      <p class="text-secondary mb-0 text-xs">{{ $item->note->date}}</p>
                    </td>
                    <td class="align-middle">                      
                      {{ $item->due_date}}                     
                    </td>
                    <td class="align-middle">                                        
                      <a href="{{ route('user.notes.evidence', $item->id)}}" class="badge btn-success badge-sm bg-gradient-success" >Eviden</a>                  
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
              <tbody>
                @foreach ($todays as $item)
                  <tr class="clickable-row" data-href="{{ $item->status != "lock" ? "#" : route('user.notes.show', $item->id) }}">
                    <td style="cursor: pointer">
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
                      {{ substr($item->start_time,0,5). ' - '.substr($item->end_time,0,5)}}
                    </td>
                  </tr>
                @endforeach
                @if(sizeof($todays) == 0)
                <tr>
                  <td colspan="2" class="text-center">
                    Belum ada Agenda hari ini
                  </td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
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
  </script>
@endsection
