<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'message',
        'opt_in',
        'status',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'opt_in' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contactNotes()
    {
        return $this->hasMany(ContactNote::class);
    }
}
