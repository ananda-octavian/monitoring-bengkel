<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wip extends Model
{
    use HasFactory;

    // Specify the primary key column
    protected $primaryKey = 'id_wips';

    // Disable auto-incrementing if the primary key is not an incrementing integer
    public $incrementing = false;

    // Set the type of the primary key
    protected $keyType = 'string'; // Adjust if `id_wips` is not a string

    // Define which attributes are mass assignable
    protected $fillable = ['id_wips', 'nospp', 'proses', 'keterangan', 'foto', 'id_bengkel', 'stopped_at'];

    // Enable timestamps
    public $timestamps = true;

    // Define the relationship with Spp
    public function spp()
    {
        return $this->belongsTo(Spp::class, 'nospp', 'nospp');
    }

    // Define the relationship with Workshop
    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'id_bengkel', 'id_bengkel');
    }
}
