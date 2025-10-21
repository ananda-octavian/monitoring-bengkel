@php
    $layout = null;

    if (auth()->user()->level === 'superadmin') {
        $layout = 'layouts.superadmin';
    } elseif (auth()->user()->level === 'adminpadma') {
        $layout = 'layouts.admin';
    } elseif (auth()->user()->level === 'manajemen') {
        $layout = 'layouts.manajemen';
    }
@endphp

@if ($layout)
    @extends($layout)

    @section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2 class="mb-4">Edit User</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    $bisnis = request()->query('bisnis');
                    $manufaktur = request()->query('manufaktur');
                    $dealer = request()->query('dealer');
                    $cabang = request()->query('cabang');
                    $lokasi = request()->query('lokasi');
                @endphp

                <a href="{{ route('manajemenuser') }}" class="btn btn-secondary mb-3">Back</a>

                <form action="{{ route('updateuser', ['id_user' => $user->id_user]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" value="{{ $user->username }}" required>
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
                            <option value="superadmin" {{ $user->level == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                            <option value="adminpadma" {{ $user->level == 'adminpadma' ? 'selected' : '' }}>Admin Padma</option>
                            <option value="kepalamekanik" {{ $user->level == 'kepalamekanik' ? 'selected' : '' }}>Kepala Mekanik</option>
                            <option value="manajemen" {{ $user->level == 'manajemen' ? 'selected' : '' }}>Manajemen</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_bengkel">Cabang</label>
                        <select name="id_bengkel" id="id_bengkel" class="form-control">
                            <option value="">---Pilih---</option>
                            @foreach($workshops as $workshop)
                                <option value="{{ $workshop->id_bengkel }}" {{ $user->id_bengkel == $workshop->id_bengkel ? 'selected' : '' }}>
                                    {{ $workshop->bisnis }} {{ $workshop->manufaktur }} {{ $workshop->dealer }} {{ $workshop->cabang }} {{ $workshop->lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary">Update</button>
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

@else
    <p>User level is not recognized. Please contact the administrator.</p>
@endif
