<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $fillable = [
        'token',
        'sessao_id',
        'titulo',
        'subtitulo',
        'descricao',
        'contact_id',
        'empresa_id',
        'questionario_id',
        'nome',
        'cargo',
        'empresa',
        'email',
        'telefone',
        'nome_empreendimento',
        'cidade',
        'tipologia',
        'num_torres',
        'estagio_obra',
        'prazo_inicial',
        'prazo_atual',
        'aceite',
        'ipm',
        'status',
    ];

    protected $casts = [
        'aceite' => 'boolean',
        'ipm'    => 'float',
    ];

    public function respostas()
    {
        return $this->hasMany(DiagnosticoResposta::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function questionario()
    {
        return $this->belongsTo(Questionario::class);
    }

    public function sessao()
    {
        return $this->belongsTo(DiagnosticoSessao::class, 'sessao_id');
    }

    public function empresaRelationship()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Human-readable IPM badge colour based on the value.
     */
    public function ipmFaixa(): string
    {
        if ($this->ipm === null) return 'gray';
        if ($this->ipm <= 40) return 'red';
        if ($this->ipm <= 70) return 'yellow';
        return 'green';
    }
}
