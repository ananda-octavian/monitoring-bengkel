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
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Add SPP</h2>
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

            <a href="{{ route('manajemenspp', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-secondary mb-3">Back</a>

            <form action="{{ route('tambahspp', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nopol">Nopol</label>
                    <input type="text" name="nopol" id="nopol" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="nospp">No SPP</label>
                    <input type="text" name="nospp" id="nospp" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="sa">SA</label>
                    <input type="text" name="sa" id="sa" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <input type="text" name="type" id="type" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="warna">Warna</label>
                    <input type="text" name="warna" id="warna" class="form-control">
                </div>
                <div class="form-group">
                    <label for="damage">Damage</label>
                    <select name="damage" id="damage" class="form-control" required>
                        <option value="Light">Light</option>
                        <option value="Medium">Medium</option>
                        <option value="Heavy">Heavy</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tglmasuk">Tanggal Masuk</label>
                    <input type="date" name="tglmasuk" id="tglmasuk" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="estimasi">Estimasi</label>
                    <input type="date" name="estimasi" id="estimasi" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="asuransi">Asuransi</label>
                    <input type="text" name="asuransi" id="asuransi" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-secondary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection
@else
    <p>User level is not recognized. Please contact the administrator.</p>
@endif