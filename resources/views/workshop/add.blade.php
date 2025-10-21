@extends('layouts.menu')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="mb-4">Add Workshop</h1>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <a href="{{ route('manajemenworkshop') }}" class="btn btn-secondary mb-3">Back</a>

            <form action="{{ route('addworkshop') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="bisnis">Bisnis</label>
                    <select name="bisnis" id="bisnis" class="form-control" required onchange="toggleInputField('bisnis')">
                        <option value="">---Pilih---</option>
                        @foreach($bisniss as $bisnis)
                            <option value="{{ $bisnis }}">{{ $bisnis }}</option>
                        @endforeach
                        <option value="lainnya">Lainnya...</option>
                    </select>
                    <input type="text" name="bisnis_text" id="bisnis_text" class="form-control mt-2" style="display:none;" placeholder="Masukkan bisnis baru">
                </div>

                <div class="form-group">
                    <label for="manufaktur">Manufaktur</label>
                    <select name="manufaktur" id="manufaktur" class="form-control" required onchange="toggleInputField('manufaktur')">
                        <option value="">---Pilih---</option>
                        @foreach($manufakturs as $manufaktur)
                            <option value="{{ $manufaktur }}">{{ $manufaktur }}</option>
                        @endforeach
                        <option value="lainnya">Lainnya...</option>
                    </select>
                    <input type="text" name="manufaktur_text" id="manufaktur_text" class="form-control mt-2" style="display:none;" placeholder="Masukkan manufaktur baru">
                </div>

                <div class="form-group">
                    <label for="dealer">Dealer</label>
                    <select name="dealer" id="dealer" class="form-control" required onchange="toggleInputField('dealer')">
                        <option value="">---Pilih---</option>
                        @foreach($dealers as $dealer)
                            <option value="{{ $dealer }}">{{ $dealer }}</option>
                        @endforeach
                        <option value="lainnya">Lainnya...</option>
                    </select>
                    <input type="text" name="dealer_text" id="dealer_text" class="form-control mt-2" style="display:none;" placeholder="Masukkan dealer baru">
                </div>

                <div class="form-group">
                    <label for="cabang">Cabang</label>
                    <select name="cabang" id="cabang" class="form-control" required onchange="toggleInputField('cabang')">
                        <option value="">---Pilih---</option>
                        @foreach($cabangs as $cabang)
                            <option value="{{ $cabang }}">{{ $cabang }}</option>
                        @endforeach
                        <option value="lainnya">Lainnya...</option>
                    </select>
                    <input type="text" name="cabang_text" id="cabang_text" class="form-control mt-2" style="display:none;" placeholder="Masukkan cabang baru">
                </div>

                <div class="form-group">
                    <label for="lokasi">Lokasi</label>
                    <select name="lokasi" id="lokasi" class="form-control" required onchange="toggleInputField('lokasi')">
                        <option value="">---Pilih---</option>
                        @foreach($lokasis as $lokasi)
                            <option value="{{ $lokasi }}">{{ $lokasi }}</option>
                        @endforeach
                        <option value="lainnya">Lainnya...</option>
                    </select>
                    <input type="text" name="lokasi_text" id="lokasi_text" class="form-control mt-2" style="display:none;" placeholder="Masukkan lokasi baru">
                </div>

                <div class="form-group">
                    <label for="id_bengkel">Kode Bengkel</label>
                    <input type="text" name="id_bengkel" id="id_bengkel" class="form-control" maxlength="10" minlength="10" pattern="[A-Za-z0-9]{10}" title="Kode Bengkel harus berupa 10 digit huruf/angka" required>
                    <small class="form-text text-muted">Masukkan 10 digit huruf untuk kode bengkel.</small>
                </div>
                
                <button type="submit" class="btn btn-secondary">Submit</button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleInputField(field) {
        var selectField = document.getElementById(field);
        var textField = document.getElementById(field + '_text');
        if (selectField.value === 'lainnya') {
            textField.style.display = 'block';
            textField.required = true;
        } else {
            textField.style.display = 'none';
            textField.required = false;
        }
    }
</script>
@endsection
