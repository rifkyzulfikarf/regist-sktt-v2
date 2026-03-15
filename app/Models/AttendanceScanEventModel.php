<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceScanEventModel extends Model
{
    protected $table         = 'attendance_scan_events';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    public    $useTimestamps = false;
    protected $allowedFields = [
        'participant_id',
        'admin_id',
        'barcode_value',
        'status',
        'message',
        'scanned_at',
    ];
}
