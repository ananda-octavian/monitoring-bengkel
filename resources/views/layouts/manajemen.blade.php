@php
    $username = session('username', '');
    $bisnis = request()->query('bisnis');
    $manufaktur = request()->query('manufaktur');
    $dealer = request()->query('dealer');
    $cabang = request()->query('cabang');
    $lokasi = request()->query('lokasi');
    $userLevel = Auth::user()->level; // Mengambil level dari user yang terautentikasi
    $redirectUrl = in_array($userLevel, ['superadmin', 'manajemen'])
        ? route('lokasi', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi])
        : route('logout', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIPP Paint Stage Monitoring</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            display: flex;
            margin: 0;
            height: 100vh;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 50px;
            background-color: whitesmoke;
            color: #ccc;
            height: 100vh;
            position: fixed;
            top: 50px;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }
        .sidebar a {
            text-decoration: none;
            color: #0162AF;
            padding: 10px 0;
            width: 100%;
            text-align: center;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #007bff;
        }
        .content {
            margin-left: 60px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        .header {
            background-color: whitesmoke;
            color: white;
            padding: 0 10px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .header img {
            height: 45px;
            width: 135px;
        }
        .header .search-input {
            display: flex;
            align-items: center;
        }
        .header .search-input input {
            margin-right: 10px;
        }
        .main-content {
            flex-grow: 1;
            overflow-y: auto;
            margin-top: 50px;
            padding: 0;
        }
        .username {
            font-size: 9px;
            color: #333;
            text-align: left;
            margin: 1rem 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            line-height: 1.2;
            background: linear-gradient(to right, #0162AF, #0162AF);
            -webkit-background-clip: text;
            color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1); 
        }
        .workshop {
            font-size: 9px;
            color: #333;
            text-align: left;
            margin: 1rem 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            line-height: 1.2;
            background: linear-gradient(to right, #0162AF, #0162AF);
            -webkit-background-clip: text;
            color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1); 
        }
        @media (max-width: 767.98px) {
            .header img {
            height: 22.5px;
            width: 67.5px;
        }
        .username {
            font-size: 5px;
            color: #333;
            text-align: left;
            margin: 1rem 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            line-height: 1.2;
            background: linear-gradient(to right, #1d99ff, #0162AF);
            -webkit-background-clip: text;
            color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1); 
        }
        .workshop {
            font-size: 8px;
            color: #333;
            text-align: left;
            margin: 1rem 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            line-height: 1.2;
            background: linear-gradient(to right, #1d99ff, #0162AF);
            -webkit-background-clip: text;
            color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1); 
        }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="{{ route('home', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" data-toggle="tooltip" data-placement="right" title="Home">
            <i class="fas fa-home"></i>
        </a>
        <a href="{{ $redirectUrl }}" data-toggle="tooltip" data-placement="right" title="Back">
            <i class="fas fa-door-open"></i>
        </a>
        <a href="{{ route('info', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" data-toggle="tooltip" data-placement="right" title="info">
            <i class="fas fa-info-circle"></i>
        </a>
    </div>
    <div class="content">
        <header class="header">
            <img src="{{ asset('images/logopadma.png') }}" alt="Logo">
            <div> <span class="username">{{ $bisnis }} {{ $manufaktur }} {{ $dealer }} {{ $lokasi }} {{ $cabang }}</span></div>
            <div>
                <span class="username">Welcome : {{ $username }}</span> | <a href="{{ route('logout', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="logout" style="color: #0162AF;" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
            </div>
        </header>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="main-content container-fluid">
            @yield('content')
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
</body>
</html>
