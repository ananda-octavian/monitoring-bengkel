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
                <h2 class="mb-4">Add Uraian</h2>
                @php
                $bisnis = request()->query('bisnis');
                $manufaktur = request()->query('manufaktur');
                $dealer = request()->query('dealer');
                $cabang = request()->query('cabang');
                $lokasi = request()->query('lokasi');
            @endphp
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <a href="{{ route('manajemenspp', request()->only(['bisnis', 'manufaktur', 'dealer', 'cabang', 'lokasi'])) }}" class="btn btn-secondary mb-3">Back</a>
                <form action="{{ route('tambahdetailup', request()->only(['bisnis', 'manufaktur', 'dealer', 'cabang', 'lokasi'])) }}" method="POST">
                    @csrf
                    <input type="hidden" name="nospp" value="{{ $sppData->nospp }}">

                    <div class="table-container">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Nama Uraian</th>
                                    <th>Harga Uraian</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="uraian-table-body">
                                <tr>
                                    <td>
                                        <input type="text" name="namauraian[]" class="form-control" id="nama-uraian" oninput="searchSuggestions(this)" required>
                                        <!-- Container untuk menampilkan saran -->
                                        <div id="suggestions" style="border: 1px solid #ccc; max-height: 100px; overflow-y: auto; display:none;"></div>
                                        <span id="error-message" style="color:red; display:none;">Input harus sesuai dengan saran!</span>
                                    </td>
                                    <td><input type="number" name="hargauraian[]" class="form-control" required></td>
                                    <td>
                                        <button type="submit" class="btn btn-success" onclick="validateInput(event)">Submit</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
                
                <h3 class="mt-4">Data Uraian</h3>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Nama Uraian</th>
                            <th>Harga Uraian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailupData as $detailup)
                            <tr>
                                <td>{{ $detailup->namauraian }}</td>
                                <td>{{ number_format($detailup->hargauraian, 0, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('hapusdetailup', ['id_uraian' => $detailup->id_uraian, 'bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this Uraian?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" align="right"><b>Grand Total:</b></td>
                            <td>{{ number_format($grandTotal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <script>
        // Daftar saran untuk autocomplete
        const uraianSuggestions = [
            "Front Bumper",
            "Rear Bumper",
            "Left Mirror",
            "Right Mirror",
            "Hood",
            "Left Front Door",
            "Left Rear Door",
            "Right Front Door",
            "Right Rear Door",
            "Trunk",
            "Roof",
            "Left Fender",
            "Right Fender",
            "Left Side Skirt",
            "Right Side Skirt"
        ];
    
        // Fungsi untuk mencari saran dan menampilkannya
        function searchSuggestions(inputElement) {
            const input = inputElement.value.toLowerCase();
            const suggestionsContainer = document.getElementById('suggestions');
            const errorMessage = document.getElementById('error-message');
    
            // Reset pesan error
            errorMessage.style.display = 'none';
    
            // Filter daftar saran berdasarkan input user
            const filteredSuggestions = uraianSuggestions.filter(uraian => uraian.toLowerCase().includes(input));
    
            // Hapus saran yang sebelumnya
            suggestionsContainer.innerHTML = '';
    
            // Tampilkan saran yang sesuai
            if (filteredSuggestions.length > 0 && input !== '') {
                filteredSuggestions.forEach(suggestion => {
                    const suggestionItem = document.createElement('div');
                    suggestionItem.textContent = suggestion;
                    suggestionItem.style.padding = '5px';
                    suggestionItem.style.cursor = 'pointer';
    
                    // Saat saran dipilih, isi kolom input dengan saran tersebut
                    suggestionItem.addEventListener('click', () => {
                        inputElement.value = suggestion;
                        suggestionsContainer.style.display = 'none'; // Sembunyikan saran setelah dipilih
                    });
    
                    suggestionsContainer.appendChild(suggestionItem);
                });
    
                suggestionsContainer.style.display = 'block'; // Tampilkan saran
            } else {
                suggestionsContainer.style.display = 'none'; // Sembunyikan jika tidak ada saran
            }
        }
    
        // Fungsi untuk validasi input
        function validateInput(event) {
            const inputElement = document.getElementById('nama-uraian');
            const errorMessage = document.getElementById('error-message');
    
            // Jika input tidak ada di dalam daftar saran, tampilkan pesan error dan cegah submit
            if (!uraianSuggestions.includes(inputElement.value)) {
                errorMessage.style.display = 'block';
                event.preventDefault(); // Cegah submit form
            } else {
                errorMessage.style.display = 'none';
            }
        }
    
        // Event listener untuk menutup saran jika klik di luar
        document.addEventListener('click', (event) => {
            const suggestionsContainer = document.getElementById('suggestions');
            if (!event.target.closest('#nama-uraian')) {
                suggestionsContainer.style.display = 'none';
            }
        });
    </script>
    @endsection
@else
    <p>User level is not recognized. Please contact the administrator.</p>
@endif
