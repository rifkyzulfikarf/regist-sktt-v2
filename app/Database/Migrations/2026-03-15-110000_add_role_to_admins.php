<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleToAdmins extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('role', 'admins')) {
            $this->forge->addColumn('admins', [
                'role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => false,
                    'default'    => 'admin_unit',
                    'after'      => 'password_hash',
                ],
            ]);
        }

        $this->db->table('admins')->set(['role' => 'admin_unit'])->where('role IS NULL')->orWhere('role', '')->update();
        $this->db->table('admins')->set(['role' => 'super_admin'])->where('username', 'admin')->update();
    }

    public function down()
    {
        if ($this->db->fieldExists('role', 'admins')) {
            $this->forge->dropColumn('admins', 'role');
        }
    }
}
