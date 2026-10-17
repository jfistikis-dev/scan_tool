<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateSuppliersTable extends Migration
{
    public function up()
    {
        // Create table for suppliers
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,  
                'unsigned' => true,
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],
            'sort' => [
                'type' => 'INT',
                'constraint' => 5,  
                'unsigned' => true,
                'null' => false,
                'default' => 0
            ],
             'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ]
            
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('suppliers', true);
            
        
    }

    public function down()
    {
        //
        $this->forge->dropTable('suppliers');
    }
}
