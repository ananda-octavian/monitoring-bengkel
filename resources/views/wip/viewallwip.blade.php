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

    @media (max-width: 900px) {
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

    .nopol {
        display: inline-block;
        padding: 2px;
        font-size: 10px;
        white-space: nowrap;
        margin-right: 10px; /* Adjust this value for spacing between nopol entries */
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


}

</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row header-container">
                <div class="col d-flex justify-content-between align-items-center">
                    <h1>ALL WIP</h1>  
                </div>
            </div>
            @php
            $bisnis = request()->query('bisnis');
            $manufaktur = request()->query('manufaktur');
            $dealer = request()->query('dealer');
            $cabang = request()->query('cabang');
            $lokasi = request()->query('lokasi');
        @endphp

<a href="{{ route('manajemenwip', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-secondary mb-3">Back</a>
            <!-- Chart Container -->
            <div class="chart-container mb-3">
                <canvas id="processChart" width="400" height="100"></canvas>
            </div>

<!-- Display actual data in a separate table -->
<div class="data-table-container">
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                @foreach($processes as $process)
                <th>
                    {{ $process }} <br>
                    <small>Count: {{ count($processMap[$process]) }}</small>
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($processes as $process)
                <td>
                    @foreach($processMap[$process] as $nopol)
                        <div class="nopol">{{ $nopol }} </div>
                    @endforeach
                </td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>


            {{ $sppData->links() }}
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('processChart').getContext('2d');
        const processCounts = {!! json_encode(array_map(function($process) use ($processMap) {
            return count($processMap[$process]);
        }, $processes)) !!};
        const processLabels = {!! json_encode($processes) !!};

        // Define the color order: red, orange, yellow, green, blue (largest to smallest)
        const colorOrder = [
            'rgba(255, 0, 0, 0.6)',    // Red for largest
            'rgba(255, 165, 0, 0.6)',  // Orange
            'rgba(255, 255, 0, 0.6)',  // Yellow
            'rgba(0, 128, 0, 0.6)',    // Green
            'rgba(0, 0, 255, 0.6)'     // Blue for smallest
        ];

        // Get unique counts and sort them in descending order
        const uniqueCounts = [...new Set(processCounts)].sort((a, b) => b - a);

        // Map each unique count to a color in the order specified
        const colorMap = {};
        uniqueCounts.forEach((count, index) => {
            colorMap[count] = colorOrder[index % colorOrder.length];
        });

        // Assign colors to each process count
        const backgroundColors = processCounts.map(count => colorMap[count]);

        const processChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: processLabels,
                datasets: [{
                    label: '',  // Empty label to remove 'WIP'
                    data: processCounts,
                    backgroundColor: backgroundColors,
                    borderColor: 'rgba(0, 0, 0, 0.1)',
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
