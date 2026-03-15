<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSkttCoreTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'participant_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'birth_date' => [
                'type' => 'DATE',
            ],
            'work_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'raw_data' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'imported_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('participant_number', 'participants_unique_number');
        $this->forge->addKey('position', false, false, 'participants_idx_position');
        $this->forge->addKey('work_unit', false, false, 'participants_idx_work_unit');
        $this->forge->createTable('participants', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'admin_unit',
            ],
            'work_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username', 'admins_unique_username');
        $this->forge->createTable('admins', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'participant_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'first_scanned_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'first_scanned_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'scan_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'last_scanned_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_scanned_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('participant_id', 'attendance_unique_participant');
        $this->forge->createTable('attendance_logs', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'participant_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'admin_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'barcode_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'scanned_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('participant_id', false, false, 'scan_events_idx_participant');
        $this->forge->addKey('status', false, false, 'scan_events_idx_status');
        $this->forge->createTable('attendance_scan_events', true);

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

        $adminExists = $this->db->table('admins')->where('username', 'admin')->countAllResults();
        if ($adminExists === 0) {
            $this->db->table('admins')->insert([
                'username'      => 'admin',
                'password_hash' => password_hash('Admin123!', PASSWORD_DEFAULT),
                'role'          => 'super_admin',
                'work_unit'     => null,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('admin_login_logs', true);
        $this->forge->dropTable('attendance_scan_events', true);
        $this->forge->dropTable('attendance_logs', true);
        $this->forge->dropTable('admins', true);
        $this->forge->dropTable('participants', true);
    }
}
