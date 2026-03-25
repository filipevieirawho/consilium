<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Contact;
use App\Models\Diagnostico;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nome_fantasia',
        'razao_social',
        'cnpj',
        'segmento',
        'porte',
        'tipo_unidade',
        'cep',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'pais',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class);
    }
}
