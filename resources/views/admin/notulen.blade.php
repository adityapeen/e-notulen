@extends('admin.layouts.template')
@section('title', 'Tabel - ' . config('app.name'))
@section('breadcrumbs', 'Tabel - ' . config('app.name'))

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
        <div class="card-footer p-3">
          {{-- <p class="mb-0"><span class="text-success font-weight-bolder text-sm">+55% </span>than lask week</p> --}}
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
        <div class="card-footer p-3">
          {{-- <p class="mb-0"><span class="text-success font-weight-bolder text-sm">+3% </span>than lask month</p> --}}
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
        <div class="card-footer p-3">
          {{-- <p class="mb-0"><span class="text-success font-weight-bolder text-sm">+5% </span>than yesterday</p> --}}
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
                  <tr class="clickable-row" data-href="{{ route('admin.notes.show', $item->id) }}">
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
                      <a href="{{ route('admin.notes.action', $item->id) }}" class="text-info font-weight-bold text-xs"
                        data-toggle="tooltip" data-original-title="Edit user">
                        Action Items
                      </a>
                    </td>
                  </tr>
                @endforeach
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
  <script>
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
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
