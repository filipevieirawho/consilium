<x-mail::message>
    # Novo contato pelo site

    Olá, Jorge! Recebemos um novo contato.

    **Nome:** {{ $contact->name }}<br>
    **E-mail:** {{ $contact->email }}<br>
    **WhatsApp:** {{ $contact->phone ?? 'Não informado' }}<br>

    **Mensagem:**<br>
    {{ $contact->message }}

    @if($contact->opt_in)
        ✅ **Aceitou receber comunicações.**
    @else
        ❌ **Não aceitou comunicações.**
    @endif

    <x-mail::button :url="config('app.url') . '/dashboard'">
        Acessar Painel
    </x-mail::button>

    Obrigado,<br>
    {{ config('app.name') }}
</x-mail::message>