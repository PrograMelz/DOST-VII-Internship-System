<?php

use App\Http\Controllers\QrAttendanceController;
use App\Http\Controllers\InternController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DTR;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::get('/qr-scanner', [QrAttendanceController::class, 'index'])->name('qr-attendance');
Route::get('/qr-attendance/latest', [QrAttendanceController::class, 'latest'])->name('qr-attendance.latest');
Route::post('/qr-attendance/scan', [QrAttendanceController::class, 'scan'])->name('qr-attendance.scan');

// Admin routes
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('/admin/interns', [AdminController::class, 'internsList'])->name('admin.interns-list');
    Route::post('/admin/interns', [AdminController::class, 'storeIntern'])->name('admin.intern.store');
    Route::get('/admin/intern/{id}', [AdminController::class, 'internDetail'])->name('admin.intern-detail');
    Route::put('/admin/intern/{id}', [AdminController::class, 'updateIntern'])->name('admin.intern.update');
    Route::delete('/admin/intern/{id}', [AdminController::class, 'destroyIntern'])->name('admin.intern.destroy');
    Route::get('/admin/attendance-report', [AdminController::class, 'attendanceReport'])->name('admin.attendance-report');
    Route::get('/admin/holidays', [AdminController::class, 'holidays'])->name('admin.holidays');
    Route::post('/admin/holidays', [AdminController::class, 'storeHoliday'])->name('admin.holiday.store');
    Route::patch('/admin/holidays/{holiday}', [AdminController::class, 'updateHoliday'])->name('admin.holiday.update');
    Route::delete('/admin/holidays/{holiday}', [AdminController::class, 'destroyHoliday'])->name('admin.holiday.destroy');
    Route::get('/admin/system-settings', [AdminController::class, 'systemSettings'])->name('admin.system-settings');
    Route::post('/admin/system-settings', [AdminController::class, 'updateSystemSettings'])->name('admin.system-settings.update');
    Route::get('/admin/admins', [AdminController::class, 'admins'])->name('admin.admins');
    Route::post('/admin/admins', [AdminController::class, 'storeAdmin'])->name('admin.admin.store');
    Route::put('/admin/admin/{id}', [AdminController::class, 'updateAdmin'])->name('admin.admin.update');
    Route::delete('/admin/admin/{id}', [AdminController::class, 'destroyAdmin'])->name('admin.admin.destroy');
});

// Intern routes
Route::get('/intern/login', [InternController::class, 'showLoginForm'])->name('intern.login');
Route::post('/intern/login', [InternController::class, 'login'])->name('intern.login.post');
Route::get('/intern/forgot-password', [InternController::class, 'showForgotPasswordForm'])->name('intern.forgot-password');
Route::post('/intern/forgot-password/send-code', [InternController::class, 'sendPasswordResetCode'])->name('intern.forgot-password.send-code');
Route::post('/intern/forgot-password/reset', [InternController::class, 'resetPasswordWithCode'])->name('intern.forgot-password.reset');
Route::get('/intern/dashboard', [InternController::class, 'dashboard'])->name('intern.dashboard');
Route::get('/intern/calendar', [InternController::class, 'calendar'])->name('intern.calendar');
Route::post('/intern/calendar/day', [InternController::class, 'updateCalendarDay'])->name('intern.calendar.day');
Route::get('/intern/profile', [InternController::class, 'profile'])->name('intern.profile');
Route::put('/intern/profile', [InternController::class, 'updateProfile'])->name('intern.profile.update');
Route::post('/intern/profile/inline-update', [InternController::class, 'inlineUpdate'])->name('intern.profile.inline-update');
Route::post('/intern/profile/email-change/send-code', [InternController::class, 'sendEmailChangeCode'])->name('intern.profile.email-change.send-code');
Route::post('/intern/profile/email-change/verify', [InternController::class, 'verifyEmailChange'])->name('intern.profile.email-change.verify');
Route::post('/intern/change-password', [InternController::class, 'changePassword'])->name('intern.change-password');
Route::post('/intern/attendance-pin', [InternController::class, 'updateAttendancePin'])->name('intern.attendance-pin');
Route::post('/intern/logout', [InternController::class, 'logout'])->name('intern.logout');
Route::get('/intern/dtr', [DTR::class, 'showForm'])->name('intern.dtr');

// DTR generation
Route::get('/dtr', [DTR::class, 'index'])->name('dtr.generate');
