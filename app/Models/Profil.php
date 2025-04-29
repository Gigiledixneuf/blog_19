<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $fillable = [
        "image",
        'bio',
        'phone',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
