<?php

namespace App\Models;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ContactNote extends Model
{
    protected $fillable = [
        'contact_id',
        'user_id',
        'note',
        'is_pinned',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
