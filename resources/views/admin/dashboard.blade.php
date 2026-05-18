@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="content-header">
        <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
        <p>Welcome back, {{ Auth::user()->name }}! Here's an overview of your internship management system</p>
    </div>

    <!-- Quick Links -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.interns-list') }}" class="btn btn-primary w-100 py-3">
                <i class="bi bi-people me-2"></i> View All Interns
            </a>
        </div>
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.attendance-report') }}" class="btn btn-info w-100 py-3">
                <i class="bi bi-calendar-check me-2"></i> Attendance Report
            </a>
        </div>
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('qr-attendance') }}" class="btn btn-success w-100 py-3">
                <i class="bi bi-qr-code me-2"></i> QR Scanner
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-start border-5 border-success">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Total Interns</h6>
                    <h3 class="card-text mb-2">{{ $interns->count() }}</h3>
                    <small class="text-success"><i class="bi bi-check-circle"></i> Active in system</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-start border-5 border-warning">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Today's Attendance</h6>
                    <h3 class="card-text mb-2">{{ $todayAttendance }}</h3>
                    <small class="text-warning">
                        <i class="bi bi-percent"></i> 
                        {{ $interns->count() > 0 ? round(($todayAttendance / $interns->count()) * 100) : 0 }}% of interns
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-start border-5 border-info">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Total Attendance Records</h6>
                    <h3 class="card-text mb-2">{{ $totalAttendance }}</h3>
                    <small class="text-info"><i class="bi bi-graph-up"></i> All time</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-start border-5 border-danger">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">This Month's Attendance</h6>
                    <h3 class="card-text mb-2">{{ $monthlyAttendance }}</h3>
                    <small class="text-danger"><i class="bi bi-calendar"></i> Current month</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="card mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Recent Attendance Records</h5>
            <a href="{{ route('admin.attendance-report') }}" class="btn btn-sm btn-outline-primary">View All →</a>
        </div>
        <div class="card-body">
            @if($recentAttendance->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Intern Name</th>
                                <th>School</th>
                                <th>Scan Date</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAttendance as $attendance)
                                <tr>
                                    <td class="fw-600">{{ $attendance->intern->full_name }}</td>
                                    <td>{{ $attendance->intern->school_name }}</td>
                                    <td>{{ $attendance->scan_time->format('M d, Y') }}</td>
                                    <td><span class="badge bg-info">{{ $attendance->scan_time->format('H:i:s') }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted"><i class="bi bi-inbox"></i> No attendance records yet</p>
                </div>
            @endif
        </div>
    </div>
@endsection
