<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>How to Generate QR Code in Laravel 9</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>

<body>

    <div class="container mt-4">

        <div class="card">
            <div class="card-header bg-primary bg-gradient text-light text-center font-weight-bold">
                <h5>{{ $note->name }}</h5>
            </div>
            <div class="card-body text-center"><?php $url = route('check_in',$note->id) ?>
                {!! QrCode::size(300)->generate($url) !!}
                <div class="row justify-content-center mt-3 font-weight-bold"><?= $url ?></div>
            </div>
            <div class="card-footer bg-primary text-light bg-gradient text-center">
                Silahkan Scan Barcode diatas
            </div>
        </div>
    </div>
</body>
</html>