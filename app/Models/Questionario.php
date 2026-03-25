<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionario extends Model
{
    use HasFactory;

    protected $fillable = [
        'modelo_id',
        'titulo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questoes()
    {
        return $this->hasMany(QuestionarioQuestao::class)->orderBy('ordem');
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class);
    }
}
