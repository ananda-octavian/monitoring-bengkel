@extends('layouts.menu')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2 class="mb-4">Add User</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <a href="{{ route('manajemenuser') }}" class="btn btn-secondary mb-3">Back</a>

            <form action="{{ route('adduser') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" required>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="level">Level</label>
                        <select name="level" id="level" class="form-control" required>
                            <option value="">---Pilih---</option>
                            <option value="superadmin">Superadmin</option>
                            <option value="adminpadma">Admin Padma</option>
                            <option value="kepalamekanik">Kepala Mekanik</option>
                            <option value="manajemen">Manajemen</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_bengkel">Cabang</label>
                        <select name="id_bengkel" id="id_bengkel" class="form-control">
                            <option value="">---Pilih---</option>
                            @foreach($workshops as $workshop)
                                <option value="{{ $workshop->id_bengkel }}">
                                    {{ $workshop->bisnis }} {{ $workshop->manufaktur }} {{ $workshop->dealer }} {{ $workshop->cabang }} {{ $workshop->lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary">Submit</button>
                </form>
                
                <!-- Font Awesome untuk ikon mata -->
                <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
                
                <script>
                    const togglePassword = document.querySelector('#togglePassword');
                    const passwordField = document.querySelector('#password');
                
                    togglePassword.addEventListener('click', function (e) {
                        // Toggle tipe input antara password dan text
                        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordField.setAttribute('type', type);
                
                        // Ubah ikon mata (fa-eye ke fa-eye-slash)
                        this.classList.toggle('fa-eye-slash');
                    });
                </script>
                
            </div>
        </div>
    </div>
    @endsection