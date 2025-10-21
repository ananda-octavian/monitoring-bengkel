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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Base Styles (applies to both desktop and mobile) */
        .container-fluid {
            top: 100px;
            padding: 20px;
            margin: 0 auto;
            width: 100%;
            max-width: 1200px;
        }
        .header-container {
            background-color: #f8f9fa;
            padding: 10px;
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-container h1 {
            margin: 0;
            font-size: 1.5rem;
            line-height: 1;
        }
        .header-container .btn-group {
            display: flex;
            gap: 10px;
        }
        .table-container {
            padding: 0px;
        }
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6 !important;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }
        .table th, .table td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        .flex-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .total-container {
            flex: 1;
            text-align: center;
        }
        .horizontal-tables {
            display: flex;
            flex-wrap: wrap;
        }
        .horizontal-tables > div {
            flex: 1 1 33%;
            padding: 3px;
            box-sizing: border-box;
        }
        .btn-dropdown {
    display: inline-block;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    text-align: center;
    padding: 10px 177.5px; /* Adjusted padding for better button fit */
    font-size: 16px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight: 600;
    white-space: nowrap;
    width: auto; /* Allow button width to adjust based on content */
    max-width: 350px;
    transition: background-color 0.3s ease;
    background-color: #007bff; /* Example background color */
    display: flex; /* Added for flex alignment */
    align-items: center; /* Vertically centers text */
    justify-content: center; /* Horizontally centers text */
}

        .btn-unitentry {
             background-color: blue;
        }
        .btn-unitok {
             background-color: green;
        }
        .btn-wip {
             background-color: red;
        }
        .modal-dialog {
            max-width: 50%;
            margin: 1.75rem auto;
        }
        .modal-content {
            border-radius: 0;
        }
        .modal-body {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }
        h2, h5 {
            color: #333;
            padding-bottom: 0.5rem;
        }
        h2 {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        h5 {
            font-size: 0.75rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
/* Color Box Styles */
.custom-blue {
    background-color: #cce5ff; /* Light Blue */
}
.custom-green {
    background-color: #d4edda; /* Light Green */
}
.custom-red {
    background-color: #f8d7da; /* Light Red */
}
    
        /* Mobile Styles */
        @media (max-width: 768px) {
    .container-fluid {
        padding: 10px;
    }
    .header-container h1 {
        font-size: 1.25rem;
    }
    .horizontal-tables > div {
        flex: 1 1 100%; /* Stack columns on smaller screens */
    }
    .btn-dropdown {
        padding: 10px 20px; /* Adjust padding for mobile */
        font-size: 14px;
        width: 100%; /* Ensure button takes full width */
        max-width: none; /* Remove any max-width constraints */
    }
    .modal-dialog {
        max-width: 90%;
    }

        }
    </style>
    
</head>
<body>
<div class="container-fluid">
    <div class="row header-container">
        <div class="col d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-3 mb-md-0">RESUME</h1>
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

            <form method="GET" action="{{ route('resumewip') }}" class="form-inline">
                <input type="hidden" name="bisnis" value="{{ $bisnis }}">
                <input type="hidden" name="manufaktur" value="{{ $manufaktur }}">
                <input type="hidden" name="dealer" value="{{ $dealer }}">
                <input type="hidden" name="cabang" value="{{ $cabang }}">
                <input type="hidden" name="lokasi" value="{{ $lokasi }}">
                
                <div class="form-group mb-2">
                    <label for="month" class="mr-2">Month</label>
                    <select id="month" name="month" class="form-control mr-3">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $selectedMonth == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
            
                <div class="form-group mb-2">
                    <label for="year" class="mr-2">Year</label>
                    <select id="year" name="year" class="form-control mr-3">
                        @for ($y = date('Y') - 10; $y <= date('Y'); $y++)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
            
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-secondary mr-2">Show</button>
                </div>
            
                <div class="form-group mb-2">
                    <a href="{{ route('resumewip', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-secondary">View</a>
                </div>
            </form>
            
            
            

            <div class="horizontal-tables">
                <div class="table-container">
                    <div class="flex-container">
                        <div class="total-container">
                            <h5>Unit Entry : {{ $totalNosppEntry }}</h5>
                        </div>
                        <div class="total-container">
                            <h5>RP Entry : Rp.{{ $totalRpEntry }},00</h5>
                        </div>
                    </div>
                    
                    <button class="btn-dropdown btn-link btn-unitentry" data-toggle="collapse" data-target="#unitEntryTable" aria-expanded="false" aria-controls="unitEntryTable">
                        Unit Entry
                    </button>
                    <div id="unitEntryTable" class="collapse">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>No Polisi</th>
                                    <th>No SPP</th>
                                    <th>RP.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($combinedNosppData as $spp)
                                @php
                                $isDifferentMonth = date('m', strtotime($spp->tglmasuk)) != $selectedMonth || date('Y', strtotime($spp->tglmasuk)) != $selectedYear;
                            @endphp
                            <tr class="{{ $isDifferentMonth ? 'custom-red' : '' }}">
                                        <td>{{ $spp->nopol }}</td>
                                        <td>{{ substr($spp->nospp, 10) }}</td>
                                        <td>Rp.{{ number_format($spp->grandtotal, 0, ',', '.') }},00</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            
                <!-- Ulangi pola yang sama untuk Unit OK dan WIP -->
                <div class="table-container">
                    <div class="flex-container">
                        <div class="total-container">
                            <h5>Unit OK : {{ $totalNosppOk }}</h5>
                        </div>
                        <div class="total-container">
                            <h5>RP OK : Rp.{{ $totalRpOk }},00</h5>
                        </div>
                    </div>
            
                    <button class="btn-dropdown btn-link btn-unitok" data-toggle="collapse" data-target="#unitOkTable" aria-expanded="false" aria-controls="unitOkTable">
                        Unit OK
                    </button>
                    <div id="unitOkTable" class="collapse">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>No Polisi</th>
                                    <th>No SPP</th>
                                    <th>RP.</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unitOkData as $spp)
                                @php
                                $isUnitOkAndReceived = optional($spp->wip->last())->proses == 'Unit OK' && !empty($spp->diterima);
                                $isUnitOkButNotReceived = optional($spp->wip->last())->proses == 'Unit OK' && empty($spp->diterima);
                            @endphp
                            <tr class="{{ $isUnitOkButNotReceived ? 'custom-blue' : '' }} {{ $isUnitOkAndReceived ? 'custom-green' : '' }}">
                                        <td>{{ $spp->nopol }}</td>
                                        <td>{{ substr($spp->nospp, 10) }}</td>
                                        <td>Rp.{{ number_format($spp->grandtotal, 0, ',', '.') }},00</td>
                                        <td>
                                            <a href="#" class="btn open-modal btn-sm" data-toggle="modal" data-target="#Modal1{{ $spp->nospp }}">></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            
                <div class="table-container">
                    <div class="flex-container">
                        <div class="total-container">
                            <h5>WIP : {{ $totalNosppWip }}</h5>
                        </div>
                        <div class="total-container">
                            <h5>RP WIP : Rp.{{ $totalRpWip }},00</h5>
                        </div>
                    </div>
            
                    <button class="btn-dropdown btn-link btn-wip" data-toggle="collapse" data-target="#wipTable" aria-expanded="false" aria-controls="wipTable">
                        WIP
                    </button>
                    <div id="wipTable" class="collapse">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>No Polisi</th>
                                    <th>No SPP</th>
                                    <th>RP.</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($combinedWipData as $spp)
                                @php
                                $isDifferentMonth = date('m', strtotime($spp->tglmasuk)) != $selectedMonth || date('Y', strtotime($spp->tglmasuk)) != $selectedYear;
                            @endphp
                            <tr class="{{ $isDifferentMonth ? 'custom-red' : '' }}">
                                        <td>{{ $spp->nopol }}</td>
                                        <td>{{ substr($spp->nospp, 10) }}</td>
                                        <td>Rp.{{ number_format($spp->grandtotal, 0, ',', '.') }},00</td>
                                        <td>
                                            <a href="#" class="btn open-modal btn-sm" data-toggle="modal" data-target="#Modal2{{ $spp->nospp }}">></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            

@foreach($sppData as $spp)
<!-- Modal 1 -->
<div class="modal fade" id="Modal1{{ $spp->nospp }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel{{ $spp->nospp }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel{{ $spp->nospp }}">Detail Unit OK</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <h2 class="text-center">{{ $spp->nopol }}</h2>
                    @php
                        $latestWip = $spp->wip->sortByDesc('created_at')->first();
                    @endphp
                    @if($latestWip && $latestWip->foto)
                        @foreach(json_decode($latestWip->foto) as $foto)
                            <img src="{{ asset('storage/' . $foto) }}" class="img-thumbnail" style="width: 100%; max-width: 1000px;">
                        @endforeach
                    @else
                        <img src="{{ asset('images/logopadma.png') }}" class="img-thumbnail" style="width: 100%; max-width: 1000px;">
                    @endif
                    <table class="table table-bordered">
                        <tr>
                            <td>Type</td>
                            <td>{{ $spp->type }}</td>
                        </tr>
                        <tr>
                            <td>Proses</td>
                            <td>{{ optional($latestWip)->proses }}</td>
                        </tr>
                        <tr>
                            <td>Selesai</td>
                            <td>{{ optional($latestWip)->stopped_at }}</td>
                        </tr>
                        <tr>
                            <td>Diterima</td>
                            <td>{{ $spp->diterima }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                @if(auth()->user()->level !== 'manajemen')
                @if($latestWip)
                    <form action="{{ route('hapusunitok', ['id_wips' => $latestWip->id_wips, 'bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this Unit OK?')">Delete</button>
                    </form>
                @endif
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach


@foreach($sppData as $spp)
<!-- Modal 2 -->
<div class="modal fade" id="Modal2{{ $spp->nospp }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel{{ $spp->nospp }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel{{ $spp->nospp }}">Detail WIP</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <h2 class="text-center">{{ $spp->nopol }}</h2>
                    @php
                    $latestWip = $spp->wip->sortByDesc('created_at')->first();
                @endphp
                @if($latestWip && $latestWip->foto)
                    @foreach(json_decode($latestWip->foto) as $foto)
                        <img src="{{ asset('storage/' . $foto) }}" class="img-thumbnail" style="width: 100%; max-width: 1000px;">
                    @endforeach
                @else
                    <img src="{{ asset('images/logopadma.png') }}" class="img-thumbnail" style="width: 100%; max-width: 1000px;">
                @endif
                    <table class="table table-bordered">
                        <tr>
                            <td>Type</td>
                            <td>{{ $spp->type }}</td>
                        </tr>
                        <tr>
                            <td>Proses</td>
                            <td>{{ optional($latestWip)->proses }}</td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>{{ optional($latestWip)->keterangan }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('.open-modal').on('click', function() {
        var modalId = $(this).data('target');
        $(modalId).modal('show');
    });
});
</script>
@endsection
@else
    <p>User level is not recognized. Please contact the administrator.</p>
@endif
