<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsUser extends Model
{
    protected $table = 'lms_users';

    protected $fillable = [
        'email',
        'password',
        'role',
        'nama',
        'link',
        'sertifikat',
        'kelas',
        'telepon',
    ];

    /**
     * Helper to find user by email
     */
    public static function findByEmail(string $email)
    {
        return self::where('email', trim(strtolower($email)))->first();
    }
}
