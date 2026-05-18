<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateInternCalendarDayRequest;
use App\Mail\EmailChangeVerificationMail;
use App\Mail\PasswordResetCodeMail;
use App\Models\AttendanceLog;
use App\Models\Holiday;
use App\Models\Intern;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InternController extends Controller
{
    private function getSessionIntern(): ?Intern
    {
        $internId = Session::get('intern_id');
        if (!$internId) {
            return null;
        }

        return Intern::query()->find($internId);
    }

    public function showLoginForm()
    {
        return view('login.intern');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $intern = Intern::where('username', $request->username)->first();

        if ($intern && Hash::check($request->password, $intern->password_hash)) {
            // Store intern in session
            Session::put('intern_id', $intern->id);
            return redirect()->route('intern.dashboard');
        }

        return back()->withErrors(['login' => 'Invalid credentials']);
    }

    public function showForgotPasswordForm()
    {
        return view('login.forgot-password');
    }

    public function sendPasswordResetCode(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $email = $request->input('email');
        $intern = Intern::where('email', $email)->first();

        if (! $intern) {
            return response()->json([
                'message' => 'No account found with this email address.',
                'errors' => ['email' => ['No account found with this email address.']],
            ], 422);
        }

        $code = (string) random_int(100000, 999999);
        $cacheKey = 'password_reset_'.$email;
        Cache::put($cacheKey, [
            'intern_id' => $intern->id,
            'code' => $code,
        ], now()->addMinutes(15));

        $fromName = SystemSetting::first()?->system_short_name ?? config('mail.from.name');

        try {
            Mail::to($email)->send(new PasswordResetCodeMail($code, $intern->full_name, $fromName));
        } catch (\Throwable $e) {
            Cache::forget($cacheKey);

            return response()->json([
                'message' => 'Failed to send reset code. Please try again later.',
            ], 502);
        }

        return response()->json([
            'sent' => true,
            'message' => 'A 6-digit code has been sent to your email. Enter it below to set a new password.',
        ]);
    }

    public function resetPasswordWithCode(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = $request->input('email');
        $cacheKey = 'password_reset_'.$email;
        $pending = Cache::get($cacheKey);

        if (! $pending || ! is_array($pending)) {
            return response()->json([
                'message' => 'Reset code expired or invalid. Please request a new code.',
                'errors' => ['code' => ['Reset code expired or invalid.']],
            ], 422);
        }

        $enteredCode = $request->input('code');
        if (! hash_equals($pending['code'], $enteredCode)) {
            return response()->json([
                'message' => 'Invalid code. Please check and try again.',
                'errors' => ['code' => ['Invalid code.']],
            ], 422);
        }

        $intern = Intern::find($pending['intern_id']);
        if (! $intern) {
            Cache::forget($cacheKey);

            return response()->json([
                'message' => 'Invalid reset request. Please start over.',
            ], 422);
        }

        $intern->password_hash = Hash::make($request->input('password'));
        $intern->save();
        Cache::forget($cacheKey);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password updated. You can now log in with your new password.',
                'redirect' => route('intern.login'),
            ]);
        }

        return redirect()->route('intern.login')->with('success', 'Password updated. You can now log in with your new password.');
    }

    public function dashboard()
    {
        $intern = $this->getSessionIntern();
        if (!$intern) {
            return redirect()->route('intern.login');
        }

        // Get attendance records (latest first for convenience)
        $attendanceLogs = $intern->attendanceLogs()
            ->orderBy('scan_time', 'desc')
            ->get();

        // Compute total rendered minutes based only on valid IN/OUT pairs
        // Guard against any unexpected negative values by clamping at zero.
        $totalRenderedMinutes = max(0, $this->calculateRenderedMinutes($attendanceLogs));

        // OJT requirement (stored as hours on intern)
        $requiredHours = $intern->ojt_hours_required !== null
            ? (int) $intern->ojt_hours_required
            : null;

        $requiredMinutesTotal = $requiredHours !== null
            ? $requiredHours * 60
            : null;

        $remainingMinutesTotal = $requiredMinutesTotal !== null
            ? max(0, $requiredMinutesTotal - $totalRenderedMinutes)
            : null;

        $renderedHours = intdiv($totalRenderedMinutes, 60);
        $renderedMinutesPart = $totalRenderedMinutes % 60;

        $remainingHours = $remainingMinutesTotal !== null
            ? intdiv($remainingMinutesTotal, 60)
            : null;

        $remainingMinutesPart = $remainingMinutesTotal !== null
            ? $remainingMinutesTotal % 60
            : null;

        $progressPercent = $requiredMinutesTotal && $requiredMinutesTotal > 0
            ? min(100.0, round(($totalRenderedMinutes / $requiredMinutesTotal) * 100, 1))
            : null;

        // Estimate remaining time: 8 hours per day, weekdays only (no Sat/Sun), excluding admin-set holidays.
        $hoursPerDay = 8;
        $workingDaysLeft = $remainingMinutesTotal !== null && $hoursPerDay > 0
            ? (int) ceil(($remainingMinutesTotal / 60) / $hoursPerDay)
            : null;

        $estimate = $workingDaysLeft !== null && $workingDaysLeft > 0
            ? $this->computeExpectedFinishDate($workingDaysLeft)
            : null;

        $estimatedDaysLeft = $estimate['calendarDays'] ?? null;
        $estimatedMonthsLeft = $estimatedDaysLeft !== null
            ? (int) max(1, round($estimatedDaysLeft / 30))
            : null;
        $expectedFinishDate = $estimate['expectedFinishDate'] ?? null;

        // Still prepare grouped attendance if the view needs a quick recent breakdown
        $dailyAttendance = $attendanceLogs->groupBy(function ($log) {
            return $log->scan_time->format('Y-m-d');
        });

        $weeklyAttendance = $attendanceLogs->groupBy(function ($log) {
            return $log->scan_time->format('Y-W');
        });

        $monthlyAttendance = $attendanceLogs->groupBy(function ($log) {
            return $log->scan_time->format('Y-m');
        });

        return view('interns.dashboard', [
            'intern' => $intern,
            'dailyAttendance' => $dailyAttendance,
            'weeklyAttendance' => $weeklyAttendance,
            'monthlyAttendance' => $monthlyAttendance,
            'totalRenderedMinutes' => $totalRenderedMinutes,
            'renderedHours' => $renderedHours,
            'renderedMinutesPart' => $renderedMinutesPart,
            'requiredHours' => $requiredHours,
            'remainingMinutesTotal' => $remainingMinutesTotal,
            'remainingHours' => $remainingHours,
            'remainingMinutesPart' => $remainingMinutesPart,
            'progressPercent' => $progressPercent,
            'estimatedDaysLeft' => $estimatedDaysLeft,
            'estimatedMonthsLeft' => $estimatedMonthsLeft,
            'expectedFinishDate' => $expectedFinishDate,
        ]);
    }

    /**
     * Compute expected OJT finish date by counting only weekdays (Mon–Fri) that are not holidays.
     *
     * @return array{expectedFinishDate: \Carbon\Carbon, calendarDays: int}
     */
    private function computeExpectedFinishDate(int $workingDaysNeeded): array
    {
        $today = Carbon::today();
        $endWindow = $today->copy()->addDays(730);
        $holidayDates = Holiday::query()
            ->whereBetween('holiday_date', [$today, $endWindow])
            ->pluck('holiday_date')
            ->map(fn (\DateTimeInterface $d) => $d->format('Y-m-d'))
            ->flip()
            ->all();

        $current = $today->copy();
        $count = 0;
        while ($count < $workingDaysNeeded) {
            if ($current->isWeekday() && ! isset($holidayDates[$current->format('Y-m-d')])) {
                $count++;
            }
            if ($count >= $workingDaysNeeded) {
                break;
            }
            $current->addDay();
        }

        return [
            'expectedFinishDate' => $current->copy(),
            'calendarDays' => (int) $today->diffInDays($current, false),
        ];
    }

    /**
     * Calculate total rendered minutes based on valid IN/OUT pairs only.
     *
     * Any IN without a corresponding later OUT is ignored for that day,
     * so partial sessions (missing time-out) are not counted.
     *
     * @param \Illuminate\Support\Collection<int, \App\Models\AttendanceLog> $attendanceLogs
     */
    private function calculateRenderedMinutes($attendanceLogs): int
    {
        if ($attendanceLogs->isEmpty()) {
            return 0;
        }

        $minutes = 0;

        // Work in chronological order and group by calendar date
        $groupedByDate = $attendanceLogs
            ->sortBy('scan_time')
            ->groupBy(static function ($log) {
                return $log->scan_time->format('Y-m-d');
            });

        foreach ($groupedByDate as $logsForDay) {
            $minutes += $this->calculateRenderedMinutesForDay($logsForDay);
        }

        return $minutes;
    }

    /**
     * Calculate rendered minutes for a single day's logs, based on valid IN/OUT pairs only.
     *
     * @param \Illuminate\Support\Collection<int, \App\Models\AttendanceLog> $logsForDay
     */
    private function calculateRenderedMinutesForDay($logsForDay): int
    {
        if ($logsForDay->isEmpty()) {
            return 0;
        }

        $minutes = 0;
        $currentIn = null;

        foreach ($logsForDay->sortBy('scan_time') as $log) {
            $scanTime = $log->scan_time;
            $type = strtoupper(trim((string) $log->scan_type));

            if ($type === 'IN') {
                // Start (or restart) an active session
                $currentIn = $scanTime;
                continue;
            }

            if ($type === 'OUT' && $currentIn !== null) {
                $inTs = $currentIn->getTimestamp();
                $outTs = $scanTime->getTimestamp();

                if ($outTs > $inTs) {
                    $minutes += (int) floor(($outTs - $inTs) / 60);
                }

                // Close the session; any next IN will start a new one
                $currentIn = null;
            }
        }

        // If the day ends with an IN and no OUT, that partial session is discarded

        return $minutes;
    }

    /**
     * Map chronological IN/OUT pairs to the four editable slots (24h H:i in Asia/Manila).
     *
     * @param \Illuminate\Support\Collection<int, \App\Models\AttendanceLog> $logsForDay
     * @return array{am_in: string|null, am_out: string|null, pm_in: string|null, pm_out: string|null}
     */
    private function getCalendarDayEditTimes($logsForDay): array
    {
        $defaults = [
            'am_in' => null,
            'am_out' => null,
            'pm_in' => null,
            'pm_out' => null,
        ];

        if ($logsForDay->isEmpty()) {
            return $defaults;
        }

        $tz = 'Asia/Manila';
        $sorted = $logsForDay->sortBy('scan_time')->values();
        $pairs = [];
        $currentIn = null;

        foreach ($sorted as $log) {
            $type = strtoupper(trim((string) ($log->scan_type ?? '')));
            $t = $log->scan_time->copy()->timezone($tz);
            if ($type === 'IN') {
                $currentIn = $t;
            } elseif ($type === 'OUT' && $currentIn !== null) {
                $pairs[] = [$currentIn, $t];
                $currentIn = null;
            }
        }

        if ($currentIn !== null) {
            $pairs[] = [$currentIn, null];
        }

        if (count($pairs) === 0) {
            return $defaults;
        }

        if (count($pairs) === 1 && $pairs[0][1] === null) {
            $h = (int) $pairs[0][0]->format('H');
            if ($h < 12) {
                $defaults['am_in'] = $pairs[0][0]->format('H:i');
            } else {
                $defaults['pm_in'] = $pairs[0][0]->format('H:i');
            }

            return $defaults;
        }

        if (count($pairs) === 1) {
            $firstIn = $pairs[0][0];
            if ((int) $firstIn->format('H') < 12) {
                $defaults['am_in'] = $pairs[0][0]->format('H:i');
                $defaults['am_out'] = $pairs[0][1]->format('H:i');
            } else {
                $defaults['pm_in'] = $pairs[0][0]->format('H:i');
                $defaults['pm_out'] = $pairs[0][1]->format('H:i');
            }

            return $defaults;
        }

        $defaults['am_in'] = $pairs[0][0]->format('H:i');
        $defaults['am_out'] = $pairs[0][1] ? $pairs[0][1]->format('H:i') : null;
        $defaults['pm_in'] = $pairs[1][0]->format('H:i');
        $defaults['pm_out'] = $pairs[1][1] ? $pairs[1][1]->format('H:i') : null;

        return $defaults;
    }

    /**
     * Build AM/PM attendance detail for the calendar modal (display + minutes).
     *
     * @param \Illuminate\Support\Collection<int, \App\Models\AttendanceLog> $logsForDay
     * @return array{am_in: string|null, am_out: string|null, pm_in: string|null, pm_out: string|null, am_minutes: int, pm_minutes: int, total_minutes: int, edit: array<string, string|null>}
     */
    private function buildAttendanceDayDetail($logsForDay): array
    {
        $edit = $this->getCalendarDayEditTimes($logsForDay);

        $fmt = function (?string $h24): ?string {
            if ($h24 === null || $h24 === '') {
                return null;
            }

            return Carbon::createFromFormat('H:i', $h24, 'Asia/Manila')->format('g:i A');
        };

        $amMin = 0;
        $pmMin = 0;

        if ($edit['am_in'] && $edit['am_out']) {
            $t1 = Carbon::createFromFormat('H:i', $edit['am_in'], 'Asia/Manila');
            $t2 = Carbon::createFromFormat('H:i', $edit['am_out'], 'Asia/Manila');
            if ($t2->gt($t1)) {
                $amMin = (int) floor(($t2->getTimestamp() - $t1->getTimestamp()) / 60);
            }
        }

        if ($edit['pm_in'] && $edit['pm_out']) {
            $t1 = Carbon::createFromFormat('H:i', $edit['pm_in'], 'Asia/Manila');
            $t2 = Carbon::createFromFormat('H:i', $edit['pm_out'], 'Asia/Manila');
            if ($t2->gt($t1)) {
                $pmMin = (int) floor(($t2->getTimestamp() - $t1->getTimestamp()) / 60);
            }
        }

        return [
            'am_in' => $fmt($edit['am_in']),
            'am_out' => $fmt($edit['am_out']),
            'pm_in' => $fmt($edit['pm_in']),
            'pm_out' => $fmt($edit['pm_out']),
            'am_minutes' => $amMin,
            'pm_minutes' => $pmMin,
            'total_minutes' => $amMin + $pmMin,
            'edit' => $edit,
        ];
    }

    public function updateCalendarDay(UpdateInternCalendarDayRequest $request): \Illuminate\Http\JsonResponse
    {
        $intern = $this->getSessionIntern();
        if (! $intern) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validated();
        $date = $validated['date'];

        if ($validated['am_out'] && ! $validated['am_in']) {
            return response()->json([
                'message' => 'AM time-out requires an AM time-in.',
                'errors' => ['am_in' => ['AM time-out requires an AM time-in.']],
            ], 422);
        }

        if ($validated['pm_out'] && ! $validated['pm_in']) {
            return response()->json([
                'message' => 'PM time-out requires a PM time-in.',
                'errors' => ['pm_in' => ['PM time-out requires a PM time-in.']],
            ], 422);
        }

        $slotOrder = [
            ['key' => 'am_in', 'type' => 'IN'],
            ['key' => 'am_out', 'type' => 'OUT'],
            ['key' => 'pm_in', 'type' => 'IN'],
            ['key' => 'pm_out', 'type' => 'OUT'],
        ];

        $events = [];
        foreach ($slotOrder as $slot) {
            $key = $slot['key'];
            if (! empty($validated[$key])) {
                $events[] = [
                    'type' => $slot['type'],
                    'at' => Carbon::createFromFormat('Y-m-d H:i', $date.' '.$validated[$key], 'Asia/Manila'),
                ];
            }
        }

        if (count($events) === 0) {
            $this->deleteAttendanceLogsForInternDate($intern->id, $date);

            return response()->json([
                'ok' => true,
                'message' => 'Attendance cleared for this day.',
            ]);
        }

        usort($events, fn (array $a, array $b): int => $a['at'] <=> $b['at']);

        for ($i = 0; $i < count($events) - 1; $i++) {
            $a = $events[$i]['at']->getTimestamp();
            $b = $events[$i + 1]['at']->getTimestamp();
            if ($a === $b) {
                return response()->json([
                    'message' => 'Duplicate times are not allowed.',
                    'errors' => ['date' => ['Duplicate times are not allowed.']],
                ], 422);
            }

            if ($b < $a) {
                return response()->json([
                    'message' => 'Times must be in chronological order.',
                    'errors' => ['date' => ['Times must be in chronological order.']],
                ], 422);
            }
        }

        $pendingIn = false;
        foreach ($events as $e) {
            if ($e['type'] === 'IN') {
                if ($pendingIn) {
                    return response()->json([
                        'message' => 'Invalid sequence: each time-out must follow a time-in.',
                        'errors' => ['date' => ['Invalid IN/OUT sequence.']],
                    ], 422);
                }
                $pendingIn = true;
            } else {
                if (! $pendingIn) {
                    return response()->json([
                        'message' => 'Invalid sequence: time-out without a matching time-in.',
                        'errors' => ['date' => ['Invalid IN/OUT sequence.']],
                    ], 422);
                }
                $pendingIn = false;
            }
        }

        try {
            DB::transaction(function () use ($intern, $date, $events): void {
                $this->deleteAttendanceLogsForInternDate($intern->id, $date);

                foreach ($events as $e) {
                    AttendanceLog::query()->create([
                        'intern_id' => $intern->id,
                        'scan_time' => $e['at']->copy(),
                        'scan_type' => $e['type'],
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Could not save attendance. Please try again.',
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Attendance saved.',
        ]);
    }

    /**
     * Delete all attendance logs for an intern on a calendar date (Asia/Manila).
     */
    private function deleteAttendanceLogsForInternDate(int $internId, string $dateYmd): void
    {
        $tz = config('app.timezone');
        $start = Carbon::createFromFormat('Y-m-d', $dateYmd, $tz)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $dateYmd, $tz)->endOfDay();

        AttendanceLog::query()
            ->where('intern_id', $internId)
            ->whereBetween('scan_time', [$start, $end])
            ->delete();
    }

    public function calendar(Request $request)
    {
        $intern = $this->getSessionIntern();
        if (! $intern) {
            return redirect()->route('intern.login');
        }

        $year = (int) $request->get('year', date('Y'));
        $month = (int) $request->get('month', date('n'));
        $month = max(1, min(12, $month));

        $first = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $last = $first->copy()->endOfMonth();

        $attendanceLogs = $intern->attendanceLogs()
            ->whereBetween('scan_time', [$first, $last])
            ->orderBy('scan_time')
            ->get();

        $dailyAttendance = $attendanceLogs
            ->groupBy(fn ($log) => $log->scan_time->format('Y-m-d'))
            ->map(fn ($group) => $group->values());

        $renderedMinutesByDate = $dailyAttendance->map(
            fn ($group) => $this->calculateRenderedMinutesForDay($group)
        );

        $attendanceForJs = $dailyAttendance->map(
            fn ($group) => $this->buildAttendanceDayDetail($group)
        );

        $holidays = Holiday::query()
            ->whereBetween('holiday_date', [$first, $last])
            ->orderBy('holiday_date')
            ->orderBy('id')
            ->get()
            ->groupBy(fn (Holiday $holiday) => $holiday->holiday_date->format('Y-m-d'))
            ->map(fn ($group) => $group->values());

        $holidaysForJs = $holidays->map(
            fn ($group) => $group
                ->map(fn (Holiday $holiday) => $holiday->holiday_name ?? '')
                ->values()
        );

        return view('interns.calendar', [
            'intern' => $intern,
            'year' => $year,
            'month' => $month,
            'dailyAttendance' => $dailyAttendance,
            'renderedMinutesByDate' => $renderedMinutesByDate,
            'attendanceForJs' => $attendanceForJs,
            'holidays' => $holidays,
            'holidaysForJs' => $holidaysForJs,
        ]);
    }

    public function profile()
    {
        $intern = $this->getSessionIntern();
        if (!$intern) {
            return redirect()->route('intern.login');
        }

        return view('interns.profile', compact('intern'));
    }

    public function updateProfile(Request $request)
    {
        $intern = $this->getSessionIntern();
        if (!$intern) {
            return redirect()->route('intern.login');
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'birthday' => ['nullable', 'date'],
            'sex' => ['nullable', 'in:male,female'],
            'username' => ['required', 'string', 'max:50', Rule::unique('interns', 'username')->ignore($intern->id)],
        ]);

        $intern->first_name = $validated['first_name'];
        $intern->middle_name = $validated['middle_name'] ?? null;
        $intern->last_name = $validated['last_name'];
        $intern->username = $validated['username'];
        $intern->email = $validated['email'] ?? null;
        $intern->phone_number = $validated['phone_number'] ?? null;
        $intern->birthday = $validated['birthday'] ?? null;
        $intern->sex = $validated['sex'] ?? null;
        $intern->save();

        return redirect()->route('intern.profile')->with('success', 'Profile updated successfully.');
    }

    public function inlineUpdate(Request $request)
    {
        $intern = $this->getSessionIntern();
        if (!$intern) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $field = $request->input('field');
        $value = $request->input('value');

        $allowedFields = [
            'username' => ['required', 'string', 'max:50', Rule::unique('interns', 'username')->ignore($intern->id)],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'birthday' => ['nullable', 'date'],
            'sex' => ['nullable', 'in:male,female'],
        ];

        if (!array_key_exists($field, $allowedFields)) {
            return response()->json(['message' => 'Field is not allowed to be updated.'], 400);
        }

        if ($field === 'email') {
            return response()->json([
                'message' => 'Email change requires verification. Use the send-code and verify flow.',
            ], 400);
        }

        $rules = ['value' => $allowedFields[$field]];
        $validator = Validator::make(['value' => $value], $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        $intern->{$field} = $value === '' ? null : $value;
        $intern->save();

        return response()->json([
            'message' => 'Profile updated.',
            'field' => $field,
            'value' => $intern->{$field},
        ]);
    }

    public function sendEmailChangeCode(Request $request): \Illuminate\Http\JsonResponse
    {
        $intern = $this->getSessionIntern();
        if (! $intern) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'new_email' => ['required', 'string', 'email', 'max:255', Rule::unique('interns', 'email')->ignore($intern->id)],
            'current_password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        $newEmail = $request->input('new_email');
        if (! Hash::check($request->input('current_password'), $intern->password_hash)) {
            return response()->json([
                'message' => 'The current password is incorrect.',
                'errors' => ['current_password' => ['The current password is incorrect.']],
            ], 422);
        }

        $code = (string) random_int(100000, 999999);
        $cacheKey = 'email_verify_'.$intern->id;
        Cache::put($cacheKey, [
            'email' => $newEmail,
            'code' => $code,
        ], now()->addMinutes(15));

        $fromName = SystemSetting::first()?->system_short_name ?? config('mail.from.name');

        try {
            Mail::to($newEmail)->send(new EmailChangeVerificationMail($code, $intern->full_name, $fromName));
        } catch (\Throwable $e) {
            Cache::forget($cacheKey);

            return response()->json([
                'message' => 'Failed to send verification email. Please try again later.',
            ], 502);
        }

        return response()->json(['sent' => true, 'message' => 'Verification code sent to your new email.']);
    }

    public function verifyEmailChange(Request $request): \Illuminate\Http\JsonResponse
    {
        $intern = $this->getSessionIntern();
        if (! $intern) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $cacheKey = 'email_verify_'.$intern->id;
        $pending = Cache::get($cacheKey);

        if (! $pending || ! is_array($pending)) {
            return response()->json([
                'message' => 'Verification code expired or invalid. Please start the email change again.',
                'errors' => ['code' => ['Verification code expired or invalid.']],
            ], 422);
        }

        $enteredCode = $request->input('code');
        if (! hash_equals($pending['code'], $enteredCode)) {
            return response()->json([
                'message' => 'Invalid verification code. Email was not changed.',
                'errors' => ['code' => ['Invalid verification code.']],
            ], 422);
        }

        $newEmail = $pending['email'];
        $intern->email = $newEmail;
        $intern->save();
        Cache::forget($cacheKey);

        return response()->json([
            'message' => 'Email updated successfully.',
            'field' => 'email',
            'value' => $intern->email,
        ]);
    }

    public function changePassword(Request $request)
    {
        $intern = $this->getSessionIntern();
        if (!$intern) {
            return redirect()->route('intern.login');
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $intern->password_hash)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ])->withInput();
        }

        $intern->password_hash = Hash::make($validated['new_password']);
        $intern->save();

        return back()->with('success_password', 'Password changed successfully.');
    }

    public function updateAttendancePin(Request $request)
    {
        $intern = $this->getSessionIntern();
        if (!$intern) {
            return redirect()->route('intern.login');
        }

        $validated = $request->validate([
            'pin' => ['nullable', 'string', 'regex:/^[0-9]{4}$/'],
        ]);

        $pin = $validated['pin'] ?? null;

        $intern->att_code = $pin !== null && $pin !== '' ? $pin : null;
        $intern->save();

        return back()->with('success_attendance_pin', $pin ? 'Attendance PIN updated.' : 'Attendance PIN removed.');
    }

    public function logout()
    {
        Session::forget('intern_id');
        return redirect()->route('intern.login');
    }
}