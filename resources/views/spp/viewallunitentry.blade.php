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
    flex-wrap: wrap;
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

/* Mobile-specific styles */
@media (max-width: 900px) {
    .header-container {
        flex-direction: column;
        align-items: flex-start;
    }
    .header-container h1 {
        margin-bottom: 10px;
    }
    .header-container .btn-group {
        width: 100%;
        justify-content: flex-start;
    }

    .counts-container,
    .data-table-container {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .counts-container table,
    .data-table-container table {
        display: flex;
        flex-direction: row;
        overflow-x: auto;
        white-space: nowrap;
    }

    .counts-container table thead tr,
    .data-table-container table thead tr {
        display: flex;
        flex-direction: column;
    }

    .counts-container table thead th,
    .data-table-container table thead th {
        display: block;
        width: auto;
        text-align: left;
        padding: 8px;
        font-size: 10px;
    }

    .data-table-container table tbody tr {
        display: flex;
        flex-direction: column;
        min-height: 50px; /* Adjust the minimum height as needed */
    }

    .data-table-container table tbody td {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        min-height: 47.35px; /* Ensure all td elements have the same height */
        min-width: 100px; /* Ensure all td elements have the same width */
        box-sizing: border-box;
    }
}

@media (max-width: 900px) {
    .chart-container {
        width: 250%;
        height: auto;
        margin-bottom: 15px; /* Adjust the margin as needed */
        overflow-x: auto; /* Enable horizontal scrolling if the chart is wider than the container */
    }

    .chart-container canvas {
        width: 250%;
        height: auto; /* Maintain aspect ratio */
    }
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row header-container">
                <div class="col d-flex justify-content-between align-items-center">
                    <h1>ALL Unit Entry</h1>  
                </div>
            </div>

            @php
            $bisnis = request()->query('bisnis');
            $manufaktur = request()->query('manufaktur');
            $dealer = request()->query('dealer');
            $cabang = request()->query('cabang');
            $lokasi = request()->query('lokasi');
            @endphp

            <a href="{{ route('manajemenspp', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-secondary mb-3">Back</a>

            <!-- Dropdown Pilihan Tahun -->
            <div class="mb-3">
                <label for="yearFilter">Pilih Tahun:</label>
                <select id="yearFilter" class="form-control" onchange="updateYear()">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Chart Container -->
            <div class="chart-container mb-3">
                <canvas id="unitEntryChart" width="400" height="100"></canvas>
            </div>

            <!-- Display actual data in a separate table -->
            <div class="data-table-container">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            @foreach($monthlyDataMap as $monthYear => $nospps)
                            <th>
                                {{ $monthYear }} <br>
                                <small>Count: {{ count($nospps) }}</small>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                </table>
            </div>
            {{ $sppData->links() }}
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function updateYear() {
        const year = document.getElementById('yearFilter').value;
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('year', year);
        window.location.search = urlParams.toString();
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('unitEntryChart').getContext('2d');
        
        // Prepare the data for the chart
        const monthlyCounts = {!! json_encode(array_map(function($monthYear) use ($monthlyDataMap) {
            return count($monthlyDataMap[$monthYear]);
        }, array_keys($monthlyDataMap))) !!};
        
        const monthYears = {!! json_encode(array_keys($monthlyDataMap)) !!};

        const unitEntryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthYears,
                datasets: [{
                    data: monthlyCounts,
                    backgroundColor: 'rgba(0, 123, 255, 0.6)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false // Disable the legend (removes the box at the top)
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1 // Ensure y-axis uses integers
                        }
                    }
                }
            }
        });
    });
</script>

@endsection
@else
    <p>User level is not recognized. Please contact the administrator.</p>
@endif
