@extends('layouts.menu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="table-container">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <td>No SPP</td>
                            <td>{{ substr($sppData->nospp, 10) }}</td>
                        </tr>
                        <tr>
                            <td>No Polisi</td>
                            <td>{{ $sppData->nopol }}</td>
                        </tr>
                        <tr>
                            <td>SA</td>
                            <td>{{ $sppData->sa }}</td>
                        </tr>
                        <tr>
                            <td>Cabang</td>
                            <td>{{ $sppData->workshop->cabang }} {{ $sppData->workshop->manufaktur }} {{ $sppData->workshop->lokasi }}</td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td>{{ $sppData->type }}</td>
                        </tr>
                        <tr>
                            <td>Warna</td>
                            <td>{{ $sppData->warna }}</td>
                        </tr>
                        <tr>
                            <td>Damage</td>
                            <td>{{ $sppData->damage }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Masuk</td>
                            <td>{{ $sppData->tglmasuk }}</td>
                        </tr>
                        <tr>
                            <td>Estimasi</td>
                            <td>{{ $sppData->estimasi }}</td>
                        </tr>
                        <tr>
                            <td>Asuransi</td>
                            <td>{{ $sppData->asuransi }}</td>
                        </tr>
                    </thead>
                </table>
                <!-- QR Code Section -->
                <div class="text-center mt-4">
                    <h5>QR Code:</h5>
                    <?php
                    // Prepare the information as a JSON object
                    $info = json_encode([
                        'nospp' => $sppData->nospp,
                        'bisnis' => $bisnis,
                        'manufaktur' => $manufaktur,
                        'dealer' => $dealer,
                        'cabang' => $cabang,
                        'lokasi' => $lokasi,
                    ]);
                    echo QrCode::size(400)->generate($info);
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                window.print();
            </script>
        </div>
    </div>
</div>
@endsection
