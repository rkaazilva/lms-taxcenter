<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsAbsensi extends Model
{
    protected $table = 'lms_absensis';

    protected $fillable = [
        'email',
        'nama',
        'mapel',
        'metode',
        'timestamp',
    ];
}
