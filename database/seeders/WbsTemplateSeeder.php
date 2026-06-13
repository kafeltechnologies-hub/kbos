<?php

namespace Database\Seeders;

use App\Models\WbsTemplate;
use Illuminate\Database\Seeder;

class WbsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['electrical_distribution', '1.0', 'Project Initiation', 'Client engagement, site visit, project approval and documentation'],
            ['electrical_distribution', '1.1', 'Site Survey', 'Survey route, load points, poles, transformer position and constraints'],
            ['electrical_distribution', '1.2', 'Engineering Design', 'Prepare electrical design, SLD, route layout and material schedule'],
            ['electrical_distribution', '1.3', 'Permits and Approvals', 'Obtain permits, wayleave, client approvals and safety clearance'],
            ['electrical_distribution', '2.0', 'Procurement', 'Procure poles, conductors, cables, transformers, accessories and PPE'],
            ['electrical_distribution', '3.0', 'Civil Works', 'Pole spotting, excavation, foundation works and site preparation'],
            ['electrical_distribution', '4.0', 'Electrical Installation', 'String conductors, install insulators, crossarms and fittings'],
            ['electrical_distribution', '4.1', 'Transformer Installation', 'Install transformer, protection devices and earthing'],
            ['electrical_distribution', '5.0', 'Testing and Commissioning', 'Continuity, insulation resistance, earth resistance and voltage checks'],
            ['electrical_distribution', '5.1', 'Client Handover', 'Inspection, snag correction, documentation and handover'],

            ['renewable_energy', '1.0', 'Solar Site Assessment', 'Assess load profile, space, shading and existing wiring'],
            ['renewable_energy', '1.1', 'System Design', 'Size PV array, inverter, batteries, protection and cable schedule'],
            ['renewable_energy', '2.0', 'Procurement', 'Procure panels, inverter, batteries, mounting system and protection'],
            ['renewable_energy', '3.0', 'Installation', 'Install mounting system, panels, inverter, batteries and protection'],
            ['renewable_energy', '4.0', 'Testing and Commissioning', 'Test polarity, insulation, charging, backup and changeover'],
            ['renewable_energy', '5.0', 'Client Handover', 'Training, manuals, warranties and documentation'],

            ['ict_infrastructure', '1.0', 'ICT Site Survey', 'Assess network points, rack location and cable routes'],
            ['ict_infrastructure', '1.1', 'Network Design', 'Prepare LAN design, IP plan, rack layout and equipment list'],
            ['ict_infrastructure', '2.0', 'Procurement', 'Procure cables, switches, rack, patch panels and accessories'],
            ['ict_infrastructure', '3.0', 'Structured Cabling', 'Install trunking, conduits, Cat6 cables and faceplates'],
            ['ict_infrastructure', '4.0', 'Configuration', 'Configure switches, routers, VLANs, Wi-Fi and security'],
            ['ict_infrastructure', '5.0', 'Testing and Documentation', 'Test points, label cables, document IPs and hand over'],
        ];

        foreach ($items as $index => $item) {
            WbsTemplate::updateOrCreate(
                [
                    'project_type' => $item[0],
                    'wbs_code' => $item[1],
                ],
                [
                    'title' => $item[2],
                    'description' => $item[3],
                    'sort_order' => $index + 1,
                    'active' => true,
                ]
            );
        }
    }
}