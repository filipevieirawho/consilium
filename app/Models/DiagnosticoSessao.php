<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticoSessao extends Model
{
    protected $table = 'diagnostico_sessoes';

    protected $fillable = [
        'token',
        'questionario_id',
        'empresa_id',
        'titulo',
        'descricao',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questionario()
    {
        return $this->belongsTo(Questionario::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class, 'sessao_id');
    }

    /** Only concluído entries count as responses. */
    public function respostas()
    {
        return $this->hasMany(Diagnostico::class, 'sessao_id')->where('status', 'concluido');
    }
}
