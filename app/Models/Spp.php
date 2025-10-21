<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    use HasFactory;

    protected $table = 'spps'; // Sesuaikan dengan nama tabel yang sesuai
    protected $primaryKey = 'nospp'; // Kolom kunci utama
    public $incrementing = false; // Kunci utama tidak auto-increment
    protected $keyType = 'string'; // Tipe data kunci utama

    protected $fillable = [
        'nopol',
        'nospp',
        'sa',
        'type',
        'warna',
        'damage',
        'tglmasuk',
        'estimasi',
        'asuransi',
        'diterima',
        'grandtotal',
        'id_bengkel',
    ];

    public $timestamps = true; // Kolom timestamp otomatis

    public function wip()
    {
        return $this->hasMany(Wip::class, 'nospp', 'nospp');
    }
    
    public function detailups()
    {
        return $this->hasMany(Detailup::class, 'nospp', 'nospp');
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'id_bengkel', 'id_bengkel');
    }
}
