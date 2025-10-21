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
    max-height: 335px;
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
.custom-blue {
    background-color: #cce5ff; /* Light Blue */
}
.custom-green {
    background-color: #d4edda; /* Light Green */
}
.custom-red {
    background-color: #f8d7da; /* Light Red */
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

/* Background for Unit OK and Received */
.bg-unit-ok-received {
    background-color: #d4edda; /* Light Green */
}

/* Background for Unit OK but Not Received */
.bg-unit-ok-not-received {
    background-color: #cce5ff; /* Light Blue */
}

/* Background for Different Month */
.bg-previous-month-unit {
    background-color: #f8d7da; /* Light Red */
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
                    {{ DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} UNIT ENTRY 
                </h1>
                <div class="d-flex flex-row flex-wrap ml-auto">
                    @if(auth()->user()->level !== 'manajemen')
                    <div class="form-group mb-2 mr-2">
                        <a href="{{ route('tambahsppview', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-secondary">+ Add</a>
                    </div>
                    @endif
                    <div class="form-group mb-2 mr-2">
                        <a href="{{ route('viewallunitentry', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi, 'month' => $selectedMonth, 'year' => $selectedYear]) }}" class="btn btn-secondary"><i class="fas fa-chart-bar"></i></a>
                    </div>
<!-- Filter Button -->
<div class="form-group mb-2 mr-2">
    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#filterModal">
        <i class="fas fa-filter"></i> Filter
    </button>
</div>

<!-- Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel"><i class="fas fa-filter"></i> Filter Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form for filtering data -->
                <form action="{{ route('sppsearch') }}" method="GET">
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

                    <!-- Month Dropdown -->
                    <div class="form-group mb-2">
                        <label for="month" class="mr-2">Month</label>
                        <select id="month" name="month" class="form-control">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $selectedMonth == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Year Dropdown -->
                    <div class="form-group mb-2">
                        <label for="year" class="mr-2">Year</label>
                        <select id="year" name="year" class="form-control">
                            @for ($y = date('Y') - 10; $y <= date('Y'); $y++)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Modal Footer with Filter and View Buttons -->
                    <div class="modal-footer">
                        <a href="{{ route('manajemenspp', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-secondary">Current Data View</a>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- import Button -->
<div class="form-group mb-2 mr-2">
    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#importModal">
        <i class="fas fa-file-excel"></i> Import
    </button>
</div>

<!-- Modal Form for import -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Ms.Excel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form untuk mengunggah file -->
                <form action="{{ route('spp.import', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" 
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" required>
                    <button type="submit" class="btn btn-primary">Upload Excel</button>
                <!-- Tombol untuk mengunduh template -->
                <a href="{{ asset('templates/template.xlsx') }}" class="btn btn-primary" download>
                    <i class="fas fa-file-excel"></i> Download Template
                </a>
                </form>
            </div>
        </div>
    </div>
</div>



                        <!-- Export Button -->
                        <div class="form-group mb-2">
                            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#exportModal"><i class="fas fa-file-excel"></i> Export</button>
                        </div>

<!-- Modal Form for Export -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export to Ms.Excel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('spp.export') }}" method="GET">
                    @csrf
                    @if($sppData->isNotEmpty())
                        @php
                            $workshop = $sppData->first()->workshop ?? null;
                        @endphp

                        @if($workshop)
                            <div class="form-group mb-2">
                                <label for="bisnis">Bisnis</label>
                                <input type="text" name="bisnis" id="bisnis" class="form-control mr-3" value="{{ $workshop->bisnis }}" readonly>
                            </div>
                            <div class="form-group mb-2">
                                <label for="manufaktur">Manufaktur</label>
                                <input type="text" name="manufaktur" id="manufaktur" class="form-control mr-3" value="{{ $workshop->manufaktur }}" readonly>
                            </div>
                            <div class="form-group mb-2">
                                <label for="dealer">Dealer</label>
                                <input type="text" name="dealer" id="dealer" class="form-control mr-3" value="{{ $workshop->dealer }}" readonly>
                            </div>
                            <div class="form-group mb-2">
                                <label for="cabang">Cabang</label>
                                <input type="text" name="cabang" id="cabang" class="form-control mr-3" value="{{ $workshop->cabang }}" readonly>
                            </div>
                            <div class="form-group mb-2">
                                <label for="lokasi">Lokasi</label>
                                <input type="text" name="lokasi" id="lokasi" class="form-control mr-3" value="{{ $workshop->lokasi }}" readonly>
                            </div>
                        @endif
                    @else
                        <p class="text-danger">No data available.</p>
                    @endif

                    <div class="form-group mb-2">
                        <label for="start_date">Start Date:</label>
                        <input type="date" class="form-control mr-2" name="start_date" id="start_date" required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="end_date">End Date:</label>
                        <input type="date" class="form-control mr-2" name="end_date" id="end_date" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Export</button>
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
                <h5>Unit Entry Total: {{ $totalNosppEntry }} Unit</h5>               
                <h5>Rp Total: Rp.{{ $totalRpEntry }},00</h5>
            </div>
            <div class="color-bar-info d-flex justify-content-around mb-4">
                <div class="color-box bg-unit-ok-received">
                    <span>Unit OK and Received</span>
                </div>
                <div class="color-box bg-unit-ok-not-received">
                    <span>Unit OK but Not Received</span>
                </div>
                <div class="color-box bg-previous-month-unit">
                    <span>Previous Month Unit</span>
                </div>
            </div> 
            <div class="table-responsive" style="overflow-x: auto; position: relative;">
                <table class="table table-bordered table-sm" id="sortableTable">
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'number')" style="width: 50px;">No.</th>
                            <th onclick="sortTable(1, 'string')" style="min-width: 120px;">No. SPP/WO</th>
                            <th onclick="sortTable(2, 'string')" style="min-width: 100px;">No. Polisi</th>
                            <th onclick="sortTable(3, 'string')" style="min-width: 120px;">SA</th>
                            <th onclick="sortTable(4, 'string')" style="min-width: 100px;">Asuransi</th>
                            <th onclick="sortTable(5, 'string')" style="min-width: 120px;">Type</th>
                            <th onclick="sortTable(6, 'string')" style="min-width: 100px;">Warna</th>
                            <th onclick="sortTable(7, 'string')" style="min-width: 120px;">Damage</th>
                            <th onclick="sortTable(8, 'date')" style="min-width: 100px;">Tanggal Masuk</th>
                            <th onclick="sortTable(9, 'date')" style="min-width: 120px;">Estimasi</th>
                            <th onclick="sortTable(10, 'string')" style="min-width: 100px;">Proses</th>
                            <th onclick="sortTable(11, 'number')" style="min-width: 120px;">Rp</th>
                            <th style="position: sticky; right: 0; background: #fff; z-index: 1;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($combinedNosppData as $index => $spp)
                            @php
                                // Cek apakah data WIP dan diterima ada
                                $isUnitOkAndReceived = optional($spp->wip->last())->proses == 'Unit OK' && !empty($spp->diterima);
                                $isUnitOkButNotReceived = optional($spp->wip->last())->proses == 'Unit OK' && empty($spp->diterima);
                                $isDifferentMonth = date('m', strtotime($spp->tglmasuk)) != $selectedMonth || date('Y', strtotime($spp->tglmasuk)) != $selectedYear;
                                $today = \Carbon\Carbon::now();
                                $estimasiDate = \Carbon\Carbon::parse($spp->estimasi);
                                $tenggat = $today->diffInDays($estimasiDate, false);
                            @endphp
                            <tr class="{{ $isUnitOkButNotReceived ? 'custom-blue' : '' }} {{ $isUnitOkAndReceived ? 'custom-green' : '' }} {{ $isDifferentMonth ? 'custom-red' : '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ substr($spp->nospp, 10) }}</td>
                                <td>{{ $spp->nopol }}</td>
                                <td>{{ $spp->sa }}</td>
                                <td>{{ $spp->asuransi }}</td>
                                <td>{{ $spp->type }}</td>
                                <td>{{ $spp->warna }}</td>
                                <td>{{ $spp->damage }}</td>
                                <td>{{ $spp->tglmasuk }}</td>
                                <td>{{ $spp->estimasi }} ({{ $tenggat >= 0 ? '+' . round($tenggat) : round($tenggat) }})</td>
                                <td>{{ optional($spp->wip->last())->proses }}</td>
                                <td>{{ number_format($spp->grandtotal, 0, ',', '.') }}</td>
                                <td style="position: sticky; right: 0; background: #fff; z-index: 1;"><a href="#" class="btn open-modal btn-sm" data-toggle="modal" data-target="#Modal1{{ $spp->nospp }}">></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <script>
                let sortDirections = {}; // Untuk menyimpan status urutan (naik/turun) tiap kolom
            
                function sortTable(columnIndex, type) {
                    const table = document.getElementById("sortableTable");
                    const rows = Array.from(table.rows).slice(1); // Mengambil semua baris kecuali header
                    const isAscending = sortDirections[columnIndex] = !sortDirections[columnIndex];
            
                    rows.sort((a, b) => {
                        let cellA = a.cells[columnIndex].innerText.trim();
                        let cellB = b.cells[columnIndex].innerText.trim();
            
                        if (type === 'number') {
                            // Hapus format ribuan dan ubah ke angka untuk pengurutan yang akurat
                            cellA = parseFloat(cellA.replace(/\./g, '').replace(',', '.')) || 0;
                            cellB = parseFloat(cellB.replace(/\./g, '').replace(',', '.')) || 0;
                            return isAscending ? cellA - cellB : cellB - cellA;
                        }
            
                        if (type === 'date') {
                            return isAscending 
                                ? new Date(cellA) - new Date(cellB)
                                : new Date(cellB) - new Date(cellA);
                        }
            
                        // Urutkan sebagai string untuk tipe teks
                        return isAscending 
                            ? cellA.localeCompare(cellB, undefined, { numeric: true })
                            : cellB.localeCompare(cellA, undefined, { numeric: true });
                    });
            
                    rows.forEach(row => table.appendChild(row));
                }
            </script>
            
            
            
            @foreach($sppData as $spp)
            <!-- Modal 1 -->
            <div class="modal fade" id="Modal1{{ $spp->nospp }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel{{ $spp->nospp }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel{{ $spp->nospp }}">Detail Unit Entry</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <h2 class="text-center">{{ $spp->nopol }}</h2>
                                <table class="table table-bordered">
                                    <tr>
                                        <td>No Polisi</td>
                                        <td>{{ $spp->nopol }}</td>
                                    </tr>
                                    <tr>
                                        <td>No SPP/WO</td>
                                        <td>{{ substr($spp->nospp, 10) }}</td>
                                    </tr>
                                    <tr>
                                        <td>SA</td>
                                        <td>{{ $spp->sa }}</td>
                                    </tr>
                                    <tr>
                                        <td>Cabang</td>
                                        <td>{{ $spp->workshop->cabang }} {{ $spp->workshop->manufaktur }} {{ $spp->workshop->lokasi }}</td>
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
                                        <td>Damage</td>
                                        <td>{{ $spp->damage }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Masuk</td>
                                        <td>{{ $spp->tglmasuk }}</td>
                                    </tr>
                                    <tr>
                                        <td>Estimasi</td>
                                        <td>{{ $spp->estimasi }}</td>
                                    </tr>
                                    <tr>
                                        <td>Asuransi</td>
                                        <td>{{ $spp->asuransi }}</td>
                                    </tr>
                                    <tr>
                                        <td>Diterima</td>
                                        <td>{{ $spp->diterima }}</td>
                                    </tr>
                                    <tr>
                                        <td>Proses</td>
                                        <td>{{ optional($spp->wip->last())->proses }}</td>
                                    </tr>
                                    <tr>
                                        <td>Keterangan</td>
                                        <td>{{ optional($spp->wip->last())->keterangan }}</td>
                                    </tr>
                                    <tr>
                                        <td>Create</td>
                                        <td>{{ $spp->created_at }}</td>
                                    </tr>
                                    <tr>
                                        <td>Update</td>
                                        <td>{{ $spp->updated_at }}</td>
                                    </tr>
                                </table>
                       </div>
                        </div>
                        
                        <div class="modal-footer">
                            <a href="{{ route('manajemendetailup', ['nospp' => $spp->nospp, 'bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-secondary">Uraian</a>
                            @if(auth()->user()->level !== 'manajemen')
                            <td>
                                <a href="{{ route('editsppview', ['nospp' => $spp->nospp, 'bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('hapusspp', ['nospp' => $spp->nospp, 'bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this SPP Data?')">Delete</button>
                                </form>
                            </td>
                            @endif
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        </div>
    </div>

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