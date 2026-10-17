<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateProductsTable extends Migration
{
    public function up()
    {
        // create migration of the products table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true    
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
            'barcode' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false     
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'stock' => [
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

        $this->forge->addKey('id');
        $this->forge->createTable('products');
    }

    public function down()
    {
        // drop the products table
        $this->forge->dropTable('products');
    }
}
