<?php

use Phinx\Seed\AbstractSeed;

class DemoDataSeeder extends AbstractSeed
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // 清空业务数据（保留系统表）
        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $this->table('bom_material_substitute')->truncate();
        $this->table('bom_material')->truncate();
        $this->table('bom')->truncate();
        $this->table('material')->truncate();
        $this->table('material_category')->truncate();
        $this->table('unit')->truncate();
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        // ========== 计量单位 ==========
        $units = [];
        $unitNames = ['个', '千克', '克', '吨', '米', '厘米', '升', '毫升', '平方米', '件', '套', '箱'];
        foreach ($unitNames as $i => $name) {
            $units[] = [
                'id' => $i + 1,
                'name' => $name,
                'status' => 1,
                'sort' => $i,
                'created_at' => $now,
            ];
        }
        $this->table('unit')->insert($units)->save();

        // ========== 物料分类 ==========
        $categories = [
            ['id' => 1,  'code' => 'RM',     'name' => '原材料',  ],
            ['id' => 2,  'code' => 'ELEC',   'name' => '电子元件'],
            ['id' => 3,  'code' => 'METAL',  'name' => '五金件',  ],
            ['id' => 4,  'code' => 'PLASTIC','name' => '塑料件',  ],
            ['id' => 5,  'code' => 'PACK',   'name' => '包装材料'],
            ['id' => 6,  'code' => 'AUX',    'name' => '辅助材料'],
            ['id' => 7,  'code' => 'SEMI',   'name' => '半成品',  ],
            ['id' => 8,  'code' => 'FG',     'name' => '成品',    ],
            ['id' => 9,  'code' => 'PLATE',  'name' => '板材',    ],
            ['id' => 10, 'code' => 'HARNESS','name' => '线束',    ],
            ['id' => 11, 'code' => 'FAST',   'name' => '紧固件',  ],
        ];
        foreach ($categories as &$c) {
            $c['status'] = 1;
            $c['sort'] = 0;
            $c['created_at'] = $now;
        }
        $this->table('material_category')->insert($categories)->save();

        // ========== 物料 ==========
        $materials = [
            // 原材料
            ['id' => 1,  'code' => 'RM-001', 'name' => '镀锌钢板',       'spec' => '1.5mm×1000×2000',   'type' => 1, 'category_id' => 9,  'unit_id' => 1, 'sort' => 0],
            ['id' => 2,  'code' => 'RM-002', 'name' => '铜导线',         'spec' => '0.5mm²',            'type' => 1, 'category_id' => 10, 'unit_id' => 5, 'sort' => 1],
            ['id' => 3,  'code' => 'RM-003', 'name' => '六角螺栓',       'spec' => 'M6×20',             'type' => 1, 'category_id' => 11, 'unit_id' => 1, 'sort' => 2],
            ['id' => 4,  'code' => 'RM-004', 'name' => 'ABS塑料粒子',   'spec' => '通用级',            'type' => 1, 'category_id' => 4,  'unit_id' => 2, 'sort' => 3],
            ['id' => 5,  'code' => 'RM-005', 'name' => '焊锡丝',         'spec' => 'Ø0.8mm 500g/卷',    'type' => 1, 'category_id' => 6,  'unit_id' => 3, 'sort' => 4],
            ['id' => 6,  'code' => 'RM-006', 'name' => '瓦楞纸箱',       'spec' => '400×300×200mm',     'type' => 1, 'category_id' => 5,  'unit_id' => 1, 'sort' => 5],
            ['id' => 7,  'code' => 'RM-007', 'name' => '铝制散热器',     'spec' => '100×80×30mm',       'type' => 1, 'category_id' => 3,  'unit_id' => 1, 'sort' => 6],
            ['id' => 8,  'code' => 'RM-008', 'name' => '电解电容',       'spec' => '100μF/25V',         'type' => 1, 'category_id' => 2,  'unit_id' => 1, 'sort' => 7],
            ['id' => 15, 'code' => 'RM-009', 'name' => '不锈钢螺栓',     'spec' => 'M6×25',             'type' => 1, 'category_id' => 11, 'unit_id' => 1, 'sort' => 8],
            // 半成品
            ['id' => 9,  'code' => 'SF-001', 'name' => '机箱组件',       'spec' => '标准款 2U',         'type' => 2, 'category_id' => 7,  'unit_id' => 10, 'sort' => 10],
            ['id' => 10, 'code' => 'SF-002', 'name' => '电源组件',       'spec' => 'DC 24V 5A',         'type' => 2, 'category_id' => 7,  'unit_id' => 10, 'sort' => 11],
            ['id' => 11, 'code' => 'SF-003', 'name' => '线束总成',       'spec' => '20芯 1.5m',         'type' => 2, 'category_id' => 7,  'unit_id' => 10, 'sort' => 12],
            // 成品
            ['id' => 12, 'code' => 'FG-001', 'name' => '工业控制主机',   'spec' => 'IPC-610',           'type' => 3, 'category_id' => 8,  'unit_id' => 11, 'sort' => 20],
            ['id' => 13, 'code' => 'FG-002', 'name' => '信号检测仪',     'spec' => 'SD-2000',           'type' => 3, 'category_id' => 8,  'unit_id' => 10, 'sort' => 21],
            ['id' => 14, 'code' => 'FG-003', 'name' => '智能网关',       'spec' => 'GW-100',            'type' => 3, 'category_id' => 8,  'unit_id' => 11, 'sort' => 22],
        ];
        foreach ($materials as &$m) {
            $m['status'] = 1;
            $m['created_at'] = $now;
            $m['created_by'] = 1;
        }
        $this->table('material')->insert($materials)->save();

        // ========== BOM ==========
        $boms = [
            ['id' => 1, 'code' => 'BOM-SF-001', 'name' => '机箱组件BOM',   'material_id' => 9,  'quantity' => 1, 'is_default' => 1],
            ['id' => 2, 'code' => 'BOM-SF-002', 'name' => '电源组件BOM',   'material_id' => 10, 'quantity' => 1, 'is_default' => 1],
            ['id' => 3, 'code' => 'BOM-SF-003', 'name' => '线束总成BOM',   'material_id' => 11, 'quantity' => 1, 'is_default' => 1],
            ['id' => 4, 'code' => 'BOM-FG-001', 'name' => '工业控制主机BOM', 'material_id' => 12, 'quantity' => 1, 'is_default' => 1],
            ['id' => 5, 'code' => 'BOM-FG-002', 'name' => '信号检测仪BOM', 'material_id' => 13, 'quantity' => 1, 'is_default' => 1],
            ['id' => 6, 'code' => 'BOM-FG-003', 'name' => '智能网关BOM',   'material_id' => 14, 'quantity' => 1, 'is_default' => 1],
        ];
        foreach ($boms as &$b) {
            $b['status'] = 1;
            $b['sort'] = 0;
            $b['created_at'] = $now;
            $b['created_by'] = 1;
        }
        $this->table('bom')->insert($boms)->save();

        // ========== BOM 物料明细 ==========
        $bomMaterials = [
            // BOM1 机箱组件 → 原材料
            ['id' => 1,  'bom_id' => 1, 'material_id' => 1,  'quantity' => 0.5,  'loss_rate' => 2,  'sort' => 0],
            ['id' => 2,  'bom_id' => 1, 'material_id' => 4,  'quantity' => 0.2,  'loss_rate' => 1,  'sort' => 1],
            ['id' => 3,  'bom_id' => 1, 'material_id' => 3,  'quantity' => 8,    'loss_rate' => 0,  'sort' => 2],
            ['id' => 4,  'bom_id' => 1, 'material_id' => 7,  'quantity' => 1,    'loss_rate' => 0,  'sort' => 3],
            // BOM2 电源组件 → 原材料
            ['id' => 5,  'bom_id' => 2, 'material_id' => 8,  'quantity' => 5,    'loss_rate' => 1,  'sort' => 0],
            ['id' => 6,  'bom_id' => 2, 'material_id' => 5,  'quantity' => 10,   'loss_rate' => 2,  'sort' => 1],
            ['id' => 7,  'bom_id' => 2, 'material_id' => 3,  'quantity' => 4,    'loss_rate' => 0,  'sort' => 2],
            // BOM3 线束总成 → 原材料
            ['id' => 8,  'bom_id' => 3, 'material_id' => 2,  'quantity' => 2,    'loss_rate' => 3,  'sort' => 0],
            ['id' => 9,  'bom_id' => 3, 'material_id' => 5,  'quantity' => 5,    'loss_rate' => 1,  'sort' => 1],
            // BOM4 工业控制主机 → 半成品 + 原材料
            ['id' => 10, 'bom_id' => 4, 'material_id' => 9,  'quantity' => 1,    'loss_rate' => 0,  'child_bom_id' => 1, 'sort' => 0],
            ['id' => 11, 'bom_id' => 4, 'material_id' => 10, 'quantity' => 1,    'loss_rate' => 0,  'child_bom_id' => 2, 'sort' => 1],
            ['id' => 12, 'bom_id' => 4, 'material_id' => 11, 'quantity' => 1,    'loss_rate' => 0,  'child_bom_id' => 3, 'sort' => 2],
            ['id' => 13, 'bom_id' => 4, 'material_id' => 3,  'quantity' => 12,   'loss_rate' => 0,  'sort' => 3],
            // BOM5 信号检测仪 → 半成品 + 原材料
            ['id' => 14, 'bom_id' => 5, 'material_id' => 10, 'quantity' => 1,    'loss_rate' => 0,  'child_bom_id' => 2, 'sort' => 0],
            ['id' => 15, 'bom_id' => 5, 'material_id' => 8,  'quantity' => 10,   'loss_rate' => 2,  'sort' => 1],
            ['id' => 16, 'bom_id' => 5, 'material_id' => 5,  'quantity' => 15,   'loss_rate' => 1,  'sort' => 2],
            ['id' => 17, 'bom_id' => 5, 'material_id' => 3,  'quantity' => 8,    'loss_rate' => 0,  'sort' => 3],
            // BOM6 智能网关 → 半成品 + 原材料
            ['id' => 18, 'bom_id' => 6, 'material_id' => 11, 'quantity' => 1,    'loss_rate' => 0,  'child_bom_id' => 3, 'sort' => 0],
            ['id' => 19, 'bom_id' => 6, 'material_id' => 4,  'quantity' => 0.5,  'loss_rate' => 2,  'sort' => 1],
            ['id' => 20, 'bom_id' => 6, 'material_id' => 8,  'quantity' => 3,    'loss_rate' => 0,  'sort' => 2],
        ];
        foreach ($bomMaterials as &$bm) {
            $bm['remark'] = null;
        }
        $this->table('bom_material')->insert($bomMaterials)->save();

        // ========== 替代料 ==========
        $substitutes = [
            // 机箱组件中的 M6螺栓 → 不锈钢螺栓替代
            ['bom_material_id' => 3,  'material_id' => 15, 'priority' => 1],
            // 机箱组件中的 ABS粒子 → 散热器替代（演示）
            ['bom_material_id' => 2,  'material_id' => 7,  'priority' => 1],
            // 工业控制主机中的 M6螺栓 → 不锈钢螺栓替代
            ['bom_material_id' => 13, 'material_id' => 15, 'priority' => 1],
            // 信号检测仪中的 M6螺栓 → 不锈钢螺栓替代
            ['bom_material_id' => 17, 'material_id' => 15, 'priority' => 1],
        ];
        foreach ($substitutes as &$s) {
            $s['remark'] = null;
        }
        $this->table('bom_material_substitute')->insert($substitutes)->save();

        echo "Demo data seeded successfully!\n";
        echo "  - " . count($units) . " units\n";
        echo "  - " . count($categories) . " categories\n";
        echo "  - " . count($materials) . " materials\n";
        echo "  - " . count($boms) . " BOMs\n";
        echo "  - " . count($bomMaterials) . " BOM material items\n";
        echo "  - " . count($substitutes) . " substitutes\n";
    }
}
