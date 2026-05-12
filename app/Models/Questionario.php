<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'titulo',
        'subtitulo',
        'descricao',
        'modelo_id',
        'is_active',
        'texto_resultado_red',
        'texto_resultado_yellow',
        'texto_resultado_green',
        'texto_disclaimer',
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
