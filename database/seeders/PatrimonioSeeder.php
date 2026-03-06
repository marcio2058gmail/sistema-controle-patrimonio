<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Company;
use Illuminate\Database\Seeder;

class PatrimonioSeeder extends Seeder
{
    public function run(): void
    {
        $patrimonios = [
            ['codigo' => 'NB-001', 'descricao' => 'Notebook Dell Inspiron 15',     'modelo' => 'Inspiron 15 3501',     'serie' => 'DELL-XK9271', 'status' => 'em_uso'],
            ['codigo' => 'NB-002', 'descricao' => 'Notebook Lenovo ThinkPad E14', 'modelo' => 'ThinkPad E14 Gen 3',   'serie' => 'LNVO-PE4892', 'status' => 'em_uso'],
            ['codigo' => 'NB-003', 'descricao' => 'Notebook Apple MacBook Air',   'modelo' => 'MacBook Air M2',        'serie' => 'APPL-C02XP3', 'status' => 'disponivel'],
            ['codigo' => 'NB-004', 'descricao' => 'Notebook HP ProBook 450',      'modelo' => 'ProBook 450 G9',        'serie' => 'HP-WK8741',   'status' => 'manutencao'],
            ['codigo' => 'NB-005', 'descricao' => 'Notebook Asus Vivobook 15',    'modelo' => 'Vivobook 15 X1502',     'serie' => 'ASUS-VB5512', 'status' => 'disponivel'],
            ['codigo' => 'MN-001', 'descricao' => 'Monitor LG 24 Pol Full HD',    'modelo' => 'LG 24MK430H',           'serie' => 'LG-MN0041',   'status' => 'em_uso'],
            ['codigo' => 'MN-002', 'descricao' => 'Monitor Samsung 27 QHD',       'modelo' => 'Samsung S27A600',       'serie' => 'SMSG-Q27002', 'status' => 'disponivel'],
            ['codigo' => 'MN-003', 'descricao' => 'Monitor Dell UltraSharp 24',   'modelo' => 'Dell U2422H',           'serie' => 'DELL-US2403', 'status' => 'em_uso'],
            ['codigo' => 'IM-001', 'descricao' => 'Impressora HP LaserJet Pro',   'modelo' => 'LaserJet Pro M404dn',   'serie' => 'HP-LJ40451',  'status' => 'em_uso'],
            ['codigo' => 'IM-002', 'descricao' => 'Impressora Canon PIXMA',       'modelo' => 'PIXMA G3110',           'serie' => 'CANN-PX3110', 'status' => 'disponivel'],
            ['codigo' => 'TB-001', 'descricao' => 'Tablet Samsung Galaxy Tab',    'modelo' => 'Galaxy Tab A8',         'serie' => 'SMSG-TA8001', 'status' => 'disponivel'],
            ['codigo' => 'TB-002', 'descricao' => 'iPad Apple 10ª Geração',       'modelo' => 'iPad 10.9-inch',        'serie' => 'APPL-IP1001', 'status' => 'em_uso'],
            ['codigo' => 'CA-001', 'descricao' => 'Câmera IP Intelbras VIP',      'modelo' => 'VIP 1130 B',            'serie' => 'INTL-CA1130', 'status' => 'em_uso'],
            ['codigo' => 'RT-001', 'descricao' => 'Roteador Mikrotik hEX',        'modelo' => 'hEX RB750Gr3',          'serie' => 'MKTK-HEX001', 'status' => 'disponivel'],
            ['codigo' => 'SN-001', 'descricao' => 'Switch de rede TP-Link 24p',   'modelo' => 'TL-SG1024D',            'serie' => 'TPLK-SW0024', 'status' => 'disponivel'],
            ['codigo' => 'TD-001', 'descricao' => 'Teclado Mecânico HyperX',      'modelo' => 'HyperX Alloy FPS Pro', 'serie' => 'HPXR-TK0012', 'status' => 'disponivel'],
            ['codigo' => 'MS-001', 'descricao' => 'Mouse Logitech MX Master 3',   'modelo' => 'MX Master 3S',          'serie' => 'LGTC-MX3001', 'status' => 'em_uso'],
            ['codigo' => 'ND-001', 'descricao' => 'No-break APC Back-UPS',        'modelo' => 'Back-UPS BX1500MI',     'serie' => 'APCX-BX1500', 'status' => 'em_uso'],
            ['codigo' => 'PC-001', 'descricao' => 'Desktop Dell OptiPlex 3080',   'modelo' => 'OptiPlex 3080 MT',      'serie' => 'DELL-OP3080', 'status' => 'disponivel'],
            ['codigo' => 'PC-002', 'descricao' => 'Desktop Lenovo ThinkCentre',   'modelo' => 'ThinkCentre M70s',      'serie' => 'LNVO-TC7001', 'status' => 'manutencao'],
        ];

        $empresa = Company::first();

        foreach ($patrimonios as $item) {
            Asset::create([
                'codigo_patrimonio' => $item['codigo'],
                'descricao'         => $item['descricao'],
                'modelo'            => $item['modelo'],
                'numero_serie'      => $item['serie'],
                'status'            => $item['status'],
                'empresa_id'        => $empresa?->id,
            ]);
        }
    }
}
