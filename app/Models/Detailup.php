<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailup extends Model
{
    use HasFactory;

    protected $table = 'detailups'; // Sesuaikan dengan nama tabel yang sesuai
    protected $primaryKey = 'id_uraian'; // Kolom kunci utama
    public $incrementing = false; // Kunci utama tidak auto-increment
    protected $keyType = 'string'; // Tipe data kunci utama

    protected $fillable = ['id_uraian','nospp', 'namauraian', 'hargauraian','id_bengkel'];

    public $timestamps = true;

    public function spp()
    {
        return $this->belongsTo(Spp::class, 'nospp', 'nospp');
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'id_bengkel', 'id_bengkel');
    }
}
