<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padma Business Monitoring</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            overflow: hidden;
        }
        /* Header */
        .header {
            background-color: whitesmoke;
            padding: 10px 20px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        /* Logo kiri */
        .logo-left img {
            height: 50px;
            width: auto;
        }
        /* Logo kanan */
        .logo-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo-right img {
            height: 45px;
            width: auto;
        }
        /* Slideshow */
        .slideshow-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: -1;
            overflow: hidden;
        }
        .slideshow-container img {
            width: 100%;
            height: 100vh;
            object-fit: cover;
            position: absolute;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }
        .slideshow-container img.active {
            opacity: 1;
        }
        /* Container Tengah */
        .center-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 10;
        }
        /* Teks Judul */
        .title-text {
            font-size: 28px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
        }
        /* Tombol Login */
        .btn-login {
            font-size: 20px;
            font-weight: bold;
            padding: 12px 30px;
            background-color: #007bff;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s;
            display: inline-block;
        }
        .btn-login:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <!-- Logo Kiri -->
        <div class="logo-left">
            <img src="{{ asset('images/logopadma.png') }}" alt="PT Padma Indah Prima Perkasa">
        </div>

        <!-- Logo Kanan -->
        <div class="logo-right">
            <img src="{{ asset('images/Logo Kemenperin.png') }}" alt="Logo Kemenperin">
            <img src="{{ asset('images/Logo STMI.png') }}" alt="Logo STMI">
        </div>
    </header>

    <!-- Slideshow -->
    <div class="slideshow-container">
        <img src="{{ asset('images/slideshow/foto1.jpg') }}" class="slideshow-image active" alt="Slideshow Image 1">
        <img src="{{ asset('images/slideshow/foto2.jpg') }}" class="slideshow-image" alt="Slideshow Image 2">
        <img src="{{ asset('images/slideshow/foto3.jpg') }}" class="slideshow-image" alt="Slideshow Image 3">
        <img src="{{ asset('images/slideshow/foto4.jpg') }}" class="slideshow-image" alt="Slideshow Image 4">
        <img src="{{ asset('images/slideshow/foto5.jpg') }}" class="slideshow-image" alt="Slideshow Image 5">
    </div>

    <!-- Teks & Tombol -->
    <div class="center-content">
        <div class="title-text">PADMA BUSINESS MONITORING</div>
        <a href="{{ route('login') }}" class="btn btn-login">Login</a>
    </div>

    <!-- Script Slideshow -->
    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slideshow-container img');

        function showNextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }

        setInterval(showNextSlide, 5000); // Ganti slide setiap 5 detik
    </script>

</body>
</html>
