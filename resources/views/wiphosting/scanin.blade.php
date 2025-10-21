@php
    $layout = null;

    if (auth()->user()->level === 'superadmin') {
        $layout = 'layouts.superadmin';
    } elseif (auth()->user()->level === 'adminpadma') {
        $layout = 'layouts.admin';
    } elseif (auth()->user()->level === 'kepalamekanik') {
        $layout = 'layouts.admin';
    } elseif (auth()->user()->level === 'manajemen') {
        $layout = 'layouts.manajemen';
    }
@endphp

@if ($layout)
    @extends($layout)

@section('content')
<div class="container-fluid">
    <div class="row header-container mb-4">
        <div class="col d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-3 mb-md-0">START {{ substr($sppData->nospp, 10) }} WIP</h1>
        </div>
    </div>

    <div class="row">
        <div class="col">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div class="row mb-3">
                <div class="col d-flex justify-content-between align-items-center">
                    <a href="{{ route('manajemenwip', ['bisnis' => request()->query('bisnis'), 'manufaktur' => request()->query('manufaktur'), 'dealer' => request()->query('dealer'), 'cabang' => request()->query('cabang'), 'lokasi' => request()->query('lokasi')]) }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
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
                            <td>{{ $sppData->workshop->manufaktur }} {{ $sppData->workshop->cabang }} {{ $sppData->workshop->lokasi }}</td>
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
                            <td>Proses</td>
                            <td>{{ optional($sppData->wip->last())->proses }}</td>
                        </tr>
                        <tr>
                            <td>Asuransi</td>
                            <td>{{ $sppData->asuransi }}</td>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="row mb-3">
                <div class="col d-flex justify-content-center align-items-center">
                    @if(auth()->user()->level !== 'manajemen')
                    <a href="#" class="branch-button" data-toggle="modal" data-target="#qrModal">Scan</a>
                    @endif
                </div>
            </div>
            
<!-- QR Code Scanner Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel">Scan QR Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="reader" style="width: 100%; height: 400px;"></div>
                <div id="result" style="margin-top: 20px;"></div>
                <button id="cancel-photo" onclick="stopCamera()" class="btn btn-danger mt-3" style="display: none;">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/html5-qrcode"></script>
<script type="text/javascript">
    const expectedNospp = "{{ $nospp }}"; // Define expected nospp from PHP

    function onScanSuccess(data) {
        let parsedData;

        try {
            // Parse the scanned QR code data as JSON
            parsedData = JSON.parse(data);
        } catch (error) {
            console.error("Error parsing QR code data:", error);
            alert("Invalid QR code data.");
            return;
        }

        // Check if the scanned 'nospp' matches the expected 'nospp'
        if (parsedData.nospp !== expectedNospp) {
            alert("Scanned QR code does not match the required nospp.");
            return;
        }

        // Construct the URL with query parameters
        const baseUrl = "http://127.0.0.1:8000/wip/tambah";
        const queryParams = `?nospp=${encodeURIComponent(parsedData.nospp)}&bisnis=${encodeURIComponent(parsedData.bisnis)}&manufaktur=${encodeURIComponent(parsedData.manufaktur)}&dealer=${encodeURIComponent(parsedData.dealer)}&cabang=${encodeURIComponent(parsedData.cabang)}&lokasi=${encodeURIComponent(parsedData.lokasi)}`;

        // Redirect to the new URL
        window.location.href = baseUrl + queryParams;
    }

    var html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", { fps: 10, qrbox: 250 }
    );
    html5QrcodeScanner.render(onScanSuccess);
</script>
        </div>
    </div>
</div>
@endsection
@else
    <p>User level is not recognized. Please contact the administrator.</p>
@endif


