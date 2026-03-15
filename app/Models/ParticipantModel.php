<?php

namespace App\Models;

use CodeIgniter\Model;

class ParticipantModel extends Model
{
    protected $table            = 'participants';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'participant_number',
        'full_name',
        'position',
        'birth_date',
        'work_unit',
        'raw_data',
        'imported_at',
    ];

    public function getDistinctPositions(): array
    {
        return $this->distinct()->select('position')->orderBy('position', 'ASC')->findAll();
    }

    public function findVerified(string $participantNumber, string $position, string $birthDate): ?array
    {
        return $this->where('participant_number', trim($participantNumber))
            ->where('position', trim($position))
            ->where('birth_date', $birthDate)
            ->first();
    }

    public function findByParticipantNumber(string $participantNumber): ?array
    {
        return $this->where('participant_number', trim($participantNumber))->first();
    }

    public function findByBarcodeMd5(string $barcodeHash): ?array
    {
        return $this->where('MD5(participant_number)', strtolower(trim($barcodeHash)))->first();
    }

    public function getReportRows(?string $workUnit = null, ?string $position = null, string $status = 'all'): array
    {
        $builder = $this->db->table($this->table . ' p')
            ->select('p.*, a.first_scanned_at, a.scan_count')
            ->join('attendance_logs a', 'a.participant_id = p.id', 'left')
            ->orderBy('p.participant_number', 'ASC');

        if ($workUnit !== null && $workUnit !== '') {
            $builder->where('p.work_unit', $workUnit);
        }

        if ($position !== null && $position !== '') {
            $builder->where('p.position', $position);
        }

        if ($status === 'hadir') {
            $builder->where('a.first_scanned_at IS NOT NULL');
        } elseif ($status === 'tidak_hadir') {
            $builder->where('a.first_scanned_at IS NULL');
        }

        return $builder->get()->getResultArray();
    }

    public function getDistinctWorkUnits(): array
    {
        return $this->distinct()->select('work_unit')->where('work_unit IS NOT NULL')->orderBy('work_unit', 'ASC')->findAll();
    }
}
