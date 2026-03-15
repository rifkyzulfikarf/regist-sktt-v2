<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminLoginLogsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('admin_login_logs')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'admin_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'admin_role' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'login_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('admin_id', false, false, 'admin_login_logs_idx_admin');
        $this->forge->addKey('login_at', false, false, 'admin_login_logs_idx_time');
        $this->forge->createTable('admin_login_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('admin_login_logs', true);
    }
}
