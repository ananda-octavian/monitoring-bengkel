<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Spp;
use App\Models\Wip;
use App\Models\Detailup;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SppImport implements ToModel
{
    protected $id_bengkel;
    protected $previousNospp = null; // Untuk menyimpan nospp sebelumnya
    protected $startRow = 6; // Mulai dari baris 6 (Excel 1-based index)

    public function __construct($id_bengkel)
    {
        $this->id_bengkel = $id_bengkel;
    }

    public function model(array $row)
    {
        static $nosppMemory = null; // Menyimpan nospp terakhir
        static $rowCounter = 0; // Menghitung jumlah baris

        $rowCounter++;
        if ($rowCounter < $this->startRow) {
            return null; // Lewati baris sebelum startRow
        }

        // Ambil nospp, jika kosong gunakan nospp sebelumnya
        if (!empty($row[4])) {
            $nosppMemory = $this->id_bengkel . $row[4]; // Gabungkan dengan id_bengkel
        }

        $nospp = $nosppMemory;

        // Jika ini adalah baris baru dengan nospp baru, buat entry SPP
        if (!empty($row[4])) {
            Spp::updateOrCreate(
                ['nospp' => $nospp, 'id_bengkel' => $this->id_bengkel],
                [
                    'nopol' => $row[1], // Nopol
                    'type' => $row[2], // Type
                    'warna' => $row[3], // Warna
                    'damage' => $row[5], // Damage
                    'tglmasuk' => $this->convertExcelDate($row[6]), // Tglmasuk
                    'asuransi' => $row[7], // Asuransi
                    'estimasi' => $this->convertExcelDate($row[9]), // Estimasi
                    'sa' => $row[10], // SA
                ]
            );
        }

        // Simpan detailup jika ada namauraian dan hargauraian
        if (!empty($row[11]) && !empty($row[14])) {
            $this->insertDetailup($nospp, $row[11], $row[14]);
        }

        // Update grandtotal setelah semua detailup ditambahkan
        $this->updateGrandtotal($nospp);

        // Tambahkan data ke tabel WIP jika belum ada
        $this->insertWip($nospp);

        return null;
    }

    private function insertDetailup($nospp, $namauraian, $hargauraian)
    {
        // Ambil id_uraian terakhir dan buat yang baru
        $lastDetailup = Detailup::where('id_bengkel', $this->id_bengkel)
            ->orderBy('id_uraian', 'desc')
            ->first();

        $lastIdUraian = $lastDetailup ? (int)substr($lastDetailup->id_uraian, strlen($this->id_bengkel)) : 0;
        $newIdUraian = $this->id_bengkel . str_pad($lastIdUraian + 1, 6, '0', STR_PAD_LEFT);

        Detailup::create([
            'id_uraian' => $newIdUraian,
            'nospp' => $nospp,
            'namauraian' => $namauraian,
            'hargauraian' => $hargauraian,
            'id_bengkel' => $this->id_bengkel,
        ]);
    }

    private function updateGrandtotal($nospp)
    {
        $grandtotal = Detailup::where('nospp', $nospp)
            ->where('id_bengkel', $this->id_bengkel)
            ->sum('hargauraian');

        Spp::where('nospp', $nospp)
            ->where('id_bengkel', $this->id_bengkel)
            ->update(['grandtotal' => $grandtotal]);
    }

    private function insertWip($nospp)
    {
        $lastWip = Wip::where('id_wips', 'LIKE', $this->id_bengkel . '%')
            ->orderBy('id_wips', 'desc')
            ->first();
        $lastIdWips = $lastWip ? intval(substr($lastWip->id_wips, strlen($this->id_bengkel))) : 0;
        $newIdWips = $this->id_bengkel . str_pad($lastIdWips + 1, 6, '0', STR_PAD_LEFT);

        // Cek apakah data WIP sudah ada
        if (!Wip::where('nospp', $nospp)->exists()) {
            Wip::create([
                'id_wips' => $newIdWips,
                'nospp' => $nospp,
                'proses' => 'Job Dispatch',
                'id_bengkel' => $this->id_bengkel,
            ]);
        }
    }

    public function convertExcelDate($excelDate)
    {
        if (is_numeric($excelDate)) {
            $timestamp = Date::excelToTimestamp($excelDate);
            return Carbon::createFromTimestamp($timestamp)->format('Y-m-d');
        }

        try {
            return Carbon::createFromFormat('d-M-y', $excelDate)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
