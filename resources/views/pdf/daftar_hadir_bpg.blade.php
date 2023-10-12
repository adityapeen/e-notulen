<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title></title>
  <style>
    @page {
      margin: 160px 50px 80px 50px;
    }

    header {
      position: fixed;
      top: -120px;
      left: 0px;
      right: 0px;
      height: 50px;
    }

    footer {
      position: fixed;
      bottom: -60px;
      left: 0px;
      right: 0px;
      height: 50px;
    }

    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 12px;
    }

    .table {
      border-collapse: collapse;
      width:100%;
    }

    .table td {
      padding: 5px;
    }

    .text-center {
      text-align: center !important;
    }

    .cell-logo {
      padding: 5 0 0 5;
      height: 30px;
      width: 100px;
      text-align: center !important;
      font-size: 8px;
      font-weight: bold;
    }

    .cell-desc {
      width: 15%;
    }

    .img-logo {
      height: auto;
      width: 60%
    }
  </style>

</head>

<body>
  <header>
    <table border="1" class="table">
      <thead>
        <tr>
          <td rowspan="4" class="cell-logo">
            <img
              src={{ 'data:image/png;base64,' . base64_encode(file_get_contents('./assets/img/logos/logo-esdm.png')) }}
              class="img-logo">
            <br>PPSDM GEOMINERBA
          </td>
          <td class="text-center" style="background-color:rgb(177, 177, 177)"><strong>SISTEM MANAJEMEN MUTU</strong></td>
          <td>No. Dokumen</td>
          <td>: </td>
        </tr>
        <tr>
          <td rowspan="3" class="text-center">
            <h3>{{ $title }}</h3>
          </td>
          <td>Halaman</td>
          <td>:</td>
        </tr>
        <tr>
          <td class="cell-desc"></td>
          <td class="cell-desc">:</td>
        </tr>
        <tr>
          <td></td>
          <td>:</td>
        </tr>
      </thead>
    </table>
    </table>
  </header>
  
  <footer>
    <table border="1" class="table">
        <tr><td class="text-center"><i>Dilarang menyalin atau memperbanyak dokumen ini tanpa ijin tertulis dari SMM PPSDM GEOMINERBA</i></td></tr>
    </table>
  </footer>

  <main>
    <table border="0">
      <tr>
        <td>Tanggal</td>
        <td>: {{ tgl_indo($notes->date) }}</td>
      </tr>
      <tr>
        <td>Tempat</td>
        <td>: {{ $notes->place }}</td>
      </tr>
      <tr>
        <td>Waktu</td>
        <td>: {{ substr($notes->start_time, 0, 5) . ' - ' . substr($notes->end_time, 0, 5) . ' WIB' }}</td>
      </tr>
      <tr>
        <td>Koordinator</td>
        <td>: </td>
      </tr>
      <tr>
        <td>Perihal</td>
        <td>: {{ $notes->name }}</td>
      </tr>
    </table>
    <br>
    <table border="1" class="table">
      <thead>
        <tr class="text-center">
          <td width="3"><b>No.</b></td>
          <td><b>Nama</b></td>
          <td width="150"><b>Unit</b></td>
          <td width="100"><b>No. Tlp</b></td>
          <td width="150"><b>Email</b></td>
          <td width="50"><b>Hadir</b></td>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        @foreach ($attendants as $item)
          <tr>
            <td class="text-center">{{ $no++.'.' }}</td>
            <td>{{ $item->user->name }}</td>
            <td>{{ $item->user->satker->name }}</td>
            <td>{{ $item->user->phone }}</td>
            <td>{{ $item->user->email }}</td>
            <td class="text-center"><?php $date = new DateTime($item->created_at, new DateTimeZone('GMT'));
            echo $date->setTimezone(new DateTimeZone('Asia/Jakarta'))->format('H:i:s');
            ?></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </main>
</body>

</html>
