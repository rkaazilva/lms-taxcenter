<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsTugas extends Model
{
    protected $table = 'lms_tugas';

    protected $fillable = [
        'id_tugas',
        'mapel',
        'judul',
        'deskripsi',
        'link_soal',
        'deadline',
        'kelas',
        'blast',
    ];
}
