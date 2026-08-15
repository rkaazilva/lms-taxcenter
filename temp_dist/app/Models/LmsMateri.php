<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsMateri extends Model
{
    protected $table = 'lms_materis';

    protected $fillable = [
        'mapel',
        'judul',
        'link_modul',
        'link_youtube',
        'keterangan',
        'status',
        'kelas',
    ];
}
