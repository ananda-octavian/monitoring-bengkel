@extends('layouts.menu')

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
    .button-spacing {
        margin-right: 10px;
    }

    @media (max-width: 767.98px) {
        .button-spacing {
            margin-right: 0;
            margin-bottom: 10px;
        }
        .header-container h1 {
            font-size: 1.2rem;
        }
        .table-container {
            padding: 0 10px;
        }
    }
</style>
<div class="container-fluid">
    <div class="row header-container mb-4">
        <div class="col d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h1 class="mb-3 mb-md-0">WORKSHOP</h1>
            <div class="search-input">
                <form action="{{ route('workshopsearch') }}" method="GET" class="form-inline">
                    <!-- Search Keyword Input -->
                    <input type="text" name="keyword" placeholder="Cari" value="{{ request('keyword') }}" class="form-control mr-2">
                    <!-- Search Button -->
                    <div class="form-group mb-2">
                        <button type="submit" class="btn btn-secondary mr-2" style="margin-top: 10px"><i class="fas fa-search"></i></button>
                    </div>
                    <!-- View All Data Button -->
                    <div class="form-group mb-2">
                        <a href="{{ route('manajemenworkshop') }}" class="btn btn-secondary mr-2" style="margin-top: 10px">View All</a>
                    </div>
                    <div class="form-group">
                        <a href="{{ route('manajemen') }}" class="btn btn-secondary mr-2" >Back</a>
                    </div>
                    <div class="form-group">
                        <a href="{{ route('addworkshopview') }}" class="btn btn-secondary mr-2">+ Add</a>
                    </div>
                </form>
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
            <div class="table-container">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Kode Bengkel</th>
                            <th>Bisnis</th>
                            <th>Manufaktur</th>
                            <th>Dealer</th>
                            <th>Cabang</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workshopData as $workshop)
                            <tr>
                                <td>{{ $workshop->id_bengkel }}</td>
                                <td>{{ $workshop->bisnis }}</td>
                                <td>{{ $workshop->manufaktur }}</td>
                                <td>{{ $workshop->dealer }}</td>
                                <td>{{ $workshop->cabang }}</td>
                                <td>{{ $workshop->lokasi }}</td>
                                <td>
                                    <a href="{{ route('editworkshopview', ['id_bengkel' => $workshop->id_bengkel]) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('hapusworkshop', ['id_bengkel' => $workshop->id_bengkel]) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection
