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
    font-size:10pt;
    color:#000;
    line-height:1.2;
}

body > p{
    text-align:justify;
}

header{
    position: fixed;
    top:-60px;
    left:0;
    right:0;
    height:50px;
}

.company{
    text-align:center;
}

.company-logo{
    max-height:60px;
    max-width:220px;
}

.title{
    text-align:center;
    font-size:10pt;
    font-weight:bold;
    margin-top:10px;
    margin-bottom:25px;
}

.section-title{
    font-weight:bold;
    margin-top:12px;
    margin-bottom:2px;
}

p{
    margin:1px 0;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:6px;
    text-align:center;
}

th,td{
    border:1px solid #000;
    padding:5px;
    font-size:10pt;
}

th{
    background:#eee;
}

.clause-title{
    font-weight:bold;
    margin-top:6px;
    margin-bottom:0;
}

.clause{
    text-align:justify;
}

.clause + .clause-title:not(.space-before){
    margin-top:0;
}

.space-before{
    margin-top:12px;
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
@php $logoPath = public_path('images/logo-locarmais.png'); @endphp
@if(file_exists($logoPath))
    <img src="{{ $logoPath }}" class="company-logo">
@else
    <span style="font-size:15pt;font-weight:bold;">{{ $company?->nome ?? 'Empresa' }}</span>
@endif
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


<p class="clause-title space-before">1. CLÁUSULA PRIMEIRA – DO OBJETO</p>  
<p class="clause">1.1 O presente Termo tem como objetivo regular o uso do(s) equipamento(s) de propriedade da EMPRESA, cedido(s) ao COLABORADOR, acima qualificado,
sendo recebido(s) pelo COLABORADOR em perfeito estado de conservação e funcionamento.</p>
<p class="clause">1.1.1 Caso o COLABORADOR não concorde com a CLÁUSULA 1.1 deverá alertar seu superior imediato antes da assinatura
do presente, sob pena de ser constatado que o defeito/dano foi ocasionado pelo COLABORADOR.</p>
<p class="clause">1.2 O COLABORADOR declara que recebeu o(s) seguinte(s) equipamento(s) sob sua responsabilidade:</p>


<table>

<thead>
<tr>
<th>Equipamento</th>
<th>Marca</th>
<th>Modelo</th>
<th>Etiqueta de<br>Identificação</th>
<th>Data de<br>Devolução</th>
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
<td>{{ $asset->visto ?? '  ' }}</td>
</tr>

@endforeach

</tbody>

</table>


<p class="clause-title space-before">2. CLÁUSULA SEGUNDA – DAS FORMAS DE UTILIZAÇÃO</p>

<p class="clause">
2.1 A utilização do(s) equipamento(s) acima descrito(s) se destina única e exclusivamente para fins de exercício das atividades inerentes à função do COLABORADOR nas dependências da EMPRESA.
</p>

<p class="clause">2.2 É expressamente vedado(a):</p>

<p class="clause">2.2.1 A utilização do equipamento para tratativas de assuntos particulares;</p>

<p class="clause">
2.2.2 Acessar ou armazenar conteúdo impróprio, de natureza ilegal ou antiética, configurando crime virtual. Crimes virtuais são os delitos praticados através da internet que podem ser enquadrados no Código Penal Brasileiro e seguem as punições previstas na Lei.
</p>

<p class="clause">
2.2.3 A utilização do Equipamento fora das dependências da EMPRESA, salvo em casos em que haja a anuência por escrito por parte do Representante legal da Empresa.
</p>


<p class="clause-title space-before">3. CLÁUSULA TERCEIRA – DA RESPONSABILIDADE</p>

<p class="clause">
3.1 O COLABORADOR compromete-se a manter o(s) equipamento(s) em perfeito estado de conservação e uso, devendo devolvê-lo(s) nas mesmas condições em que o(s) recebeu, em caso de troca de equipamento ou rescisão de Contrato entre COLABORADOR e EMPRESA.
</p>

<p class="clause">
3.2 Caso o(s) equipamento(s) apresente(m) problemas técnicos ou danos decorrentes de mau uso, o COLABORADOR compromete-se a arcar com os custos necessários para reparo e/ou substituição da(s) peça(s) danificada(s).
</p>


<p class="clause-title space-before">4. CLÁUSULA QUARTA – DOS DEVERES</p>

<p class="clause">4.1 O COLABORADOR compromete-se com:</p>

<p class="clause">
4.1.1 A conservação e o cuidado com o equipamento, sendo de total responsabilidade do COLABORADOR no uso e atribuições inerentes ao trabalho;
</p>

<p class="clause">
4.1.2 Comunicar diretamente à EMPRESA a necessidade de manutenção do(s) equipamento(s), sendo vedado encaminhar o(s) equipamento(s) para assistência sem anuência da EMPRESA;
</p>

<p class="clause">
4.1.5 Comunicar imediatamente à EMPRESA qualquer ocorrência relacionada ao(s) equipamento(s), tais como danos, avarias, roubo ou furto.
</p>

<p class="clause">
4.1.6 Devolver imediatamente o(s) equipamento(s) em caso de troca de equipamento ou rescisão de Contrato entre COLABORADOR e EMPRESA.
</p>


<p class="clause-title space-before">5. CLÁUSULA QUINTA – DAS DISPOSIÇÕES GERAIS</p>

<p class="clause">
5.1 As cláusulas e/ou condições ora pactuadas poderão ser revistas, suprimidas e/ou revogadas no todo ou em parte a critério da EMPRESA, mediante comunicação ao COLABORADOR.
</p>

<p class="clause">
5.2 Nenhuma indenização será devida pela EMPRESA, durante a vigência do presente Termo, ou em caso de revogação, suspensão ou extinção do mesmo.
</p>

<p class="clause">
5.3 Por não ser permitida a utilização do equipamento para fins particulares, o mesmo não integrará salário para qualquer fim, seja ele previdenciário, trabalhista ou tributário.
</p>

<p class="clause">
5.4 O presente Termo terá início a partir da data de sua assinatura e vigorará por prazo indeterminado, enquanto durar o vínculo entre a EMPRESA e o COLABORADOR, podendo ser revogado a qualquer tempo.
</p>

<p class="clause">
5.5 Dar-se-á como automaticamente extinto o presente Termo na ocorrência das seguintes hipóteses: mudança de função cuja utilização do equipamento deixe de ser necessária; determinação ou liberalidade da EMPRESA; e extinção, cessação ou rescisão do contrato entre COLABORADOR e EMPRESA.
</p>

<p class="clause space-before">
E por estarem de pleno acordo com todas as cláusulas e condições ora pactuadas, assinam o presente instrumento em duas vias de igual teor, sendo uma delas entregue ao COLABORADOR.
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