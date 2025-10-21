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
            <h1 class="mb-3 mb-md-0">URAIAN</h1>
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
            <div class="table-container">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <td>No SPP</td>
                            <td>{{ substr($sppData->nospp, 8) }}</td>
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
                <div class="col d-flex justify-content-between align-items-center">
                    <a href="{{ route('manajemenspp', ['bisnis' => request()->query('bisnis'), 'manufaktur' => request()->query('manufaktur'), 'dealer' => request()->query('dealer'), 'cabang' => request()->query('cabang'), 'lokasi' => request()->query('lokasi')]) }}" class="btn btn-secondary">Back</a>
                    @if(auth()->user()->level !== 'manajemen')
                        <a href="{{ route('tambahdetailupview', ['nospp' => $sppData->nospp, 'bisnis' => request()->query('bisnis'), 'manufaktur' => request()->query('manufaktur'), 'dealer' => request()->query('dealer'), 'cabang' => request()->query('cabang'), 'lokasi' => request()->query('lokasi')]) }}" class="btn btn-secondary">+ Add</a>
                    @endif
                </div>
            </div>

            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Uraian Pekerjaan</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detailupData as $detailup)
                        <tr>
                            <td>{{ $detailup->namauraian }}</td>
                            <td>{{ number_format($detailup->hargauraian, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" align="center">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td colspan="1" align="right"><b>Grand Total:</b></td>
                        <td>{{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@else
    <p>User level is not recognized. Please contact the administrator.</p>
@endif
