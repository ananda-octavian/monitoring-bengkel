<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_user';
    
    protected $fillable = [
        'username',
        'password',
        'level',
        'id_bengkel', // Tambahkan kolom id_bengkel
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi dengan model Workshop
    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'id_bengkel', 'id_bengkel');
    }
}
