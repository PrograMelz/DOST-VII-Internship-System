<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Intern;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QrAttendanceController extends Controller
{
    public function index()
    {
        return view('qr_attendance');
    }

    public function latest(): JsonResponse
    {
        return response()->json([
            'data' => $this->latestScans(),
        ]);
    }

    public function scan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_code' => ['required', 'string', 'max:20'],
            'pin' => ['nullable', 'string', 'size:4'],
        ]);

        $qrCode = $validated['qr_code'];
        $pin = $validated['pin'] ?? null;

        $intern = Intern::query()
            ->where('qr_code', $qrCode)
            ->first();

        if (!$intern) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid QR code.',
                'data' => $this->latestScans(),
            ], 422);
        }

        if ($intern->att_code !== null) {
            if ($pin === null) {
                return response()->json([
                    'ok' => false,
                    'requires_pin' => true,
                    'message' => 'Attendance PIN required.',
                    'intern' => [
                        'id' => $intern->id,
                        'name' => $intern->full_name,
                    ],
                ]);
            }

            if (!hash_equals($intern->att_code, $pin)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Invalid attendance PIN.',
                ], 422);
            }
        }

        $now = CarbonImmutable::now();
        $today = $now->toDateString();

        return DB::transaction(function () use ($intern, $now, $today) {
            $todayLogs = AttendanceLog::query()
                ->where('intern_id', $intern->id)
                ->whereDate('scan_time', $today)
                ->orderByDesc('scan_time')
                ->lockForUpdate()
                ->get();

            $lastToday = $todayLogs->first();

            if ($lastToday && $lastToday->scan_time && $lastToday->scan_time->diffInSeconds($now) < 10) {
                return response()->json([
                    'ok' => true,
                    'ignored' => true,
                    'message' => 'Duplicate scan ignored.',
                    'intern' => [
                        'id' => $intern->id,
                        'name' => $intern->full_name,
                    ],
                    'data' => $this->latestScans(),
                ]);
            }

            $currentIsMorning = $now->hour < 12 || ($now->hour == 12 && $now->minute < 30);

            $morningLogs = $todayLogs->filter(fn($log) => $log->scan_time->format('H:i') < '12:30');
            $afternoonLogs = $todayLogs->filter(fn($log) => $log->scan_time->format('H:i') >= '12:30');

            $morningIn = $morningLogs->firstWhere('scan_type', 'IN');
            $morningOut = $morningLogs->firstWhere('scan_type', 'OUT');

            if (!$currentIsMorning && $morningIn && !$morningOut && $afternoonLogs->isEmpty()) {
                $nextType = 'IN';
                $period = 'afternoon';
                $lastToday = null;
            } else {
                if (!$lastToday) {
                    $nextType = 'IN';
                } else {
                    $nextType = $lastToday->scan_type === 'IN' ? 'OUT' : 'IN';
                }

                if ($currentIsMorning) {
                    if ($morningLogs->count() >= 2) {
                        return response()->json([
                            'ok' => false,
                            'message' => 'Already scanned twice in morning. Next scan available in afternoon.',
                            'data' => $this->latestScans(),
                        ], 422);
                    }

                    $period = 'morning';
                } else {
                    if ($afternoonLogs->count() >= 2) {
                        return response()->json([
                            'ok' => false,
                            'message' => 'Already completed afternoon scans.',
                            'data' => $this->latestScans(),
                        ], 422);
                    }

                    $period = 'afternoon';
                }
            }

            if ($nextType === 'OUT' && $lastToday && $lastToday->scan_type === 'IN') {
                $timeSinceIn = $lastToday->scan_time->diffInMinutes($now);

                if ($timeSinceIn < 30) {
                    $inTime = $lastToday->scan_time->format('H:i');

                    return response()->json([
                        'ok' => false,
                        'message' => "You time in {$inTime}, its too early for time out.",
                        'data' => $this->latestScans(),
                    ], 422);
                }
            }

            if ($lastToday && $lastToday->scan_type === $nextType) {
                return response()->json([
                    'ok' => false,
                    'message' => "Duplicate {$nextType} scan.",
                    'data' => $this->latestScans(),
                ], 422);
            }

            AttendanceLog::query()->create([
                'intern_id' => $intern->id,
                'scan_time' => $now,
                'scan_type' => $nextType,
            ]);

            return response()->json([
                'ok' => true,
                'ignored' => false,
                'message' => "Recorded: {$nextType} ({$period})",
                'intern' => [
                    'id' => $intern->id,
                    'name' => $intern->full_name,
                ],
                'scan_type' => $nextType,
                'scan_time' => $now->toDateTimeString(),
                'data' => $this->latestScans(),
            ]);
        });
    }

    private function latestScans(): array
    {
        return AttendanceLog::query()
            ->with(['intern:id,first_name,middle_name,last_name'])
            ->orderByDesc('scan_time')
            ->limit(8)
            ->get()
            ->map(function (AttendanceLog $log): array {
                return [
                    'id' => $log->id,
                    'intern_id' => $log->intern_id,
                    'name' => $log->intern?->full_name ?? 'Unknown',
                    'scan_type' => $log->scan_type,
                    'scan_time' => optional($log->scan_time)->toDateTimeString(),
                ];
            })
            ->all();
    }
}