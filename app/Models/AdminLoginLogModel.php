<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminLoginLogModel extends Model
{
    protected $table         = 'admin_login_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    public    $useTimestamps = false;
    protected $allowedFields = [
        'admin_id',
        'admin_role',
        'ip_address',
        'user_agent',
        'status',
        'message',
        'login_at',
    ];
}
