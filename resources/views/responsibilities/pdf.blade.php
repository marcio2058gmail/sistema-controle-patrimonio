<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Termo de Responsabilidade #{{ $responsibility->id }}</title>

<style>

@page {
    margin: 80px 50px;
}

body{
    font-family: Arial, Helvetica, sans-serif;
    font-size:08pt;
    color:#000;
    line-height:1.6;
}

header{
    position: fixed;
    top:-60px;
    left:0;
    right:0;
    height:50px;
}

.company{
    display:table;
    width:100%;
}

.company-logo{
    display:table-cell;
    width:100px;
    vertical-align:middle;
}

.company-info{
    display:table-cell;
    vertical-align:middle;
}

.company-name{
    font-size:15pt;
    font-weight:bold;
}

.company-meta{
    font-size:9pt;
}

.title{
    text-align:center;
    font-size:10pt;
    font-weight:bold;
    margin-top:10px;
    margin-bottom:25px;
}

p{
    text-align:justify;
}

.section-title{
    font-weight:bold;
    margin-top:18px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}

th,td{
    border:1px solid #000;
    padding:6px;
    font-size:10pt;
}

th{
    background:#eee;
}

/* evita quebrar assinatura */
.signature-block{
    page-break-inside: avoid;
    margin-top:40px;
}

.signature-table{
    width:100%;
    margin-top:30px;
}

.signature-cell{
    width:50%;
    text-align:center;
}

.signature-line{
    border-top:1px solid #000;
    width:70%;
    margin:0 auto;
    padding-top:6px;
}

.qrcode{
    text-align:center;
    margin-top:20px;
    font-size:9pt;
}

</style>

</head>

<body>

<header>

<div class="company">

<div class="company-info">

<div class="company-name">
{{ $company?->nome ?? 'Empresa' }}
</div>

<div class="company-meta">

@if($company?->cnpj)
CNPJ: {{ $company->cnpj }}
@endif

@if($company?->telefone)
| Tel: {{ $company->telefone }}
@endif

@if($company?->email)
| {{ $company->email }}
@endif

</div>

</div>

</div>

</header>

@php
    $nomeColaborador  = $responsibility->employee->nome;
    $cargoColaborador = $responsibility->employee->cargo ?? '—';
    $cpfColaborador   = $responsibility->employee->user?->cpf ?? '—';
    $rgColaborador    = $responsibility->employee->rg_numero ?? '—';
    $ctpsNumero       = $responsibility->employee->ctps_numero ?? '—';
    $ctpsSerie        = $responsibility->employee->ctps_serie ?? '—';
    $nomeEmpresa      = $company?->nome ?? 'EMPRESA';
    $cnpjEmpresa      = $company?->cnpj ?? null;
@endphp

<div class="title">
<u>TERMO DE RESPONSABILIDADE POR UTILIZAÇÃO DE EQUIPAMENTO CORPORATIVO</u>
</div>


<p>

Pelo presente TERMO DE RESPONSABILIDADE POR UTILIZAÇÃO DE EQUIPAMENTO CORPORATIVO, de um lado

<strong>{{ $nomeEmpresa }}</strong>@if($cnpjEmpresa), inscrita no CNPJ nº {{ $cnpjEmpresa }}@endif,
com sede à Av. Pioneiro Alício Arantes Campolina, nº 2527, Jardim Canadá, na cidade de Maringá no Estado
do Paraná, CEP: 87083-020, doravante denominada "EMPRESA", e de outro lado

<strong>{{ $nomeColaborador }}</strong>,
exercendo a função de <strong>{{ $cargoColaborador }}</strong>, portador(a) da CTPS nº {{ $ctpsNumero }} série {{ $ctpsSerie }},
da Cédula de Identidade RG nº {{ $rgColaborador }} e inscrito(a) no 
CPF/MF sob nº {{ $cpfColaborador }}, doravante denominado <strong>COLABORADOR</strong>, tem entre si,
justo e contratado o que a seguir especificam:

</p>

<div class="section-title">
1. CLÁUSULA PRIMEIRA – DO OBJETO
</div>
1.1 O presente Termo tem como objetivo regular o uso do(s) equipamento(s) de propriedade da EMPRESA, cedido(s) ao COLABORADOR, acima qualificado,
sendo recebido(s) pelo COLABORADOR em perfeito estado de conservação e funcionamento.<br>
1.1.1 Caso o COLABORADOR não concorde com a CLÁUSULA 1.1 deverá alertar seu superior imediato antes da assinatura
do presente, sob pena de ser constatado que o defeito/dano foi ocasionado pelo COLABORADOR.<br>
1.2 O COLABORADOR declara que recebeu o(s) seguinte(s) equipamento(s) sob sua responsabilidade:<br>


<table>

<thead>
<tr>
<th>Equipamento</th>
<th>Marca</th>
<th>Modelo</th>
<th>Etiqueta de Indentificação</th>
<th>Data de Devolução</th>
<th>Visto</th>
</tr>
</thead>

<tbody>

@foreach($responsibility->assets as $asset)

<tr>
<td>{{ $asset->descricao }}</td>
<td>{{ $asset->marca ?? '—' }}</td>
<td>{{ $asset->modelo ?? '—' }}</td>
<td>{{ $asset->codigo_patrimonio }}</td>
<td>{{ $asset->data_devolucao?->format('d/m/Y') ?? '—' }}</td>
<td>{{ $asset->visto ?? '—' }}</td>
</tr>

@endforeach

</tbody>

</table>


<div class="section-title">
2. CLÁUSULA SEGUNDA – DAS FORMAS DE UTILIZAÇÃO
</div>

<p>
<u><strong>2.1 A utilização do(s) equipamento(s) se destina exclusivamente para fins de exercícios das atividades inerentes à função do COLABORADOR nas dependências da EMPRESA.</u></strong>
2.2 É expressamente vedado(a):

</p>


<div class="section-title">
3. CLÁUSULA TERCEIRA – DA RESPONSABILIDADE
</div>

<p>
O COLABORADOR compromete-se a manter o equipamento em perfeito estado de conservação e devolvê-lo nas mesmas condições em caso de troca ou desligamento.
</p>


<div class="section-title">
4. CLÁUSULA QUARTA – DOS DEVERES
</div>

<p>
O COLABORADOR compromete-se a zelar pela conservação do equipamento, comunicar problemas técnicos e informar imediatamente qualquer ocorrência de dano, roubo ou extravio.
</p>


<div class="section-title">
5. CLÁUSULA QUINTA – DISPOSIÇÕES GERAIS
</div>

<p>
Este termo permanecerá válido enquanto durar o vínculo entre EMPRESA e COLABORADOR.
</p>


@if($responsibility->data_devolucao)
<div class="section-title">Registro de Devolução</div>
<table>
<tr>
<th style="width:30%">Data de Devolução</th>
<td>{{ $responsibility->data_devolucao->format('d/m/Y') }}</td>
</tr>
@if($responsibility->observacao_devolucao)
<tr>
<th>Observações</th>
<td>{{ $responsibility->observacao_devolucao }}</td>
</tr>
@endif
</table>
@endif

<p style="margin-top:30px">
MARINGÁ-PR, {{ now()->isoFormat('D [de] MMMM [de] Y') }}
</p>


<div class="signature-block">

<table class="signature-table">

<tr>

<td class="signature-cell">

@if($responsibility->assinatura_base64)

<div style="height:70px">
<img src="{{ $responsibility->assinatura_base64 }}" style="max-height:60px">
</div>

@else
<div style="height:70px"></div>
@endif

<div class="signature-line">

<strong>{{ $nomeColaborador }}</strong>

<br>

CPF: {{ $cpfColaborador }}

@if($responsibility->assinado_em)
<br>
<span style="font-size:9pt">
Assinado em {{ $responsibility->assinado_em->format('d/m/Y H:i') }}
</span>
@endif

</div>

</td>


<td class="signature-cell">

<div style="height:70px"></div>

<div class="signature-line">

<strong>{{ $nomeEmpresa }}</strong>

@if($cnpjEmpresa)
<br>
CNPJ: {{ $cnpjEmpresa }}
@endif

<br>
Responsável pelo Patrimônio

</div>

</td>

</tr>

</table>


<p style="text-align:center; font-size:9pt; margin-top:20px; color:#555">
    Documento gerado em {{ now()->format('d/m/Y \à\s H:i') }}
    @if($cnpjEmpresa) · {{ $nomeEmpresa }} · CNPJ {{ $cnpjEmpresa }}@endif
</p>

</div>


</body>
</html>