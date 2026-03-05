<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Termo de Responsabilidade #{{ $responsibility->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11pt; color: #1a1a1a; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1a1a1a; padding-bottom: 16px; }
        .header h1 { font-size: 18pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .header p { font-size: 10pt; color: #555; margin-top: 4px; }
        .section { margin-bottom: 24px; }
        .section-title { font-size: 10pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #555; border-bottom: 1px solid #ddd; padding-bottom: 4px; margin-bottom: 12px; }
        .info-grid { display: flex; flex-wrap: wrap; gap: 8px 24px; }
        .info-item { min-width: 200px; }
        .info-label { font-size: 9pt; color: #666; }
        .info-value { font-size: 11pt; font-weight: 500; }
        table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        table thead tr { background: #1a1a1a; color: #fff; }
        table th { padding: 7px 10px; text-align: left; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.5px; }
        table tbody tr:nth-child(even) { background: #f5f5f5; }
        table td { padding: 7px 10px; border-bottom: 1px solid #e0e0e0; vertical-align: top; }
        .termo-box { background: #f9f9f9; border: 1px solid #ddd; border-left: 4px solid #1a1a1a; padding: 14px; font-size: 10pt; line-height: 1.7; white-space: pre-line; }
        .signatures { margin-top: 50px; display: flex; justify-content: space-between; }
        .sig-line { text-align: center; width: 42%; }
        .sig-line .line { border-top: 1px solid #1a1a1a; padding-top: 6px; font-size: 10pt; }
        .sig-line .label { font-size: 9pt; color: #666; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 9pt; font-weight: 600; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .footer { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 9pt; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Termo de Responsabilidade</h1>
        <p>Documento #{{ $responsibility->id }} — Gerado em {{ now()->format('d/m/Y \à\s H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Dados do Responsável</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Funcionário</div>
                <div class="info-value">{{ $responsibility->employee->nome }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Cargo</div>
                <div class="info-value">{{ $responsibility->employee->cargo ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Data de Entrega</div>
                <div class="info-value">{{ $responsibility->data_entrega->format('d/m/Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Data de Devolução</div>
                <div class="info-value">{{ $responsibility->data_devolucao?->format('d/m/Y') ?? 'Em aberto' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Assinado</div>
                <div class="info-value">
                    @if($responsibility->assinado)
                        <span class="badge badge-green">Sim</span>
                    @else
                        <span class="badge badge-yellow">Não</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Equipamentos ({{ $responsibility->assets->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Modelo</th>
                    <th>Nº Série</th>
                </tr>
            </thead>
            <tbody>
                @foreach($responsibility->assets as $asset)
                <tr>
                    <td>{{ $asset->codigo_patrimonio }}</td>
                    <td>{{ $asset->descricao }}</td>
                    <td>{{ $asset->modelo ?? '—' }}</td>
                    <td>{{ $asset->numero_serie ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($responsibility->termo_responsabilidade)
    <div class="section">
        <div class="section-title">Declaração</div>
        <div class="termo-box">{{ $responsibility->termo_responsabilidade }}</div>
    </div>
    @endif

    <div class="signatures">
        <div class="sig-line">
            <div class="line">{{ $responsibility->employee->nome }}</div>
            <div class="label">Funcionário Responsável</div>
        </div>
        <div class="sig-line">
            <div class="line">Gestor de Patrimônio</div>
            <div class="label">Setor de Patrimônio</div>
        </div>
    </div>

    <div class="footer">
        Este documento foi gerado automaticamente pelo Sistema de Controle de Patrimônio.
    </div>
</body>
</html>
