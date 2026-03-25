<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionarioQuestao extends Model
{
    use HasFactory;

    protected $table = 'questionario_questoes';

    protected $fillable = [
        'questionario_id',
        'dimensao_nome',
        'dimensao_peso',
        'texto',
        'ordem',
    ];

    public function questionario()
    {
        return $this->belongsTo(Questionario::class);
    }
}
