<?php

namespace App\Mail;

use App\Models\Diagnostico;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DiagnosticoResultadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Diagnostico $diagnostico,
        public array $resultado,
    ) {
        $this->diagnostico->loadMissing('questionario');
    }

    public function envelope(): Envelope
    {
        $titulo = $this->diagnostico->titulo
            ?? ($this->diagnostico->questionario?->titulo ?? 'Previsibilidade de Margem');

        $empreendimento = $this->diagnostico->nome_empreendimento
            ? ' — ' . $this->diagnostico->nome_empreendimento
            : '';

        return new Envelope(
            from: new Address('jorge@consilium.eng.br', 'Jorge Consilium'),
            subject: 'Seu diagnóstico de ' . $titulo . ' está pronto' . $empreendimento,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.diagnostico.resultado',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
