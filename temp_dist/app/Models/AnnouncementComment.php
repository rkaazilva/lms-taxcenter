<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id',
        'name',
        'email',
        'content',
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}
