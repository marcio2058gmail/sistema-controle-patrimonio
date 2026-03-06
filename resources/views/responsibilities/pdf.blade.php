<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Termo de Responsabilidade #{{ $responsibility->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            padding: 30px 40px;
            line-height: 1.4;
        }

        /* ── Cabeçalho ── */
        .header-wrap {
            display: table;
            width: 100%;
            border-bottom: 3px solid #1a1a1a;
            padding-bottom: 12px;
            margin-bottom: 4px;
        }
        .header-left  { display: table-cell; vertical-align: middle; width: 70%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 30%; }
        .company-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .company-sub  { font-size: 8.5pt; color: #555; margin-top: 3px; }
        .doc-num      { font-size: 8.5pt; color: #777; }

        /* ── Título ── */
        .doc-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 14px 0 16px;
            border: 1.5px solid #1a1a1a;
            padding: 8px 4px;
        }

        /* ── Seções ── */
        .section { margin-bottom: 18px; }
        .section-title {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #fff;
            background: #1a1a1a;
            padding: 4px 8px;
            margin-bottom: 10px;
        }

        /* ── Grid de info ── */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 4px 6px; vertical-align: top; font-size: 9.5pt; }
        .info-table .label { color: #555; font-size: 8.5pt; display: block; margin-bottom: 1px; }
        .info-table .value { font-weight: 600; border-bottom: 1px solid #ccc; display: block; min-height: 16px; padding-bottom: 2px; }

        /* ── Tabela de equipamentos ── */
        .eq-table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-top: 2px; }
        .eq-table thead tr { background: #1a1a1a; color: #fff; }
        .eq-table th { padding: 6px 8px; text-align: left; font-size: 8.5pt; text-transform: uppercase; letter-spacing: 0.4px; }
        .eq-table tbody tr:nth-child(even) { background: #f5f5f5; }
        .eq-table tbody tr:nth-child(odd)  { background: #fff; }
        .eq-table td { padding: 6px 8px; border-bottom: 1px solid #ddd; vertical-align: top; }
        .eq-table .center { text-align: center; }

        /* ── Declaração / cláusulas ── */
        .declaration {
            font-size: 9.5pt;
            line-height: 1.75;
            text-align: justify;
            white-space: pre-line;
            background: #fafafa;
            border: 1px solid #ddd;
            border-left: 4px solid #1a1a1a;
            padding: 12px 14px;
        }

        /* ── Assinaturas ── */
        .signatures { display: table; width: 100%; margin-top: 40px; }
        .sig-col    { display: table-cell; width: 48%; text-align: center; vertical-align: bottom; }
        .sig-space  { display: table-cell; width: 4%; }
        .sig-img-box {
            min-height: 72px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            margin-bottom: 4px;
        }
        .sig-img-box img { max-height: 72px; max-width: 96%; }
        .sig-line-rule { border-top: 1.5px solid #1a1a1a; padding-top: 5px; margin-top: 2px; }
        .sig-name   { font-weight: bold; font-size: 10pt; }
        .sig-role   { font-size: 8.5pt; color: #555; margin-top: 2px; }
        .sig-audit  { font-size: 7.5pt; color: #999; margin-top: 3px; }
        .sig-date-line { margin-top: 30px; font-size: 9.5pt; text-align: center; }

        /* ── Rodapé ── */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            font-size: 7.5pt;
            color: #aaa;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- ======================== CABEÇALHO ======================== --}}
    <div class="header-wrap">
        <div class="header-left">
            <div class="company-name">{{ $company?->nome ?? 'Empresa' }}</div>
            @if($company?->cnpj)
            <div class="company-sub">CNPJ: {{ $company->cnpj }}</div>
            @endif
            @if($company?->email || $company?->telefone)
            <div class="company-sub">
                @if($company->telefone) Tel.: {{ $company->telefone }} @endif
                @if($company->email && $company->telefone) &nbsp;|&nbsp; @endif
                @if($company->email) {{ $company->email }} @endif
            </div>
            @endif
        </div>
        <div class="header-right">
            <div class="doc-num">Termo nº {{ str_pad($responsibility->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div class="doc-num">Emitido em: {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- ======================== TÍTULO ======================== --}}
    <div class="doc-title">Termo de Responsabilidade de Uso de Equipamento</div>

    {{-- ======================== IDENTIFICAÇÃO DO RESPONSÁVEL ======================== --}}
    <div class="section">
        <div class="section-title">Identificação do Responsável</div>
        <table class="info-table">
            <tr>
                <td style="width:40%">
                    <span class="label">Nome Completo</span>
                    <span class="value">{{ $responsibility->employee->nome }}</span>
                </td>
                <td style="width:30%">
                    <span class="label">Cargo / Função</span>
                    <span class="value">{{ $responsibility->employee->cargo ?? '—' }}</span>
                </td>
                <td style="width:30%">
                    <span class="label">Departamento</span>
                    <span class="value">{{ $responsibility->employee->department?->nome ?? '—' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">E-mail</span>
                    <span class="value">{{ $responsibility->employee->email ?? '—' }}</span>
                </td>
                <td>
                    @php $cpf = $responsibility->employee->user?->cpf; @endphp
                    <span class="label">CPF</span>
                    <span class="value">{{ $cpf ?? '—' }}</span>
                </td>
                <td>
                    <span class="label">Data de Entrega</span>
                    <span class="value">{{ $responsibility->data_entrega->format('d/m/Y') }}</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- ======================== EQUIPAMENTOS ======================== --}}
    <div class="section">
        <div class="section-title">Equipamentos sob Responsabilidade ({{ $responsibility->assets->count() }} item(ns))</div>
        <table class="eq-table">
            <thead>
                <tr>
                    <th style="width:15%">Código</th>
                    <th style="width:35%">Descrição</th>
                    <th style="width:25%">Modelo</th>
                    <th style="width:25%">Nº de Série</th>
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

    {{-- ======================== DECLARAÇÃO ======================== --}}
    <div class="section">
        <div class="section-title">Termo de Declaração e Responsabilidade</div>
        @php
            $empresa = $company?->nome ?? 'esta empresa';
            $textoTermo = $responsibility->termo_responsabilidade
                ?: "Eu, {$responsibility->employee->nome}, declaro ter recebido o(s) equipamento(s) listado(s) acima, pertencente(s) a {$empresa}, em perfeitas condições de uso, e assumo total responsabilidade pela sua guarda, conservação e utilização exclusiva para fins profissionais.

Comprometo-me a:
  1. Utilizar os equipamentos apenas para atividades relacionadas às minhas funções profissionais em {$empresa};
  2. Comunicar imediatamente qualquer dano, perda, furto ou extravio ao setor de patrimônio;
  3. Devolver os equipamentos em condições adequadas ao término do vínculo empregatício ou quando solicitado pela empresa;
  4. Não realizar alterações, instalações de software não autorizado ou modificações físicas sem autorização prévia;
  5. Responsabilizar-me pelos custos de reparo ou reposição em caso de dano por mau uso ou negligência.

A inobservância destas cláusulas poderá acarretar as sanções previstas na legislação vigente e no regulamento interno de {$empresa}.";
        @endphp
        <div class="declaration">{{ $textoTermo }}</div>
    </div>

    {{-- ======================== LOCAL E DATA ======================== --}}
    <div class="sig-date-line">
        _____________________________, {{ now()->isoFormat('D [de] MMMM [de] Y') }}
    </div>

    {{-- ======================== ASSINATURAS ======================== --}}
    <div class="signatures">
        <div class="sig-col">
            <div class="sig-img-box">
                @if($responsibility->assinatura_base64)
                    <img src="{{ $responsibility->assinatura_base64 }}" alt="Assinatura do funcionário">
                @endif
            </div>
            <div class="sig-line-rule">
                <div class="sig-name">{{ $responsibility->employee->nome }}</div>
                <div class="sig-role">{{ $responsibility->employee->cargo ?? 'Funcionário Responsável' }}</div>
                @if($responsibility->assinado_em)
                <div class="sig-audit">Assinado digitalmente em {{ $responsibility->assinado_em->format('d/m/Y \à\s H:i') }}</div>
                @endif
            </div>
        </div>
        <div class="sig-space"></div>
        <div class="sig-col">
            <div class="sig-img-box"></div>
            <div class="sig-line-rule">
                <div class="sig-name">{{ $company?->nome ?? 'Empresa' }}</div>
                <div class="sig-role">Responsável pelo Patrimônio</div>
            </div>
        </div>
    </div>

    {{-- ======================== RODAPÉ ======================== --}}
    <div class="footer">
        Documento gerado em {{ now()->format('d/m/Y \à\s H:i') }} pelo Sistema de Controle de Patrimônio
        @if($company?->cnpj) — {{ $company->nome }} | CNPJ {{ $company->cnpj }} @endif
    </div>

</body>
</html>
