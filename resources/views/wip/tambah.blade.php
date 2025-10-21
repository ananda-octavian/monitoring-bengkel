@php
    $layout = null;

    if (auth()->check()) {
        $userLevel = auth()->user()->level;
        
        if ($userLevel === 'superadmin') {
            $layout = 'layouts.superadmin';
        } elseif ($userLevel === 'adminpadma') {
            $layout = 'layouts.admin';
        } elseif (auth()->user()->level === 'kepalamekanik') {
            $layout = 'layouts.admin';
        } elseif ($userLevel === 'manajemen') {
            $layout = 'layouts.manajemen';
        }
    }
@endphp

@if ($layout)
    @extends($layout)

    @section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2 class="mb-4">Add WIP</h2>
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

                <a href="{{ route('manajemenwip', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" class="btn btn-secondary mb-3">Back</a>

                <form action="{{ route('tambahwip', ['bisnis' => $bisnis, 'manufaktur' => $manufaktur, 'dealer' => $dealer, 'cabang' => $cabang, 'lokasi' => $lokasi]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($nospp))
                    <div class="form-group">
                        <label for="nospp">No SPP</label>
                        <input type="text" name="nospp_display" class="form-control" value="{{ substr($nospp, 10) }}" disabled>
                        <input type="hidden" name="nospp" value="{{ $nospp }}">
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="proses">Proses</label>
                        <select name="proses" class="form-control" id="proses" required onchange="toggleInputField()">
                            <option value="Panel">Panel</option>
                            <option value="Putty">Putty</option>
                            <option value="Surfacer">Surfacer</option>
                            <option value="Masking">Masking</option>
                            <option value="Painting">Painting</option>
                            <option value="Polishing">Polishing</option>
                            <option value="Assembly">Assembly</option>
                            <option value="Washing">Washing</option>
                            <option value="Finishing">Finishing</option>
                            <option value="Final Check">Final Check</option>
                            <option value="Unit OK">Unit OK</option>
                            <option value="Work Paused">Work Paused</option>
                        </select>
                        <input type="text" name="keterangan" id="keterangan_text" class="form-control mt-2" style="display:none;" placeholder="Masukkan keterangan">
                    </div>
                    <div class="form-group">
                        <label for="foto">Foto</label>
                        <!-- Petunjuk kecil untuk ukuran file -->
                        <small class="form-text text-muted">Format: jpeg, png, jpg, gif, svg. Ukuran maksimum: 2MB.</small>
                        
                        <!-- Input untuk memilih foto dari file -->
                        <input type="file" name="foto[]" class="form-control-file" id="foto" accept=".jpeg,.png,.jpg,.gif,.svg" multiple onchange="loadImage(event)" style="display:block;">
                        <div id="foto-preview" class="mt-3"></div> <!-- Container for image previews -->
                    
                        <!-- Tombol untuk membuka kamera -->
                        <button type="button" id="start-camera" class="btn btn-secondary" onclick="openCamera()">Open Camera</button>
                        
                        <!-- Video dan tombol capture kamera -->
                        <video id="video" style="display:none; width: 300px; height: auto; margin-top: 10px;" autoplay></video>
                        <button type="button" id="capture" style="margin-top: 10px; display:none; background-color: #4CAF50; color: white; border: none; padding: 10px; border-radius: 8px; cursor: pointer;" onclick="captureImage()">Take Photo</button>
                    
                        <!-- Menyimpan gambar dari kamera -->
                        <input type="hidden" name="camera_image" id="camera_image">
                        <img id="imagePreview" style="display:none; width: 100px; height: auto; margin-top: 10px;">
                    </div>
                        <!-- Tombol untuk menukar kamera -->
                        <button type="button" id="swap-camera" class="btn btn-secondary" style="display:none;" onclick="swapCamera()">Swap Camera</button>                
                        <!-- Tombol cancel -->
                        <button type="button" id="cancel-photo" class="btn btn-warning" style="display:none;" onclick="cancelPhoto()">Cancel Photo</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
    @endsection
@else
    <p>User level is not recognized. Please contact the administrator.</p>
@endif

<script>
    function toggleInputField() {
        const selectField = document.getElementById('proses');
        const textField = document.getElementById('keterangan_text');
        if (selectField.value === 'Work Paused') {
            textField.style.display = 'block';
            textField.required = true;
        } else {
            textField.style.display = 'none';
            textField.required = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
    const fotoInput = document.getElementById('foto');
    const cameraButton = document.getElementById('start-camera');
    const previewContainer = document.getElementById('foto-preview');

    // Saat memilih foto dari file, sembunyikan tombol "Buka Kamera"
    fotoInput.addEventListener('change', function () {
        if (fotoInput.files.length > 0) {
            cameraButton.style.display = 'none'; // Sembunyikan tombol "Buka Kamera"
        }
    });
});

function loadImage(event) {
    const input = event.target;
    const previewContainer = document.getElementById('foto-preview');
    previewContainer.innerHTML = ''; // Bersihkan pratinjau sebelumnya

    for (const file of input.files) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.src = e.target.result;
                
                img.onload = () => {
                    compressImage(img, 2048 * 1024, function(compressedDataUrl) {
                        const previewImage = document.createElement('img');
                        previewImage.src = compressedDataUrl;
                        previewImage.style.width = '100px';
                        previewImage.style.height = 'auto';
                        previewContainer.appendChild(previewImage);
                    });
                };
            };
            reader.readAsDataURL(file);
        }
    }

    // Show cancel button
    document.getElementById('cancel-photo').style.display = 'inline-block';
}

function compressImage(img, maxFileSize, callback) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    let quality = 0.9; // Mulai dengan kualitas 90%
    let compressedDataUrl;

    // Set canvas ukuran gambar sesuai ukuran asli
    canvas.width = img.width;
    canvas.height = img.height;
    ctx.drawImage(img, 0, 0);

    function compress() {
        compressedDataUrl = canvas.toDataURL('image/jpeg', quality);
        const fileSize = dataURLToFileSize(compressedDataUrl);

        if (fileSize > maxFileSize && quality > 0.1) {
            quality -= 0.1; // Turunkan kualitas dan ulangi
            compress();
        } else {
            callback(compressedDataUrl); // Kembalikan hasil jika sudah di bawah ukuran yang diinginkan
        }
    }

    compress();
}

function dataURLToFileSize(dataURL) {
    const base64String = dataURL.split(',')[1]; // Ambil bagian base64 dari data URL
    const fileSize = (base64String.length * (3 / 4)) - (base64String.endsWith('==') ? 2 : (base64String.endsWith('=') ? 1 : 0));
    return fileSize; // Ukuran file dalam byte
}

let currentStream = null;
let currentFacingMode = 'environment'; // Default ke kamera belakang
let backCameraAvailable = false;
let frontCameraAvailable = false;

function openCamera() {
    const video = document.getElementById('video');
    const captureButton = document.getElementById('capture');
    const startCameraButton = document.getElementById('start-camera');
    const swapCameraButton = document.getElementById('swap-camera');

    navigator.mediaDevices.enumerateDevices()
        .then(devices => {
            backCameraAvailable = devices.some(device => device.kind === 'videoinput' && device.label.toLowerCase().includes('back'));
            frontCameraAvailable = devices.some(device => device.kind === 'videoinput' && device.label.toLowerCase().includes('front'));

            if (!backCameraAvailable && frontCameraAvailable) {
                currentFacingMode = 'user'; // Kamera depan jika kamera belakang tidak tersedia
            }

            return navigator.mediaDevices.getUserMedia({ video: { facingMode: currentFacingMode } });
        })
        .then(stream => {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop()); // Stop previous stream
            }

            video.srcObject = stream;
            currentStream = stream;
            video.style.display = 'block';
            startCameraButton.style.display = 'none';
            captureButton.style.display = 'block';
            swapCameraButton.style.display = backCameraAvailable || frontCameraAvailable ? 'inline-block' : 'none'; // Tampilkan tombol swap jika ada kamera

            // Hapus input file dan sembunyikan pratinjau
            document.getElementById('foto').value = '';
            document.getElementById('foto-preview').innerHTML = '';
        })
        .catch(err => {
            console.error('Error accessing camera:', err);
        });

    // Tampilkan tombol batal
    document.getElementById('cancel-photo').style.display = 'inline-block';
}

function swapCamera() {
    if (!backCameraAvailable || !frontCameraAvailable) return; // Tidak ada kamera untuk ditukar

    currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
    openCamera(); // Buka kamera dengan mode yang baru
}

function captureImage() {
    const video = document.getElementById('video');
    const imagePreview = document.getElementById('imagePreview');
    const cameraImage = document.getElementById('camera_image');
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    compressImage(canvas, 2048 * 1024, function(compressedDataUrl) {
        imagePreview.src = compressedDataUrl;
        imagePreview.style.display = 'block';
        cameraImage.value = compressedDataUrl;
    });

    // Stop the video stream
    if (currentStream) {
        const tracks = currentStream.getTracks();
        tracks.forEach(track => track.stop());
    }
    const videoElement = document.getElementById('video');
    videoElement.style.display = 'none';
    document.getElementById('start-camera').style.display = 'block';
    document.getElementById('capture').style.display = 'none';
}

function cancelPhoto() {
    // Clear file input and preview
    const fileInput = document.getElementById('foto');
    fileInput.value = '';
    document.getElementById('foto-preview').innerHTML = '';

    // Stop camera stream and hide video
    if (currentStream) {
        const tracks = currentStream.getTracks();
        tracks.forEach(track => track.stop());
    }
    document.getElementById('video').style.display = 'none';

    // Hide image preview and reset hidden inputs
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('camera_image').value = '';

    // Reset buttons
    document.getElementById('start-camera').style.display = 'block';
    document.getElementById('capture').style.display = 'none';
    document.getElementById('swap-camera').style.display = 'none';
    document.getElementById('cancel-photo').style.display = 'none';
}


</script>

