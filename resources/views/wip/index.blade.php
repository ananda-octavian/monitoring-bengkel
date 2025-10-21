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
    <title>Manajemen SPP</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container-fluid {
    top: 100px;
    padding: 0;
    margin: 0;
    width: 100%;
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
    overflow-x: auto;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6 !important;
}

.table-responsive {
    position: relative;
    max-height: 300px;
}

.table thead th {
    position: sticky;
    top: 0; /* Stick the thead to the top when scrolling */
    background-color: #ffffff; /* Background color to prevent overlap */
    z-index: 10; /* Ensure thead is on top of tbody content */
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.1); /* Optional: to add a subtle shadow */
}

th {
        cursor: pointer;
    }

.modal-dialog {
    max-width: 90%;
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
    border-bottom: 2px solid #ddd;
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
.late-row {
    background-color: #f8d7da; /* Warna merah muda untuk data yang melewati estimasi */
}

.job-dispatch-late {
    background-color: #fff3cd; /* Warna kuning muda untuk proses 'Job Dispatch' lebih dari sehari */
}

.color-bar-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.color-box {
    display: flex;
    align-items: center;
    padding: 10px;
    width: 150px;
    height: 25px;
    border-radius: 5px;
    color: #333;
    font-weight: bold;
    text-align: center;
    justify-content: center;
    font-size: 10px;
}

/* Background for Unit OK but Not Received */
.bg-unit-past-estimate {
    background-color: #f8d7da; /* Light Blue */
}

/* Background for Different Month */
.bg-job-dispatch-late {
    background-color: #fff3cd; /* Light Red */
}

/* Mobile Styles */
@media (max-width: 901px) {
    /* Adjusting header for mobile */
    .header-container {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-container h1 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    /* Buttons layout on mobile */
    .header-container .d-flex {
        flex-direction: row;
        flex-wrap: nowrap; /* Prevent buttons from wrapping into a new line */
    }

    .header-container .form-group {
        margin-right: 5px; /* Add spacing between buttons */
    }

    .table-responsive {
    position: relative;
    max-height: 750px;
}

    /* Modal adjustments for mobile */
    .modal-dialog {
        max-width: 100%;
        margin: 0;
    }

    /* Image responsive in modal */
    .modal-body img {
        width: 100%;
        height: auto;
    }

    /* Color bar adjustments for mobile */
    .color-bar-info {
        display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    }

    .color-box {
        width: 100%; /* Full width for mobile */
        text-align: center; /* Align text to the left */
        padding: 8px;
        font-size: 8px; /* Slightly larger font for readability */
    }
}


    </style>
    
    
</head>
<body>
    <div class="container-fluid">
        <div class="row header-container mb-4">
            <div class="col d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h1 class="mb-3 mb-md-0">
                    WIP
                </h1>
                <div class="d-flex flex-row flex-wrap ml-auto">
<!-- Search Button -->
<div class="form-group mb-2 mr-2">
    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#searchModal">
        <i class="fas fa-search"></i>
    </button>
</div>

<!-- Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel"><i class="fas fa-search"></i> Search Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form for searching data -->
                <form action="{{ route('wipsearch', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" method="GET">
                    @csrf

                    <!-- Hidden fields for bisnis, manufaktur, cabang, and lokasi -->
                    <input type="hidden" name="bisnis" value="{{ $bisnis }}">
                    <input type="hidden" name="manufaktur" value="{{ $manufaktur }}">
                    <input type="hidden" name="dealer" value="{{ $dealer }}">
                    <input type="hidden" name="cabang" value="{{ $cabang }}">
                    <input type="hidden" name="lokasi" value="{{ $lokasi }}">

                    <!-- Keyword Search Input -->
                    <div class="form-group mb-2">
                        <label for="keyword">Search Keyword</label>
                        <input type="text" name="keyword" id="keyword" placeholder="Cari" value="{{ request('keyword') }}" class="form-control" style="width: 100%;">
                    </div>
                    <!-- Modal Footer with Search and View Buttons -->
                    <div class="modal-footer">
                        <a href="{{ route('manajemenwip', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-secondary">Current Data View</a>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Filter Button -->
    <div class="form-group mb-2">
        <button type="button" class="btn btn-secondary mr-2" data-toggle="modal" data-target="#filterModal"><i class="fas fa-chart-bar"></i></button>
    </div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">WIP Filters</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled">
                    @foreach($statusCounts as $status => $count)
                        <li>
                            <a class="dropdown-item" href="{{ url('wip?filter=' . $status .  '&bisnis=' . $bisnis . '&manufaktur=' . $manufaktur . '&dealer=' . $dealer . '&cabang=' . $cabang . '&lokasi=' . $lokasi) }}">
                                {{ $status }} ({{ $count }})
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
                </form>
            </div>
        </div>
    </div>
</div>


 
            </div>
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

            
                <div class="col-12 mb-3 d-flex justify-content-between">
                    <h5>WIP Total: {{ $totalWipCount }} Unit</h5>
                    <h5>Rp Total: {{ $totalRpWip }},00</h5>
                </div>
                <div class="color-bar-info d-flex justify-content-around mb-4">
                    <div class="color-box bg-unit-past-estimate">
                        <span>Unit Late OTD</span>
                    </div>
                    <div class="color-box bg-job-dispatch-late">
                        <span>Pending Job Dispatch</span>
                    </div>
                </div> 
                <div class="table-responsive" style="overflow-x: auto; position: relative;">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No.</th>
                                <th style="min-width: 120px;">No. SPP/WO</th>
                                <th style="min-width: 100px;">No. Polisi</th>
                                <th style="min-width: 120px;">Proses</th>
                                <th style="min-width: 150px;">Estimasi</th>
                                <th style="min-width: 100px;">Rp</th>
                                <th style="min-width: 120px;">Data Foto</th>
                                <th style="position: sticky; right: 0; background: #fff; z-index: 1;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $nonJobDispatch = $sppData->filter(function($spp) {
                                    return optional($spp->wip->last())->proses !== 'Job Dispatch';
                                })->sortBy('estimasi');
                                
                                $jobDispatchLate = $sppData->filter(function($spp) {
                                    return optional($spp->wip->last())->proses === 'Job Dispatch' && \Carbon\Carbon::parse($spp->tglmasuk)->diffInHours(now()) > 24;
                                })->sortBy('estimasi');
                    
                                $jobDispatchOnTime = $sppData->filter(function($spp) {
                                    return optional($spp->wip->last())->proses === 'Job Dispatch' && \Carbon\Carbon::parse($spp->tglmasuk)->diffInHours(now()) <= 24;
                                })->sortBy('estimasi');
                            @endphp
                            
                            @foreach($nonJobDispatch->merge($jobDispatchLate)->merge($jobDispatchOnTime) as $spp)
                            @php
                                $isLate = \Carbon\Carbon::parse($spp->estimasi)->lt(now());
                                $isJobDispatchLate = optional($spp->wip->last())->proses === 'Job Dispatch' && \Carbon\Carbon::parse($spp->tglmasuk)->diffInDays(now()) > 1;
                                $today = \Carbon\Carbon::now();
                                $estimasiDate = \Carbon\Carbon::parse($spp->estimasi);
                                $tenggat = $today->diffInDays($estimasiDate, false);
                            @endphp
                            <tr class="{{ $isLate ? 'late-row' : '' }} {{ $isJobDispatchLate ? 'job-dispatch-late' : '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ substr($spp->nospp, 10) }}</td>
                                <td>{{ $spp->nopol }}</td>
                                <td>{{ optional($spp->wip->last())->proses }}</td>
                                <td>
                                    {{ $spp->estimasi }}
                                    @php
                                        $tenggatFormatted = $tenggat >= 0 ? '+'.round($tenggat) : round($tenggat);
                                    @endphp
                                    ({{ $tenggatFormatted }})
                                </td>
                                <td>{{ number_format($spp->grandtotal, 0, ',', '.') }}</td>
                                <td>Data Foto ({{ $spp->wip->count() }})</td>
@php
    $lastWip = \App\Models\Wip::where('nospp', $spp->nospp)
                ->latest('id_wips')
                ->first();

    $buttonClass = $lastWip && $lastWip->stopped_at ? 'btn-success' : 'btn-danger';
@endphp

<td style="position: sticky; right: 0; background: #fff; z-index: 1;">
    <a href="#" class="btn {{ $buttonClass }} open-modal" data-toggle="modal" data-target="#Modal1{{ $spp->nospp }}">
        <i class="fas fa-arrow-right"></i>
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
</div>

@foreach($sppData as $spp)
<!-- Modal 1 -->
<div class="modal fade" id="Modal1{{ $spp->nospp }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel{{ $spp->nospp }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel{{ $spp->nospp }}">Detail {{ substr($spp->nospp, 10) }} WIP</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <h2 class="text-center">{{ substr($spp->nospp, 10) }}</h2>
                    <table class="table table-bordered">
                        <tr>
                            <td>No Polisi</td>
                            <td>{{ $spp->nopol }}</td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td>{{ $spp->type }}</td>
                        </tr>
                        <tr>
                            <td>Warna</td>
                            <td>{{ $spp->warna }}</td>
                        </tr>
                        <tr>
                            <td>Grandtotal</td>
                            <td>{{ number_format($spp->grandtotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Proses</td>
                            <td>{{ optional($spp->wip->last())->proses }}</td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>{{ optional($spp->wip->last())->keterangan }}</td>
                        </tr>
                    </table>
                </div>
                <div class="row header-container">
                    <div class="col d-flex justify-content-between align-items-center">
                        @php
                            // Get the latest WIP record for the current nospp
                            $latestWip = \App\Models\Wip::where('nospp', $spp->nospp)
                                ->orderByDesc('id_wips')
                                ->first();
                        @endphp
                    
                        @if(auth()->user()->level !== 'manajemen')
                            @if(!$latestWip || $latestWip->stopped_at !== null)
                                <!-- Show "START" button if there's no active WIP or the last one is stopped -->
                                <a href="{{ route('tambahwipview', ['nospp' => $spp->nospp, 'bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-secondary">START</a>
                            @else
                                <!-- Show "STOP" button if there's an active WIP without a stopped_at timestamp -->
                                <a href="{{ route('stopwip', ['nospp' => $spp->nospp, 'bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-secondary">STOP</a>
                            @endif
                        @endif
                    </div>
                    
                    
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Proses</th>
                            <th style="min-width: 100px;">START</th>
                            <th style="min-width: 100px;">STOP</th>
                            <th style="min-width: 100px;">TIME</th>
                            <th style="min-width: 100px;">Foto</th>
                            <th style="position: sticky; right: 0; background: #fff; z-index: 1;"></th>
                            @if(auth()->user()->level !== 'manajemen')
                            <th style="min-width: 100px;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($spp->wip->sortByDesc('created_at') as $wip)
                        @php
                            // Hitung perbedaan waktu antara `created_at` dan `stopped_at` untuk setiap wip
                            $startedAt = \Carbon\Carbon::parse($wip->created_at);
                            $stoppedAt = \Carbon\Carbon::parse($wip->stopped_at);
                    
                            // Hitung total menit antara created_at dan stopped_at
                            $totalMinutes = $startedAt->diffInMinutes($stoppedAt);
                    
                            // Konversi total menit menjadi jam dan menit
                            $totalHours = intdiv($totalMinutes, 60);  // Total jam
                            $remainingMinutes = $totalMinutes % 60;   // Sisa menit
                        @endphp
                        <tr>
                            <td>{{ $wip->proses }}</td>
                            <td>{{ $wip->created_at }}</td>
                            <td>{{ $wip->stopped_at }}</td>
                            <td>{{ $totalHours }} jam, {{ $remainingMinutes }} menit</td>
                            <td>
                                @if($wip->foto)
                                @foreach(json_decode($wip->foto) as $foto)
                                <img src="{{ asset('storage/' . $foto) }}" class="img-thumbnail" style="width: 100px; max-width: 100px;">
                                @endforeach
                                @else
                                <img src="{{ asset('images/logopadma.png') }}" class="img-thumbnail" style="width: 100%; max-width: 100px;">
                                @endif
                            </td>
                            <td style="position: sticky; right: 0; background: #fff; z-index: 1;">
                                <a href="#" class="btn open-modal" data-toggle="modal" data-target="#Modal2{{ $wip->id_wips }}">></a>
                            </td>
                            @if(auth()->user()->level !== 'manajemen')
                            <td>
                                <form action="{{ route('hapuswip', ['id_wips' => $wip->id_wips, 'bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this WIP?')">Delete</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endforeach

@foreach($sppData as $spp)
    <!-- Modal 2 -->
    @foreach($spp->wip as $wip)
    <div class="modal fade" id="Modal2{{ $wip->id_wips }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel{{ $wip->id_wips }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel{{ $wip->id_wips }}">Detail {{ substr($spp->nospp, 10) }} WIP</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <h2 class="text-center">{{ substr($spp->nospp, 10) }} {{ $wip->proses }}</h2>
                        @if($wip->foto)
                            @foreach(json_decode($wip->foto) as $foto)
                                <img src="{{ asset('storage/' . $foto) }}" class="img-thumbnail" style="width: 100%; max-width: 1100px;">
                            @endforeach
                        @else
                            <img src="{{ asset('images/logopadma.png') }}" class="img-thumbnail" style="width: 100%; max-width: 1100px;">
                        @endif
                        <table class="table table-bordered">
                            <tr>
                                <td>No Polisi</td>
                                <td>{{ $spp->nopol }}</td>
                            </tr>
                            <tr>
                                <td>Start</td>
                                <td>{{ $wip->created_at }}</td>
                            </tr>
                            <tr>
                                <td>Stop</td>
                                <td>{{ $wip->stopped_at }}</td>
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
@endforeach

@endsection

@section('scripts')
<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS, Popper.js, dan jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
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