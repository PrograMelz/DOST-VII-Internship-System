<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Previous cast stored UTC instants in the datetime column (e.g. 08:00 Manila → 00:00 UTC).
     * Convert each row to Asia/Manila wall-clock so DB values match chosen 24h times.
     */
    public function up(): void
    {
        if (! Schema::hasTable('attendance_logs')) {
            return;
        }

        $tz = config('app.timezone', 'Asia/Manila');

        DB::table('attendance_logs')->orderBy('id')->chunkById(200, function ($rows) use ($tz): void {
            foreach ($rows as $row) {
                /** @var object{id: int|string, scan_time: string|null} $row */
                if ($row->scan_time === null) {
                    continue;
                }
                $local = Carbon::parse($row->scan_time, 'UTC')->timezone($tz);

                DB::table('attendance_logs')->where('id', $row->id)->update([
                    'scan_time' => $local->format('Y-m-d H:i:s'),
                ]);
            }
        });
    }

    /**
     * Best-effort reverse: treat stored values as Manila wall clock, write UTC instant string.
     */
    public function down(): void
    {
        if (! Schema::hasTable('attendance_logs')) {
            return;
        }

        $tz = config('app.timezone', 'Asia/Manila');

        DB::table('attendance_logs')->orderBy('id')->chunkById(200, function ($rows) use ($tz): void {
            foreach ($rows as $row) {
                if ($row->scan_time === null) {
                    continue;
                }
                $utc = Carbon::parse($row->scan_time, $tz)->timezone('UTC');

                DB::table('attendance_logs')->where('id', $row->id)->update([
                    'scan_time' => $utc->format('Y-m-d H:i:s'),
                ]);
            }
        });
    }
};
