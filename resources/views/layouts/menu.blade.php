@php
    $username = session('username', '');
    $manufaktur = request()->query('manufaktur');
    $dealer = request()->query('dealer');
    $cabang = request()->query('cabang');
    $lokasi = request()->query('lokasi');
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
            overflow: hidden;
            position: relative;
        }
        .content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.8); /* Slight white overlay for better readability */
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
            padding: 20px;
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
            background: linear-gradient(to right, grey, black);
            -webkit-background-clip: text;
            color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1); 
        }

        /* Slideshow CSS */
        .slideshow-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        .slideshow-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }
        .slideshow-image.active {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="slideshow-container">
        <img src="{{ asset('images/slideshow/foto1.jpg') }}" class="slideshow-image active" alt="Slideshow Image 1">
        <img src="{{ asset('images/slideshow/foto2.jpg') }}" class="slideshow-image" alt="Slideshow Image 2">
        <img src="{{ asset('images/slideshow/foto3.jpg') }}" class="slideshow-image" alt="Slideshow Image 3">
        <img src="{{ asset('images/slideshow/foto4.jpg') }}" class="slideshow-image" alt="Slideshow Image 4">
        <img src="{{ asset('images/slideshow/foto5.jpg') }}" class="slideshow-image" alt="Slideshow Image 5">
        <!-- Add more images as needed -->
    </div>

    <div class="content">
        <header class="header">
            <img src="{{ asset('images/logopadma.png') }}" alt="Logo">
            <div>
                <span class="username">Welcome : {{ $username }}</span>
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
        $(document).ready(function() {
            let currentIndex = 0;
            const images = $('.slideshow-image');
            const imageCount = images.length;

            setInterval(function() {
                images.eq(currentIndex).removeClass('active');
                currentIndex = (currentIndex + 1) % imageCount;
                images.eq(currentIndex).addClass('active');
            }, 5000); // Change image every 5 seconds
        });
    </script>
</body>
</html>
