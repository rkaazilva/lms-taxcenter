<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsSubmission extends Model
{
    protected $table = 'lms_submissions';

    protected $fillable = [
        'id_tugas',
        'email',
        'nama_siswa',
        'link_tugas',
        'nilai',
        'feedback',
        'submitted_at',
    ];
}
