<?php

namespace App\Http\Controllers;

use App\Mail\InternWelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\AttendanceLog;
use App\Models\Holiday;
use App\Models\Intern;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('login.admin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    public function dashboard()
    {
        // Get all interns
        $interns = Intern::all();
        
        // Get total attendance records
        $totalAttendance = AttendanceLog::count();
        
        // Get today's attendance
        $todayAttendance = AttendanceLog::whereDate('scan_time', Carbon::today())->count();
        
        // Get this month's attendance
        $monthlyAttendance = AttendanceLog::whereMonth('scan_time', Carbon::now()->month)
            ->whereYear('scan_time', Carbon::now()->year)
            ->count();
        
        // Get attendance by intern (last 7 days)
        $attendanceByIntern = AttendanceLog::where('scan_time', '>=', Carbon::now()->subDays(7))
            ->select('intern_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('intern_id')
            ->with('intern')
            ->get();

        // Get recent attendance records
        $recentAttendance = AttendanceLog::orderBy('scan_time', 'desc')
            ->with('intern')
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'interns' => $interns,
            'totalAttendance' => $totalAttendance,
            'todayAttendance' => $todayAttendance,
            'monthlyAttendance' => $monthlyAttendance,
            'attendanceByIntern' => $attendanceByIntern,
            'recentAttendance' => $recentAttendance,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function internsList()
    {
        $query = Intern::query();
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('school_name', 'like', "%{$search}%");
            });
        }

        $interns = $query->paginate(15)->appends(request()->query());
        return view('admin.interns-list', compact('interns'));
    }

    /**
     * Store a new intern (called via AJAX or standard form)
     */
    public function storeIntern(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'school_id' => 'required|string|max:50',
            'school_name' => 'required|string|max:150',
            'email' => 'required|email|max:100|unique:interns,email',
            'phone_number' => 'nullable|string|max:20',
            'birthday' => 'required|date',
            'sex' => 'required|in:male,female',
            'ojt_hours_required' => 'required|integer|min:0|max:65535',
        ]);

        $namePart = ucfirst(strtolower(trim(preg_replace('/[^a-zA-Z]/', '', $data['first_name']))));
        if ($namePart === '') {
            $namePart = 'User';
        }
        $birthYear = Carbon::parse($data['birthday'])->year;

        $letters = 'abcdefghijklmnopqrstuvwxyz';
        $username = $namePart.$birthYear.'_'.substr(str_shuffle($letters), 0, 3);
        while (Intern::where('username', $username)->exists()) {
            $username = $namePart.$birthYear.'_'.substr(str_shuffle($letters), 0, 3);
        }

        $plainPassword = $namePart.$birthYear.'_'.str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);

        $internData = [
            'username' => $username,
            'password_hash' => Hash::make($plainPassword),
            'qr_code' => $this->generateInternQrCode(),
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'school_id' => $data['school_id'],
            'school_name' => $data['school_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'birthday' => $data['birthday'],
            'sex' => $data['sex'],
            'ojt_hours_required' => (int) $data['ojt_hours_required'],
        ];

        $intern = Intern::create($internData);

        $fromName = SystemSetting::first()?->system_short_name ?? config('mail.from.name');
        try {
            Mail::to($intern->email)->send(new InternWelcomeMail(
                $intern->full_name,
                $intern->username,
                $plainPassword,
                $fromName
            ));
        } catch (\Throwable $e) {
            report($e);
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'intern' => $intern]);
        }

        return redirect()->route('admin.interns-list')->with('success', 'Intern added. A welcome email with login credentials has been sent to the intern.');
    }

    /**
     * Update an existing intern
     */
    public function updateIntern(Request $request, $id)
    {
        $intern = Intern::findOrFail($id);
        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'username' => ['required', 'string', 'max:50', Rule::unique('interns', 'username')->ignore($id)],
            'school_id' => 'required|string|max:50',
            'school_name' => 'required|string|max:150',
            'email' => 'required|email|max:100|unique:interns,email,' . $id,
            'phone_number' => 'nullable|string|max:20',
            'birthday' => 'required|date',
            'sex' => 'required|in:male,female',
            'ojt_hours_required' => 'required|integer|min:0|max:65535',
            'password' => 'nullable|string|min:8',
        ]);

        $updateData = [
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'school_id' => $data['school_id'],
            'school_name' => $data['school_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'birthday' => $data['birthday'],
            'sex' => $data['sex'],
            'ojt_hours_required' => (int) $data['ojt_hours_required'],
        ];

        if (! empty($data['password'] ?? null)) {
            $updateData['password_hash'] = Hash::make($data['password']);
        }

        $intern->update($updateData);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'intern' => $intern]);
        }

        return redirect()->route('admin.interns-list')->with('success', 'Intern updated');
    }

    /**
     * Delete an intern
     */
    public function destroyIntern(Request $request, $id)
    {
        $intern = Intern::findOrFail($id);
        $intern->delete();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('admin.interns-list')->with('success', 'Intern removed');
    }

    public function internDetail($id)
    {
        $intern = Intern::findOrFail($id);
        if (request()->wantsJson()) {
            return response()->json([
                'ok' => true,
                'intern' => [
                    'id' => $intern->id,
                    'full_name' => $intern->full_name,
                    'first_name' => $intern->first_name,
                    'middle_name' => $intern->middle_name,
                    'last_name' => $intern->last_name,
                    'username' => $intern->username,
                    'email' => $intern->email,
                    'phone_number' => $intern->phone_number,
                    'birthday' => $intern->birthday,
                    'sex' => $intern->sex,
                    'school_id' => $intern->school_id,
                    'school_name' => $intern->school_name,
                    'qr_code' => $intern->qr_code,
                    'ojt_hours_required' => $intern->ojt_hours_required ?? 0,
                    'created_at' => $intern->created_at,
                ],
            ]);
        }

        return redirect()
            ->route('admin.interns-list')
            ->with('info', 'Intern details are now available from the interns list.');
    }

    public function attendanceReport()
    {
        $date = request()->get('date', Carbon::today()->format('Y-m-d'));
        $attendance = AttendanceLog::whereDate('scan_time', $date)
            ->with('intern')
            ->orderBy('scan_time', 'asc')
            ->paginate(20);
        
        return view('admin.attendance-report', compact('attendance', 'date'));
    }

    public function holidays(Request $request)
    {
        $year = (int) $request->get('year', date('Y'));
        $month = (int) $request->get('month', date('n'));
        $month = max(1, min(12, $month));

        $first = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $last = $first->copy()->endOfMonth();

        $holidays = Holiday::query()
            ->whereBetween('holiday_date', [$first, $last])
            ->orderBy('holiday_date')
            ->orderBy('id')
            ->get()
            ->groupBy(fn (Holiday $holiday) => $holiday->holiday_date->format('Y-m-d'))
            ->map(fn ($group) => $group->values());

        $holidaysForJs = $holidays->map(
            fn ($group) => $group
                ->map(fn (Holiday $holiday) => [
                    'id' => $holiday->id,
                    'name' => $holiday->holiday_name ?? '',
                ])
                ->values()
        );

        return view('admin.holidays', [
            'year' => $year,
            'month' => $month,
            'holidays' => $holidays,
            'holidaysForJs' => $holidaysForJs,
        ]);
    }

    public function storeHoliday(Request $request)
    {
        $validated = $request->validate([
            'holiday_date' => ['required', 'date'],
            'holiday_name' => ['nullable', 'string', 'max:100'],
        ]);

        $holiday = Holiday::query()->create([
            'holiday_date' => $validated['holiday_date'],
            'holiday_name' => $validated['holiday_name'] ?? null,
        ]);

        $d = $holiday->holiday_date;

        return redirect()->route('admin.holidays', [
            'year' => $d->format('Y'),
            'month' => (int) $d->format('n'),
        ])->with('success', 'Holiday added successfully.');
    }

    public function updateHoliday(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'holiday_name' => ['nullable', 'string', 'max:100'],
        ]);

        $holiday->holiday_name = $validated['holiday_name'] ?? null;
        $holiday->save();

        $d = $holiday->holiday_date;

        return redirect()->route('admin.holidays', [
            'year' => $d->format('Y'),
            'month' => (int) $d->format('n'),
        ])->with('success', 'Holiday updated successfully.');
    }

    public function destroyHoliday(Holiday $holiday)
    {
        $d = $holiday->holiday_date;
        $holiday->delete();

        return redirect()->route('admin.holidays', [
            'year' => $d->format('Y'),
            'month' => (int) $d->format('n'),
        ])->with('success', 'Holiday deleted successfully.');
    }

    public function systemSettings()
    {
        $settings = SystemSetting::query()->first();

        return view('admin.system-settings', [
            'settings' => $settings,
        ]);
    }

    public function updateSystemSettings(Request $request)
    {
        $data = $request->validate([
            'system_short_name' => 'required|string|max:20',
            'system_long_name' => 'required|string|max:150',
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'button_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'heading_text_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'body_text_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'system_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $settings = SystemSetting::query()->first();

        if ($settings === null) {
            $settings = new SystemSetting();
        }

        $settings->system_short_name = $data['system_short_name'];
        $settings->system_long_name = $data['system_long_name'];
        $settings->primary_color = $data['primary_color'];
        $settings->secondary_color = $data['secondary_color'] ?? null;
        $settings->button_color = $data['button_color'] ?? null;
        $settings->heading_text_color = $data['heading_text_color'] ?? null;
        $settings->body_text_color = $data['body_text_color'] ?? null;

        if ($request->hasFile('system_logo')) {
            $path = $request->file('system_logo')->store('system-logos', 'public');
            $settings->system_logo = 'storage/' . $path;
        }

        $settings->save();

        return redirect()
            ->route('admin.system-settings')
            ->with('success', 'System settings updated successfully.');
    }

    public function admins()
    {
        $admins = User::query()->orderBy('name')->get();

        return view('admin.admins', compact('admins'));
    }

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admin,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('admin.admins')->with('success', 'Admin added successfully.');
    }

    public function updateAdmin(Request $request, int $id)
    {
        $admin = User::query()->findOrFail($id);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admin', 'email')->ignore($admin->id)],
        ];
        if ($request->filled('password')) {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        if (! empty($validated['password'] ?? null)) {
            $admin->password = Hash::make($validated['password']);
        }
        $admin->save();

        return redirect()->route('admin.admins')->with('success', 'Admin updated successfully.');
    }

    public function destroyAdmin(int $id)
    {
        $admin = User::query()->findOrFail($id);
        if ((int) $admin->id === (int) Auth::id()) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }
        $admin->delete();

        return response()->json(['message' => 'Admin removed.']);
    }

    /**
     * Generate a unique QR code for an intern
     * Format: INTRN + 4 random characters (upper and lowercase) + 5 random numbers
     * Example: INTRNnwGt19473
     */
    private function generateInternQrCode(): string
    {
        do {
            // Generate 4 random characters (mix of upper and lowercase)
            $chars = '';
            $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            for ($i = 0; $i < 4; $i++) {
                $chars .= $charset[random_int(0, strlen($charset) - 1)];
            }

            // Generate 5 random numbers
            $numbers = '';
            for ($i = 0; $i < 5; $i++) {
                $numbers .= random_int(0, 9);
            }

            $qrCode = 'INTRN' . $chars . $numbers;

            // Check if this QR code already exists
            $exists = Intern::where('qr_code', $qrCode)->exists();
        } while ($exists);

        return $qrCode;
    }
}
