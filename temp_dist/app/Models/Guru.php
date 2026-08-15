<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'nama',
        'mapel',
        'status',
        'catatan'
    ];

    protected $casts = [
        'mapel' => 'array', // Cast mapel as JSON array
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get gurus by email
     */
    public static function findByEmail($email)
    {
        return static::where('email', $email)->first();
    }

    /**
     * Check if guru has specific mapel
     */
    public function hasMapel($mapel)
    {
        return in_array($mapel, $this->mapel ?? []);
    }

    /**
     * Get all mapel as comma-separated string
     */
    public function getMapelString()
    {
        return implode(', ', $this->mapel ?? []);
    }
}
