<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateProductLogsTable extends Migration
{
    public function up()
    {
        // create table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true    
            ],
            'type_id' => [
                'type' => 'TINYINT',
                'unsigned' => true,
                'null' => false
            ],
            'product_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => false
            ],
            'supplier_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => false
            ],
            'brand_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => false
            ],
            'measuring_unit_id' => [
                'type' => 'SMALLINT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => false
            ],
            'group_uid' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false  
            ],
            'quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0
            ],
            'buying_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0
            ],
            'selling_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0
            ],
            'wholesale_discount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0
            ],
            'reorder_quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0
            ],
            'old_stock' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0
            ],
            'new_stock' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
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
        $this->forge->createTable('product_logs');

         
    }

    public function down()
    {
        // drop table
        $this->forge->dropTable('product_logs');
    }
}
