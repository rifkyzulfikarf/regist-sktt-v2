<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceLogModel extends Model
{
    protected $table         = 'attendance_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'participant_id',
        'first_scanned_at',
        'first_scanned_by',
        'scan_count',
        'last_scanned_at',
        'last_scanned_by',
    ];
}
