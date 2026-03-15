<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table         = 'admins';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['username', 'password_hash', 'work_unit'];

    public function verifyLogin(string $username, string $password): ?array
    {
        $admin = $this->where('username', $username)->first();

        if (! $admin) {
            return null;
        }

        if (! password_verify($password, $admin['password_hash'])) {
            return null;
        }

        return $admin;
    }
}
