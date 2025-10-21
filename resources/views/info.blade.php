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
    .info-section img {
        width: 200px; /* Sesuaikan ukuran logo */
        height: auto;
    }
    
    .info-section h1 {
        font-size: 2rem;
        margin-bottom: 1rem;
        position: relative;
    }
    
    .info-section p {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }
    
    .logout button {
        font-size: 1rem;
        padding: 0.5rem 2rem;
    }
    
    </style>
<div class="container">
    <div class="info-section text-center">
        <!-- Logo -->
        <img src="{{ asset('images/logopadma.png') }}" alt="Logo" class="mb-4">

        <!-- Company Info -->
        <p class="motto">
            <strong>"One Stop Refinish Solution"</strong>
        </p>
        <p>
            <strong>Distributor of:</strong> Sherwin Williams, Debeer Refinish, Medusa Refinish, PPG Refinish, Durr Spraygun.
        </p>
        <p>
            <strong>Alamat:</strong> Jl. Industri Utama 1, blok RR3G, Jababeka 2, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530
        </p>
        <p>
            <strong>Telp:</strong> (021) 89832817
        </p>

        <!-- Logout Button -->
        <div class="logout mt-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>
</div>
    @endsection
@else
    <p>User level is not recognized. Please contact the administrator.</p>
@endif
