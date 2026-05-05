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
        'empresa_id',
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

    public function activities()
    {
        return $this->hasMany(ContactActivity::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class);
    }

    /**
     * Extract the email domain, ignoring free providers.
     */
    public static function corporateDomain(string $email): ?string
    {
        $freeProviders = ['gmail', 'hotmail', 'outlook', 'yahoo', 'icloud', 'live', 'bol', 'uol', 'terra'];
        $domain = strtolower(substr($email, strpos($email, '@') + 1));
        $base   = explode('.', $domain)[0];
        return in_array($base, $freeProviders) ? null : $domain;
    }
}
