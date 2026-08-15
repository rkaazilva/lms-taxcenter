<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsJadwal extends Model
{
    protected $table = 'lms_jadwals';

    protected $fillable = [
        'tanggal',
        'jam',
        'mapel',
        'materi',
        'dosen',
        'link_zoom',
        'status_sesi',
        'blast',
    ];
}
