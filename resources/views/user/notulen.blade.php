@extends('user.layouts.template')
@section('title', 'Tabel - ' . config('app.name'))
@section('breadcrumbs', 'Tabel - ' . config('app.name'))

@section('content')
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
                  <tr class="clickable-row" data-href="{{ $item->status != "lock" ? "#" : $item->file_notulen }}">
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
                      {{ substr($item->start_time,0,5). ' - '.substr($item->start_time,0,5)}}
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

@endsection

@section('script')
  <script>
    $(".clickable-row").click(function() {
      var url = $(this).data("href");
        window.open(url);
    });
  </script>
@endsection
