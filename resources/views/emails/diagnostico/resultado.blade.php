<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Diagnóstico</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">

@php
    $ipm = $resultado['ipm'];
    $faixa = $resultado['faixa'];
    $dimensoesFracas = $resultado['dimensoes_fracas'];
    $dimensoes = $resultado['dimensoes'];

    $faixaConfig = [
        'red'    => ['label' => 'Previsibilidade Comprometida', 'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fca5a5'],
        'yellow' => ['label' => 'Previsibilidade Instável',     'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fcd34d'],
        'green'  => ['label' => 'Previsibilidade Consistente',  'color' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#86efac'],
    ];
    $cfg = $faixaConfig[$faixa];

    $tituloDiagnostico = $diagnostico->titulo
        ?? ($diagnostico->questionario?->titulo ?? 'Previsibilidade de Margem');

    $nome = $diagnostico->nome;
    $primeiroNome = explode(' ', $nome)[0];
    $resultUrl = route('diagnostico.result', $diagnostico->token);
@endphp

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f4f6;">
    <tr>
        <td align="center" style="padding:32px 16px;">

            <!-- Card wrapper -->
            <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">

                <!-- Header -->
                <tr>
                    <td style="background-color:#111827;padding:24px 32px;border-bottom:3px solid #D0AE6D;">
                        <img src="{{ asset('assets/images/logo-horizontal-crop.png') }}"
                             alt="Consilium"
                             width="140"
                             style="display:block;height:auto;">
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:36px 32px 0;">

                        <!-- Greeting -->
                        <p style="margin:0 0 8px;font-size:14px;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Olá, {{ $primeiroNome }}</p>
                        <h1 style="margin:0 0 24px;font-size:22px;font-weight:700;color:#111827;line-height:1.3;">
                            Seu diagnóstico de<br>{{ $tituloDiagnostico }} está pronto.
                        </h1>

                        @if($diagnostico->nome_empreendimento)
                        <p style="margin:0 0 24px;font-size:14px;color:#6b7280;">
                            Empreendimento: <strong style="color:#374151;">{{ $diagnostico->nome_empreendimento }}</strong>
                        </p>
                        @endif

                        <!-- IPM Score -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
                            <tr>
                                <td style="background-color:#fdf8ed;border:1px solid #D0AE6D;border-radius:10px;padding:24px 28px;">
                                    <p style="margin:0 0 4px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#D0AE6D;">Índice de Previsibilidade de Margem</p>
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td style="vertical-align:middle;">
                                                <span style="font-size:56px;font-weight:800;color:#111827;line-height:1;">{{ number_format($ipm, 1) }}</span>
                                                <span style="font-size:22px;font-weight:600;color:#6b7280;margin-left:2px;">/100</span>
                                            </td>
                                            <td style="vertical-align:middle;padding-left:20px;">
                                                <span style="display:inline-block;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:700;letter-spacing:0.04em;background-color:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};border:1px solid {{ $cfg['border'] }};">
                                                    {{ $cfg['label'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Interpretation text -->
                        <p style="margin:0 0 28px;font-size:14px;color:#4b5563;line-height:1.7;">
                            {{ $resultado['texto'] }}
                        </p>

                        @if(count($dimensoesFracas) > 0)
                        <!-- Weak dimensions -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                            <tr>
                                <td style="padding-bottom:10px;">
                                    <p style="margin:0;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#9ca3af;">Dimensões com maior atenção</p>
                                </td>
                            </tr>
                            @foreach($dimensoesFracas as $nomeDim)
                            @php
                                $dimData = collect($dimensoes)->firstWhere('nome', $nomeDim);
                                $score = $dimData ? $dimData['score'] : 0;
                            @endphp
                            <tr>
                                <td style="padding-bottom:8px;">
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px 16px;">
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td style="font-size:13px;font-weight:600;color:#374151;">{{ $nomeDim }}</td>
                                                        <td align="right" style="font-size:13px;font-weight:700;color:#dc2626;">{{ $score }}/100</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        @endif

                        <!-- CTA -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:36px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $resultUrl }}"
                                       style="display:inline-block;padding:14px 36px;background-color:#D0AE6D;color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;border-radius:8px;letter-spacing:0.03em;">
                                        Ver resultado completo →
                                    </a>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- Divider -->
                <tr>
                    <td style="padding:0 32px;">
                        <hr style="border:none;border-top:1px solid #f3f4f6;margin:0;">
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding:24px 32px 28px;">
                        <p style="margin:0 0 4px;font-size:12px;color:#9ca3af;line-height:1.6;">
                            Este relatório foi gerado pelo sistema Consilium e enviado exclusivamente para <strong style="color:#6b7280;">{{ $diagnostico->email }}</strong>.
                        </p>
                        <p style="margin:0;font-size:12px;color:#9ca3af;line-height:1.6;">
                            Em caso de dúvidas, responda este e-mail ou entre em contato com <a href="mailto:jorge@consilium.eng.br" style="color:#D0AE6D;text-decoration:none;">jorge@consilium.eng.br</a>.
                        </p>
                    </td>
                </tr>

                <!-- Bottom bar -->
                <tr>
                    <td style="background-color:#111827;padding:14px 32px;">
                        <p style="margin:0;font-size:11px;color:#4b5563;text-align:center;">© {{ date('Y') }} Consilium Engenharia. Todos os direitos reservados.</p>
                    </td>
                </tr>

            </table>
            <!-- /Card wrapper -->

        </td>
    </tr>
</table>

</body>
</html>
