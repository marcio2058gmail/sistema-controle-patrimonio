<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termo de Responsabilidade #{{ $responsibility->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11pt; color: #1a1a1a; line-height: 1.6; }
        .page { padding: 50px 60px; }
        .header { text-align: center; border-bottom: 2px solid #1a1a1a; padding-bottom: 16px; margin-bottom: 24px; }
        .header h1 { font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .header p { font-size: 9pt; color: #555; margin-top: 4px; }
        .section-title { font-size: 10pt; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.5px; color: #333; border-bottom: 1px solid #ddd;
            padding-bottom: 4px; margin: 20px 0 10px; }
        table.info { width: 100%; border-collapse: collapse; font-size: 10pt; }
        table.info td { padding: 5px 8px; border: 1px solid #ddd; }
        table.info td.label { background-color: #f5f5f5; font-weight: bold; width: 35%; color: #444; }
        .termo-box { border: 1px solid #ccc; border-radius: 4px; padding: 14px 16px;
            background-color: #fafafa; font-size: 10pt; line-height: 1.7; margin-top: 6px; }
        .assinaturas { margin-top: 50px; }
        .assinatura-bloco { display: inline-block; width: 45%; margin-right: 8%; text-align: center; }
        .assinatura-linha { border-top: 1px solid #555; margin-bottom: 6px; }
        .assinatura-nome { font-size: 9pt; color: #333; }
        .footer { margin-top: 40px; padding-top: 12px; border-top: 1px solid #ddd;
            text-align: center; font-size: 8pt; color: #888; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 8pt; font-weight: bold; }
        .badge-ativo { background: #d1fae5; color: #065f46; }
        .badge-devolvido { background: #e5e7eb; color: #374151; }
    </style>
</head>
<body>
<div class="page">

    <div class="header">
        <h1>Termo de Responsabilidade Patrimonial</h1>
        <p>Documento de Controle de Bens — Registro #{{ $responsibility->id }}</p>
    </div>

    <div class="section-title">Dados do Funcionário</div>
    <table class="info">
        <tr>
            <td class="label">Nome</td>
            <td>{{ $responsibility->funcionario->nome }}</td>
            <td class="label">Cargo</td>
            <td>{{ $responsibility->funcionario->cargo ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">E-mail</td>
            <td colspan="3">{{ $responsibility->funcionario->email }}</td>
        </tr>
    </table>

    <div class="section-title">Dados do Patrimônio</div>
    <table class="info">
        <tr>
            <td class="label">Código</td>
            <td>{{ $responsibility->patrimonio->codigo_patrimonio }}</td>
            <td class="label">Status</td>
            <td>
                @if(! $responsibility->data_devolucao)
                    <span class="badge badge-ativo">Em Uso</span>
                @else
                    <span class="badge badge-devolvido">Devolvido</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Descrição</td>
            <td colspan="3">{{ $responsibility->patrimonio->descricao }}</td>
        </tr>
        <tr>
            <td class="label">Modelo</td>
            <td>{{ $responsibility->patrimonio->modelo ?? '—' }}</td>
            <td class="label">Número de Série</td>
            <td>{{ $responsibility->patrimonio->numero_serie ?? '—' }}</td>
        </tr>
    </table>

    <div class="section-title">Período de Responsabilidade</div>
    <table class="info">
        <tr>
            <td class="label">Data de Entrega</td>
            <td>{{ $responsibility->data_entrega->format('d/m/Y') }}</td>
            <td class="label">Data de Devolução</td>
            <td>{{ $responsibility->data_devolucao?->format('d/m/Y') ?? 'Em aberto' }}</td>
        </tr>
        <tr>
            <td class="label">Assinado Fisicamente</td>
            <td colspan="3">{{ $responsibility->assinado ? 'Sim' : 'Não' }}</td>
        </tr>
    </table>

    <div class="section-title">Declaração de Responsabilidade</div>
    <div class="termo-box">{{ $responsibility->termo_responsabilidade }}</div>

    <div class="assinaturas">
        <div class="assinatura-bloco">
            <div class="assinatura-linha"></div>
            <div class="assinatura-nome">
                {{ $responsibility->funcionario->nome }}<br>
                <span style="color:#888">Responsável pelo Bem</span>
            </div>
        </div>
        <div class="assinatura-bloco">
            <div class="assinatura-linha"></div>
            <div class="assinatura-nome">
                Gestor / Administrador<br>
                <span style="color:#888">Autorização</span>
            </div>
        </div>
    </div>

    <div class="footer">
        Documento gerado em {{ now()->format('d/m/Y \à\s H:i') }} — Sistema de Controle Patrimonial
    </div>

</div>
</body>
</html>
