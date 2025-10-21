<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $table = 'workshops'; // Sesuaikan dengan nama tabel yang sesuai
    protected $primaryKey = 'id_bengkel'; // Kolom kunci utama
    public $incrementing = false; // Kunci utama tidak auto-increment
    protected $keyType = 'string'; // Tipe data kunci utama

    protected $fillable = [
        'id_bengkel',
        'bisnis',
        'manufaktur',
        'dealer',
        'cabang',
        'lokasi',
    ];

    public $timestamps = true; // Kolom timestamp otomatis

    public function wip()
    {
        return $this->hasMany(Wip::class, 'id_bengkel', 'id_bengkel');
    }

    public function spp()
    {
        return $this->hasMany(Spp::class, 'id_bengkel', 'id_bengkel');
    }

    public function detailup()
    {
        return $this->hasMany(Detailup::class, 'id_bengkel', 'id_bengkel');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'id_bengkel', 'id_bengkel');
    }
}
