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
            <h2 class="mb-4">Edit SPP</h2>
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

            <form action="{{ route('updatespp', ['nospp' => $spp->nospp, 'bisnis' => request('bisnis'), 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nopol">Nopol</label>
                    <input type="text" name="nopol" id="nopol" class="form-control" value="{{ $spp->nopol }}" required>
                </div>
                <div class="form-group">
                    <label for="sa">SA</label>
                    <input type="text" name="sa" id="sa" class="form-control" value="{{ $spp->sa }}" required>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <input type="text" name="type" id="type" class="form-control" value="{{ $spp->type }}" required>
                </div>
                <div class="form-group">
                    <label for="warna">Warna</label>
                    <input type="text" name="warna" id="warna" class="form-control" value="{{ $spp->warna }}">
                </div>
                <div class="form-group">
                    <label for="damage">Damage</label>
                    <select name="damage" id="damage" class="form-control" required>
                        <option value="Light" {{ $spp->damage == 'Light' ? 'selected' : '' }}>Light</option>
                        <option value="Medium" {{ $spp->damage == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="Heavy" {{ $spp->damage == 'Heavy' ? 'selected' : '' }}>Heavy</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tglmasuk">Tanggal Masuk</label>
                    <input type="date" name="tglmasuk" id="tglmasuk" class="form-control" value="{{ $spp->tglmasuk }}" required>
                </div>
                <div class="form-group">
                    <label for="estimasi">Estimasi</label>
                    <input type="date" name="estimasi" id="estimasi" class="form-control" value="{{ $spp->estimasi }}" required>
                </div>
                <div class="form-group">
                    <label for="diterima">Diterima</label>
                    <input type="date" name="diterima" id="diterima" class="form-control" value="{{ $spp->diterima }}" >
                </div>
                <div class="form-group">
                    <label for="asuransi">Asuransi</label>
                    <input type="text" name="asuransi" id="asuransi" class="form-control" value="{{ $spp->asuransi }}" required>
                </div>
                <button type="submit" class="btn btn-secondary">Update</button>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection
@else
    <p>User level is not recognized. Please contact the administrator.</p>
@endif