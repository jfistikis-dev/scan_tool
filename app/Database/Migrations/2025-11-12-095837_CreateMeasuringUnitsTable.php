<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMeasuringUnitsTable extends Migration
{
    public function up()
    {
        //Create table for measuring units
        $this->forge->addField([
            'id' => [
                'type' => 'SMALLINT',
                'constraint' => 2,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'sort' => [
                'type' => 'SMALLINT',
                'null' => true,
                'default' => 0
            ]
            ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('measuring_units');
    }

    public function down()
    {
        // Drop table for measuring units
        $this->forge->dropTable('measuring_units');

    }
}
