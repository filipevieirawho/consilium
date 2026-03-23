<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticoResposta extends Model
{
    protected $table = 'diagnostico_respostas';

    protected $fillable = [
        'diagnostico_id',
        'dimensao',
        'pergunta',
        'resposta',
    ];

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class);
    }
}
