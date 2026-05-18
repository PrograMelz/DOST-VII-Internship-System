<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'intern_id',
        'scan_time',
        'scan_type',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            /** Stored as Asia/Manila wall clock (matches app timezone); same instant user selects in UI / QR scanner. */
            'scan_time' => 'datetime',
        ];
    }

    /**
     * Get the intern that this attendance log belongs to
     */
    public function intern(): BelongsTo
    {
        return $this->belongsTo(Intern::class);
    }

    /**
     * Get attendance logs for a specific intern, year, and month.
     *
     * @param int $internId
     * @param int $year
     * @param int $month
     * @return array<array<string, mixed>>
     */
    public static function getLogsForMonth(int $internId, int $year, int $month): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('intern_id', $internId)
            ->whereYear('scan_time', $year)
            ->whereMonth('scan_time', $month)
            ->orderBy('scan_time')
            ->get();
    }
}

