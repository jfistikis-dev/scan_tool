<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateBrandsTable extends Migration
{
    public function up()
    {
        //create migration of the brands table
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
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
        

        // add foreign key constraint
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('brands');}

    public function down()
    {
        // drop the table
        $this->forge->dropTable('brands');
    }
}
