@extends('admin.layouts.template')
@section('title', $title . ' - ' . config('app.name'))
@section('breadcrumbs', $title . ' - ' . config('app.name'))

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card mt-4 mb-2">
        <div class="card-header position-relative mt-n4 z-index-2 mx-3 p-0">
          <div class="bg-gradient-info shadow-info border-radius-lg d-flex align-items-center pb-2 pt-2">
            <h6 class="text-capitalize ps-3 text-white">Data Pegawai</h6>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">Nama Pegawai</div>
            <div class="col-md-8 font-weight-bold">{{ $user->name }}</div>
          </div>
          <div class="row">
            <div class="col-md-4">NIP</div>
            <div class="col-md-8 font-weight-bold">{{ $user->nip }}</div>
          </div>
          <div class="row">
            <div class="col-md-4">Satker</div>
            <div class="col-md-8 font-weight-bold">{{ $user->satker->name }}</div>
          </div>
          <div class="row">
            <div class="col-md-4">Pokja</div>
            <div class="col-md-8 font-weight-bold">{{ $user->team == null ? '' : $user->team->name }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php 
    $total = $summary[0]->todo_count + $summary[0]->onprogress_count + $summary[0]->done_count;
    $percent = round(($summary[0]->done_count / $total) * 100, 2);
   ?>

  <div class="row">
    <div class="col-md-6">
      <div class="card my-3">
        <div class="card-header p-3 pb-0">
          <div class="bg-gradient-info shadow-info border-radius-lg d-flex align-items-center pb-2 pt-2">
            <h6 class="text-capitalize ps-3 text-white">Performance</h6>
          </div>
        </div>
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-9">
              <div class="progress-wrapper">
                <div class="progress">
                  <div
                    class="progress-bar bg-gradient-{{ $percent < 50 ? 'danger' : ($percent < 95 ? 'info' : 'success') }}"
                    role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"
                    style="width: {{ round($summary[0]->performance_avg,2) }}%;"></div>
                    
                </div>
              </div>
            </div>
            <div class="col-3">
              <h4 class="mb-0">{{ round($summary[0]->performance_avg,2) }}%</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card my-3">
        <div class="card-header p-3 pb-0">
          <div class="bg-gradient-success shadow-success border-radius-lg d-flex align-items-center pb-2 pt-2">
            <h6 class="text-capitalize ps-3 text-white">Task Done</h6>
          </div>
        </div>
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-10">
              <div class="progress-wrapper">
                <div class="progress">
                  <div
                    class="progress-bar bg-gradient-{{ $percent < 50 ? 'danger' : ($percent < 90 ? 'info' : 'success') }}"
                    role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"
                    style="width: {{ $percent }}%;"></div>
                </div>
              </div>
            </div>
            <div class="col-2">
              <h4 class="mb-0">{{ $percent }} %</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4 mb-3">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header px-3 py-0">
          <div
            class="icon icon-lg icon-shape bg-gradient-primary shadow-primary border-radius-xl mt-n4 position-absolute text-center">
            <i class="material-icons opacity-10">error</i>
          </div>
          <div class="pt-1 text-end">
            <p class="text-capitalize mb-0 text-sm">To Do</p>
            <h4 class="mb-0">{{ $summary[0]->todo_count }}</h4>
          </div>
        </div>
        <hr class="horizontal dark my-3">
      </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header px-3 py-0">
          <div
            class="icon icon-lg icon-shape bg-gradient-info shadow-info border-radius-xl mt-n4 position-absolute text-center">
            <i class="material-icons opacity-10">sync</i>
          </div>
          <div class="pt-1 text-end">
            <p class="text-capitalize mb-0 text-sm">On Progress</p>
            <h4 class="mb-0">{{ $summary[0]->onprogress_count }}</h4>
          </div>
        </div>
        <hr class="horizontal dark my-3">
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-header px-3 py-0">
          <div
            class="icon icon-lg icon-shape bg-gradient-success shadow-success border-radius-xl mt-n4 position-absolute text-center">
            <i class="material-icons opacity-10">check</i>
          </div>
          <div class="pt-1 text-end">
            <p class="text-capitalize mb-0 text-sm">Done</p>
            <h4 class="mb-0">{{ $summary[0]->done_count }}</h4>
          </div>
        </div>
        <hr class="horizontal dark my-3">
      </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header px-3 py-0">
          <div
            class="icon icon-lg icon-shape bg-gradient-dark shadow-dark border-radius-xl mt-n4 position-absolute text-center">
            <i class="material-icons opacity-10">checklist</i>
          </div>
          <div class="pt-1 text-end">
            <p class="text-capitalize mb-0 text-sm">Action Items</p>
            <h4 class="mb-0">
              {{ $total }}</h4>
          </div>
        </div>
        <hr class="horizontal dark my-3">
      </div>
    </div>
  </div>
</div>

  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header position-relative mt-n4 z-index-2 mx-3 p-0">
          <div class="bg-gradient-dark shadow-dark border-radius-lg d-flex align-items-center pb-2 pt-2">
            <h6 class="text-capitalize ps-3 text-white">{{ $title . ' - ' . $user->name }}</h6>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive p-0">
            <table class="align-items-center mb-0 table">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Rapat</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Due Date</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Done Date</th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($tasks as $item)
                  <tr>
                    <td>
                      {{ $item->action->note->name }}
                    </td>
                    <td>
                      {{ $item->action->due_date }}
                    </td>
                    <td class="text-center align-middle text-sm">
                      <span
                        class="badge badge-sm bg-gradient-{{ $item->status == 'done' ? 'success' : ($item->status == 'onprogress' ? 'info' : 'danger') }}">{{ $item->status }}</span>
                    </td>
                    <td>
                      {{ $item->action->done_date }}
                    </td>
                    <td>
                      <span
                        class="badge badge-sm bg-gradient-info">{{ $item->performance != null || $item->performance != '' ? $item->performance . '%' : '' }}</span>
                    </td>
                  </tr>
                @endforeach
                @if (sizeof($tasks) == 0)
                  <tr>
                    <td colspan="2" class="text-center">
                      Belum ada Action Item
                    </td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div>
          {{ $tasks->links('vendor.pagination.bootstrap-5') }}
        </div>
      </div>
    </div>
  </div>


@endsection

@section('script')
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="{{ asset('assets/js/dynamic-form.js') }}"></script>
  <script>
    var api = '{{ url('api/act_pic/') }}/';

    $(document).ready(function() {
      $('.existing').each(function(index) {
        var id = $(this).data('id');
        $(this).select2({
          placeholder: 'Pilih PIC',
        });
        getExisting(id);


      });

      $('#date_first').val(getSeminggu());
      $('.baru-data').last().find('.btn-hapus').css("display", "none");
      $('.baru-data').last().find('.btn-tambah').css("display", "");
    });

    async function getExisting(id) {
      const result = await $.ajax({
        type: 'GET',
        url: api + id
      }).then(function(data) {
        var list = JSON.parse(data);
        var idselected = [];
        list.results.forEach(item => idselected.push(item.id));
        var selector = '#' + id
        $(selector).val(idselected).trigger('change');
      });

      return result;
    }
  </script>
@endsection
