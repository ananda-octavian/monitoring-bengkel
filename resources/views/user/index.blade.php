@extends('layouts.menu')

@section('content')
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manajemen User</title>
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
    </head>
    <body>
    <div class="container-fluid">
        <div class="row header-container mb-4">
            <div class="col d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h1 class="mb-3 mb-md-0">User</h1>
                <div class="search-input">
                    <form action="{{ route('usersearch') }}" method="GET" class="form-inline">
                        <input type="text" name="keyword" placeholder="Cari" value="{{ request('keyword') }}" class="form-control mr-2">
                        <div class="form-group mb-2">
                            <button type="submit" class="btn btn-secondary mr-2" style="margin-top: 10px"><i class="fas fa-search"></i></button>
                        </div>
                        <div class="form-group mb-2">
                            <a href="{{ route('manajemenuser') }}" class="btn btn-secondary mr-2" style="margin-top: 10px">View All</a>
                        </div>
                        <div class="form-group">
                            <a href="{{ route('manajemen') }}" class="btn btn-secondary mr-2">Back</a>
                        </div>
                        <div class="form-group">
                            <a href="{{ route('adduserview') }}" class="btn btn-secondary mr-2">+ Add</a>
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
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Level</th>
                                    <th>Cabang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userData as $user)
                                    <tr>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->level }}</td>
                                        <td>
                                            @if($user->workshop)
                                            {{ $user->workshop->bisnis }} {{ $user->workshop->manufaktur }} {{ $user->workshop->dealer }} {{ $user->workshop->cabang }} {{ $user->workshop->lokasi }}
                                            @else
                                                PT PADMA INDAH PRIMA PERKASA
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('edituserview', ['id_user' => $user->id_user]) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('hapususer', ['id_user' => $user->id_user]) }}" method="POST" style="display:inline-block;">
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
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection
