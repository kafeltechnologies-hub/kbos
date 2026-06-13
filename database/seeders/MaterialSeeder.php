<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            // Electrical poles
            ['MAT-POLE-001', '9m Concrete Pole', '9m reinforced concrete distribution pole', 'Electrical Poles', 'No.', 0, 0],
            ['MAT-POLE-002', '11m Concrete Pole', '11m reinforced concrete distribution pole', 'Electrical Poles', 'No.', 0, 0],
            ['MAT-POLE-003', '12m Concrete Pole', '12m reinforced concrete distribution pole', 'Electrical Poles', 'No.', 0, 0],

            // Conductors
            ['MAT-CON-001', '50mm AAC Conductor', '50mm² all aluminium conductor', 'Conductors', 'm', 0, 0],
            ['MAT-CON-002', '70mm AAC Conductor', '70mm² all aluminium conductor', 'Conductors', 'm', 0, 0],
            ['MAT-CON-003', '120mm Hard Drawn Aluminium Conductor', '120mm² hard drawn aluminium overhead conductor', 'Conductors', 'm', 0, 0],
            ['MAT-CON-004', '185mm XLPE Copper Cable', '4-core 185mm² XLPE copper underground cable', 'Cables', 'm', 0, 0],
            ['MAT-CON-005', '240mm XLPE Aluminium Cable', '240mm² XLPE aluminium power cable', 'Cables', 'm', 0, 0],

            // Transformers
            ['MAT-TRF-001', '50kVA Distribution Transformer', '11/0.433kV 50kVA distribution transformer', 'Transformers', 'No.', 0, 0],
            ['MAT-TRF-002', '100kVA Distribution Transformer', '11/0.433kV 100kVA distribution transformer', 'Transformers', 'No.', 0, 0],
            ['MAT-TRF-003', '200kVA Distribution Transformer', '11/0.433kV 200kVA distribution transformer', 'Transformers', 'No.', 0, 0],
            ['MAT-TRF-004', '315kVA Distribution Transformer', '11/0.433kV 315kVA distribution transformer', 'Transformers', 'No.', 0, 0],
            ['MAT-TRF-005', '500kVA Distribution Transformer', '11/0.433kV 500kVA distribution transformer', 'Transformers', 'No.', 0, 0],

            // Protection and switching
            ['MAT-SWG-001', '11kV Drop Out Fuse', '11kV drop out fuse assembly', 'Switchgear', 'No.', 0, 0],
            ['MAT-SWG-002', '11kV Lightning Arrester', '11kV polymer lightning arrester', 'Switchgear', 'No.', 0, 0],
            ['MAT-SWG-003', '11kV Isolator', '11kV outdoor isolator switch', 'Switchgear', 'No.', 0, 0],
            ['MAT-SWG-004', 'RMU', 'Ring Main Unit for medium voltage distribution', 'Switchgear', 'No.', 0, 0],
            ['MAT-SWG-005', 'LV Feeder Pillar', 'Low voltage feeder pillar', 'Switchgear', 'No.', 0, 0],

            // Earthing
            ['MAT-EAR-001', 'Earth Rod', 'Copper bonded earth rod', 'Earthing', 'No.', 0, 0],
            ['MAT-EAR-002', 'Earth Cable 16mm', '16mm² copper earth cable', 'Earthing', 'm', 0, 0],
            ['MAT-EAR-003', 'Earth Clamp', 'Earth rod clamp', 'Earthing', 'No.', 0, 0],
            ['MAT-EAR-004', 'Earth Inspection Pit', 'Earth inspection chamber', 'Earthing', 'No.', 0, 0],

            // Accessories
            ['MAT-ACC-001', 'Stay Wire', 'Galvanized stay wire', 'Line Accessories', 'm', 0, 0],
            ['MAT-ACC-002', 'Stay Rod', 'Stay rod with accessories', 'Line Accessories', 'No.', 0, 0],
            ['MAT-ACC-003', 'Shackle Insulator', 'LV shackle insulator', 'Line Accessories', 'No.', 0, 0],
            ['MAT-ACC-004', 'Pin Insulator', '11kV pin insulator', 'Line Accessories', 'No.', 0, 0],
            ['MAT-ACC-005', 'Cross Arm', 'Galvanized steel cross arm', 'Line Accessories', 'No.', 0, 0],
            ['MAT-ACC-006', 'U-Bolt', 'Galvanized U-bolt', 'Line Accessories', 'No.', 0, 0],
            ['MAT-ACC-007', 'Binding Wire', 'Aluminium binding wire', 'Line Accessories', 'kg', 0, 0],
            ['MAT-ACC-008', 'Cable Lug', 'Copper/aluminium cable lug', 'Cable Accessories', 'No.', 0, 0],
            ['MAT-ACC-009', 'Heat Shrink Kit', 'Heat shrink cable termination kit', 'Cable Accessories', 'Set', 0, 0],

            // Solar
            ['MAT-SOL-001', 'Solar Panel 550W', 'Monocrystalline solar PV module 550W', 'Solar', 'No.', 0, 0],
            ['MAT-SOL-002', 'Solar Inverter 5kW', '5kW hybrid solar inverter', 'Solar', 'No.', 0, 0],
            ['MAT-SOL-003', 'Solar Inverter 10kW', '10kW hybrid solar inverter', 'Solar', 'No.', 0, 0],
            ['MAT-SOL-004', 'Lithium Battery 5kWh', '5kWh lithium battery storage module', 'Solar', 'No.', 0, 0],
            ['MAT-SOL-005', 'PV Cable 6mm', '6mm² solar PV cable', 'Solar', 'm', 0, 0],
            ['MAT-SOL-006', 'DC Combiner Box', 'Solar DC combiner box', 'Solar', 'No.', 0, 0],
            ['MAT-SOL-007', 'AC Combiner Box', 'Solar AC combiner box', 'Solar', 'No.', 0, 0],

            // ICT
            ['MAT-ICT-001', 'Cat6 Cable', 'Cat6 UTP network cable', 'ICT', 'm', 0, 0],
            ['MAT-ICT-002', '24-Port Network Switch', '24-port gigabit network switch', 'ICT', 'No.', 0, 0],
            ['MAT-ICT-003', '42U Server Rack', '42U network/server rack', 'ICT', 'No.', 0, 0],
            ['MAT-ICT-004', 'Access Point', 'Wireless access point', 'ICT', 'No.', 0, 0],
            ['MAT-ICT-005', 'Patch Panel', '24-port patch panel', 'ICT', 'No.', 0, 0],
            ['MAT-ICT-006', 'RJ45 Connector', 'RJ45 network connector', 'ICT', 'No.', 0, 0],
            ['MAT-ICT-007', 'CCTV Camera', 'IP CCTV camera', 'Security', 'No.', 0, 0],
            ['MAT-ICT-008', 'NVR 16 Channel', '16-channel network video recorder', 'Security', 'No.', 0, 0],

            // Services
            ['SRV-001', 'Electrical Installation Labour', 'General electrical installation labour service', 'Services', 'Job', 0, 0],
            ['SRV-002', 'Transformer Installation Service', 'Transformer installation and commissioning service', 'Services', 'Job', 0, 0],
            ['SRV-003', 'Solar Installation Service', 'Solar system installation and commissioning service', 'Services', 'Job', 0, 0],
            ['SRV-004', 'Network Installation Service', 'Structured cabling and network installation service', 'Services', 'Job', 0, 0],
            ['SRV-005', 'Consultancy Service', 'Engineering or ICT consultancy service', 'Services', 'Job', 0, 0],
            ['SRV-006', 'Maintenance Service', 'Electrical/ICT maintenance service', 'Services', 'Job', 0, 0],
        ];

        foreach ($materials as $item) {
            Material::updateOrCreate(
                ['material_code' => $item[0]],
                [
                    'name' => $item[1],
                    'description' => $item[2],
                    'category' => $item[3],
                    'unit' => $item[4],
                    'standard_price' => $item[5],
                    'selling_price' => $item[6],
                    'active' => true,
                ]
            );
        }
    }
}