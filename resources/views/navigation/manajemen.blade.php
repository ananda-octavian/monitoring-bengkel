@extends('layouts.menu')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bisnis</title>
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
            margin-top: 50px; /* Adjust margin-top based on your header height */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-container h1 {
            margin: 0;
            font-size: 1.25rem;
            line-height: 1; /* Ensure the height of h1 matches the font size */
        }
        .header-container .btn-group {
            display: flex;
            gap: 10px;
        }
        .back-button {
            margin: 10px 0;
        }

        /* Center branch buttons on mobile screens */
        @media (max-width: 767.98px) {
            .container-fluid {
                top: 100px;
            padding: 0;
            margin: 0;
            width: 100%;
                align-items: center; /* Center items horizontally */
            }

            .branch-button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            font-size: 1.2rem;
            text-align: center;
            background-color: #0162AF;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row header-container mb-4">
            <div class="col d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h1 class="mb-3 mb-md-0">BUSINESS REGISTRATION</h1>
                <form action="{{ route('bisnis') }}" method="GET">
                    <button type="submit" class="btn btn-secondary back-button">Back</button>
                </form>
            </div>
        </div>
            <a href="{{ route('manajemenworkshop') }}" class="branch-button">WORKSHOP</a>
            <a href="{{ route('manajemenuser') }}" class="branch-button">USER</a>
    </div>
</body>
</html>
@endsection