@extends('layouts.menu')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Edit Workshop</h2>
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

            <form action="{{ route('updateworkshop',  ['id_bengkel' => $workshop->id_bengkel]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="bisnis">Bisnis</label>
                    <select name="bisnis" id="bisnis" class="form-control" required onchange="toggleInputField('bisnis')">
                        <option value="">---Pilih---</option>
                        @foreach($bisniss as $item)
                            <option value="{{ $item }}" {{ $item == old('bisnis', $workshop->bisnis) ? 'selected' : '' }}>
                                {{ $item }}
                            </option>
                        @endforeach
                        <option value="lainnya">Lainnya...</option>
                    </select>
                    <input type="text" name="bisnis_text" id="bisnis_text" class="form-control mt-2" style="display:none;" placeholder="Masukkan bisnis baru" value="{{ old('bisnis_text') }}">
                </div>

                <div class="form-group">
                    <label for="manufaktur">Manufaktur</label>
                    <select name="manufaktur" id="manufaktur" class="form-control" required onchange="toggleInputField('manufaktur')">
                        <option value="">---Pilih---</option>
                        @foreach($manufakturs as $item)
                            <option value="{{ $item }}" {{ $item == old('manufaktur', $workshop->manufaktur) ? 'selected' : '' }}>
                                {{ $item }}
                            </option>
                        @endforeach
                        <option value="lainnya">Lainnya...</option>
                    </select>
                    <input type="text" name="manufaktur_text" id="manufaktur_text" class="form-control mt-2" style="display:none;" placeholder="Masukkan manufaktur baru" value="{{ old('manufaktur_text') }}">
                </div>

                <div class="form-group">
                    <label for="dealer">Dealer</label>
                    <select name="dealer" id="dealer" class="form-control" required onchange="toggleInputField('dealer')">
                        <option value="">---Pilih---</option>
                        @foreach($dealers as $item)
                            <option value="{{ $item }}" {{ $item == old('dealer', $workshop->dealer) ? 'selected' : '' }}>
                                {{ $item }}
                            </option>
                        @endforeach
                        <option value="lainnya">Lainnya...</option>
                    </select>
                    <input type="text" name="dealer_text" id="dealer_text" class="form-control mt-2" style="display:none;" placeholder="Masukkan dealer baru" value="{{ old('dealer_text') }}">
                </div>

                <div class="form-group">
                    <label for="cabang">Cabang</label>
                    <select name="cabang" id="cabang" class="form-control" required onchange="toggleInputField('cabang')">
                        <option value="">---Pilih---</option>
                        @foreach($cabangs as $item)
                            <option value="{{ $item }}" {{ $item == old('cabang', $workshop->cabang) ? 'selected' : '' }}>
                                {{ $item }}
                            </option>
                        @endforeach
                        <option value="lainnya">Lainnya...</option>
                    </select>
                    <input type="text" name="cabang_text" id="cabang_text" class="form-control mt-2" style="display:none;" placeholder="Masukkan cabang baru" value="{{ old('cabang_text') }}">
                </div>

                <div class="form-group">
                    <label for="lokasi">Lokasi</label>
                    <select name="lokasi" id="lokasi" class="form-control" required onchange="toggleInputField('lokasi')">
                        <option value="">---Pilih---</option>
                        @foreach($lokasis as $item)
                            <option value="{{ $item }}" {{ $item == old('lokasi', $workshop->lokasi) ? 'selected' : '' }}>
                                {{ $item }}
                            </option>
                        @endforeach
                        <option value="lainnya">Lainnya...</option>
                    </select>
                    <input type="text" name="lokasi_text" id="lokasi_text" class="form-control mt-2" style="display:none;" placeholder="Masukkan lokasi baru" value="{{ old('lokasi_text') }}">
                </div>

                <div class="form-group">
                    <label for="id_bengkel">Kode Bengkel</label>
                    <input type="text" name="id_bengkel" id="id_bengkel" class="form-control" maxlength="10" value="{{ old('id_bengkel', $workshop->id_bengkel) }}" minlength="10" pattern="[A-Za-z0-9]{10}" title="Kode Bengkel harus berupa 10 digit huruf/angka" required>
                    <small class="form-text text-muted">Masukkan 10 digit huruf untuk kode bengkel.</small>
                </div>
                <button type="submit" class="btn btn-secondary">Update</button>
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
