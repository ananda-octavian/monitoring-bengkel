<?php

namespace App\Exports;

use App\Models\Spp;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SppExport implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $bisnis;
    protected $manufaktur;
    protected $dealer;
    protected $cabang;
    protected $lokasi;
    protected $displayedNospp = [];
    protected $index = 0;

    public function __construct($startDate, $endDate, $bisnis, $manufaktur, $dealer, $cabang, $lokasi)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->bisnis = $bisnis;
        $this->manufaktur = $manufaktur;
        $this->dealer = $dealer;
        $this->cabang = $cabang;
        $this->lokasi = $lokasi;
    }

    public function collection()
    {
        return Spp::select(
                'spps.nospp', 'spps.nopol', 'spps.sa', 'spps.asuransi', 'spps.type',
                'spps.warna', 'spps.damage', 'spps.tglmasuk', 'spps.estimasi',
                'spps.grandtotal', 'detailups.namauraian', 'detailups.hargauraian',
                DB::raw("(SELECT proses FROM wips WHERE wips.nospp = spps.nospp ORDER BY wips.updated_at DESC LIMIT 1) as proses")
            )
            ->join('workshops', 'spps.id_bengkel', '=', 'workshops.id_bengkel')
            ->leftJoin('detailups', 'detailups.nospp', '=', 'spps.nospp')
            ->whereBetween('spps.tglmasuk', [$this->startDate, $this->endDate])
            ->when($this->bisnis, fn($query) => $query->where('workshops.bisnis', $this->bisnis))
            ->when($this->manufaktur, fn($query) => $query->where('workshops.manufaktur', $this->manufaktur))
            ->when($this->dealer, fn($query) => $query->where('workshops.dealer', $this->dealer))
            ->when($this->cabang, fn($query) => $query->where('workshops.cabang', $this->cabang))
            ->when($this->lokasi, fn($query) => $query->where('workshops.lokasi', $this->lokasi))
            ->get();
    }

    public function headings(): array
    {
        return [
            ['PT PADMA INDAH PRIMA PERKASA'],
            [],
            ['UNIT ENTRY', "$this->dealer $this->lokasi", "$this->startDate - $this->endDate", 'RASIO', '######'],
            [
                'NO', 'NOPOL', 'TYPE', 'WARNA',
                'WORK ORDER', '', '',
                'CUSTOMER',
                'TANGGAL PRODUKSI', '', '',
                'URAIAN PEKERJAAN',
                'RASIO PANEL', '',
                'NILAI', '', '',
                'KETERANGAN'
            ],
            [
                '', '', '', '',
                'NOMOR', 'KATEGORI KERUSAKAN', 'TGL',
                '',
                'DITERIMA', 'JANJI', 'SELESAI',
                '',
                'MANUAL', 'AUTO',
                'PRICE LIST PER PANEL (Rp)', 'PRICE LIST PER UNIT (Rp)', 'PANEL FI',
                ''
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Judul utama
        $sheet->mergeCells('A1:R2');
        $sheet->getStyle('A1:R2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'name' => 'Times New Roman'],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // UNIT ENTRY dengan informasi Dealer dan Lokasi
        $sheet->mergeCells('A3:C3');
        $sheet->mergeCells('D3:L3');
        $sheet->mergeCells('M3:O3');
        $sheet->mergeCells('P3:Q3');
        $sheet->mergeCells('R3:R3');

        $sheet->setCellValue('D3', "$this->dealer $this->lokasi");
        $sheet->setCellValue('M3', "PERIODE $this->startDate - $this->endDate");
        $sheet->setCellValue('P3', 'RASIO');
        $sheet->setCellValue('R3', '######');

        $sheet->getStyle('A3:R3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFFF00']],
        ]);

        $sheet->getStyle('P3:R3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FF0000']],
        ]);

        // Header tabel
        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:C5');
        $sheet->mergeCells('D4:D5');
        $sheet->mergeCells('E4:G4');
        $sheet->mergeCells('H4:H5');
        $sheet->mergeCells('I4:K4');
        $sheet->mergeCells('L4:L5');
        $sheet->mergeCells('M4:N4');
        $sheet->mergeCells('O4:Q4');
        $sheet->mergeCells('R4:R5');

        $sheet->getStyle('A4:R5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => '00FFFF']],
        ]);

        // Border
        $sheet->getStyle('A3:R5')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
        ]);
    }

    public function map($spp): array
    {
        $firstOccurrence = !isset($this->displayedNospp[$spp->nospp]);
        
        if ($firstOccurrence) {
            $this->displayedNospp[$spp->nospp] = true;
            $this->index++;
        }

        return [
            $firstOccurrence ? $this->index : '',
            $firstOccurrence ? $spp->nopol : '',
            $firstOccurrence ? $spp->type : '',
            $firstOccurrence ? $spp->warna : '',
            $firstOccurrence ? substr($spp->nospp, 10) : '',
            $firstOccurrence ? $spp->damage : '',
            $firstOccurrence ? $spp->tglmasuk : '',
            $firstOccurrence ? $spp->asuransi : '',
            $firstOccurrence ? $spp->tglmasuk : '',
            $firstOccurrence ? $spp->estimasi : '',
            $firstOccurrence ? $spp->sa : '',
            $spp->namauraian,
            '', '',
            $spp->hargauraian,
            $firstOccurrence ? $spp->grandtotal : '',
            '',
            $firstOccurrence ? $spp->proses : ''
        ];
    }
}
