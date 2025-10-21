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
            margin-top: 30px;
            width: 100%;
        }
        .branch-button {
            display: inline-block;
            padding: 25px;
            background-color: #0162AF;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease; /* Transition for hover effects */
        }
    
        .branch-button {
            width: 350px;
            height: 225px;
        }
        .branch-button:hover {
            background-color: #014c84; /* Darker blue on hover */
            transform: translateY(-5px); /* Slight upward movement on hover */
        }
    
        .container-fluid {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
    
        .d-flex {
            display: flex;
            justify-content: space-between;
            flex: 1;
        }
    
        .chart-container {
            max-width: 1000px;
            margin: 0 auto;
            height: 130px; 
        }

        @media (max-width: 991px) {
            .d-flex {
                display: flex;
            justify-content: space-between;
            flex: 1;
            }

            .branch-button {
            display: inline-block;
            padding: 25px;
            background-color: #0162AF;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            font-size: 100%;
            transition: background-color 0.3s ease, transform 0.3s ease; /* Transition for hover effects */
        }
    
        .branch-button {
            width: 340px;
            height: 385px;
        }
        .branch-button:hover {
            background-color: #014c84; /* Darker blue on hover */
            transform: translateY(-5px); /* Slight upward movement on hover */
        }

            .chart-container {
                max-width: 90%;
                height: 250px; /* Mengurangi tinggi diagram untuk tampilan mobile */
            }
        }

        @media (max-width: 450px) {
            .d-flex {
                display: flex;
            justify-content: space-between;
            flex: 1;
            }

            .branch-button {
            display: inline-block;
            padding: 25px;
            background-color: #0162AF;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            font-size: 100%;
            transition: background-color 0.3s ease, transform 0.3s ease; /* Transition for hover effects */
        }
    
        .branch-button {
            width: 240px;
            height: 285px;
        }
        .branch-button:hover {
            background-color: #014c84; /* Darker blue on hover */
            transform: translateY(-5px); /* Slight upward movement on hover */
        }

            .chart-container {
                max-width: 90%;
                height: 150px; /* Mengurangi tinggi diagram untuk tampilan mobile */
            }
        }

        @media (min-width: 900px) {
            .branch-button {
                flex: 1;
            }

            .chart-container {
                max-width: 50%;
            }
        }

        @media (max-width: 991px) {
    .form-inline .form-group {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        width: 50%;
    }

    .form-inline .form-group label {
        margin-bottom: 5px;
        width: 50%;
    }

    .form-inline .form-group select,
    .form-inline .form-group button,
    .form-inline .form-group a {
        width: 95%;
        margin-bottom: 10px;
    }

    .form-inline .form-group:last-child {
        margin-bottom: 0;
    }
}

    </style>

    <div class="container-fluid">
        @php
            $bisnis = request()->query('bisnis');
            $manufaktur = request()->query('manufaktur');
            $cabang = request()->query('cabang');
            $lokasi = request()->query('lokasi');
        @endphp

<form method="GET" action="{{ route('home') }}" class="form-inline">
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
        <a href="{{ route('home', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-secondary mr-2">Current Data View</a>
    </div>
</form>


<div class="d-flex justify-content-between mb-2">
    <!-- UNIT ENTRY -->
    @if(auth()->user()->level !== 'kepalamekanik')
    <a href="{{ route('manajemenspp', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi, 'month' => $selectedMonth, 'year' => $selectedYear]) }}" class="branch-button text-center">
        <div>UNIT ENTRY</div>
        <p style="font-size: 12px; margin: 0;">[Unit : {{ $totalNosppEntry }}]</p>
        <p style="font-size: 12px; margin: 0;">[RP : {{ $totalRpEntry }},00]</p>
        <img src="{{ asset('images/car-repair.png') }}" alt="Gambar Unit Entry" class="img-fluid mt-2" style="max-width: 200px; max-height: 100px;">
    </a>
    @endif

    <!-- UNIT WIP -->
    @if(auth()->user()->level !== 'adminpadma')
    <a href="{{ route('manajemenwip', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="branch-button text-center">
        <div>UNIT WIP</div>
        <p style="font-size: 12px; margin: 0;">[Unit : {{ $totalNosppWip }}]</p>
        <p style="font-size: 12px; margin: 0;">[RP : {{ $totalRpWip }},00]</p>
        <img src="{{ asset('images/car-painting.png') }}" alt="Gambar Unit WIP" class="img-fluid mt-2" style="max-width: 200px; max-height: 100px;">
    </a>
    @endif
</div>


<div class="d-flex justify-content-center">
    <a href="{{ route('resumewip', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi, 'month' => $selectedMonth, 'year' => $selectedYear]) }}" class="branch-button" style="width: 100%;">
        <div>RESUME UNIT</div>
        <p style="font-size: 12px;">[Unit Entry : {{ $totalNosppEntry }} ({{ $totalRpEntry }},00)]  [Unit OK    : {{ $totalNosppOk }} ({{ $totalRpOk }},00)]  [Unit WIP        : {{ $totalNosppWip }} ({{ $totalRpWip }},00)]</p>
        <div class="chart-container">
            <canvas id="myChart"></canvas>
        </div>
    </a>
</div>
</div>

<!-- Chart.js Setup -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('myChart').getContext('2d');
        const totalNosppEntry = @json($totalNosppEntry);
        const totalNosppOk = @json($totalNosppOk);
        const totalNosppWip = @json($totalNosppWip);
        
        const totalRpEntry = @json($totalRpEntry).replace(/\./g, '').replace(',', '.');
        const totalRpOk = @json($totalRpOk).replace(/\./g, '').replace(',', '.');
        const totalRpWip = @json($totalRpWip).replace(/\./g, '').replace(',', '.');
    
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Unit Entry', 'Unit OK', 'Unit WIP'],
                datasets: [{
                    data: [totalNosppEntry , totalNosppOk, totalNosppWip],
                    backgroundColor: [
                        'rgba(54, 162, 235, 1)', // Blue
                        'rgba(75, 192, 192, 1)', // Green
                        'rgba(255, 99, 132, 1)'  // Red
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)', // Blue border
                        'rgba(75, 192, 192, 1)', // Green border
                        'rgba(255, 99, 132, 1)'  // Red border
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Disable the legend
                    },
                    datalabels: {
                        anchor: 'center',
                        align: 'center',
                        color: 'white',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        ticks: {
                            color: 'white',
                            beginAtZero: true
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.3)' 
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
