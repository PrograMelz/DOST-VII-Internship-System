@extends('layouts.admin')

@section('title', 'Attendance Report')

@section('extra-css')
    <style>
        .section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 2rem;
        }

        .filter-box {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-box form {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
            width: 100%;
        }

        .filter-box input {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .filter-box input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .filter-box button {
            background: var(--button-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .filter-box button:hover {
            background: var(--button-color);
            filter: brightness(0.9);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f9f9f9;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            color: #666;
            border-bottom: 2px solid #e0e0e0;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .intern-name {
            font-weight: 600;
            color: #333;
        }

        .time-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid var(--primary-color);
            border-radius: 5px;
            text-decoration: none;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: var(--button-color);
            color: white;
        }

        .pagination .active {
            background: var(--button-color);
            color: white;
            border-color: var(--button-color);
        }

        .pagination .disabled {
            color: #ccc;
            cursor: not-allowed;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .current-date {
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .filter-box {
                flex-direction: column;
            }

            .filter-box input, .filter-box button {
                width: 100%;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="content-header">
        <h1><i class="bi bi-calendar-check"></i> Attendance Report</h1>
        <p class="current-date">Date: {{ date('F d, Y', strtotime($date)) }}</p>
    </div>

    <div class="section">
            <div class="filter-box">
                <form method="GET" action="{{ route('admin.attendance-report') }}" style="display: flex; gap: 10px; width: 100%; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label for="date" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Select Date:</label>
                        <input 
                            type="date" 
                            name="date" 
                            id="date"
                            value="{{ $date }}"
                            style="width: 100%;"
                        >
                    </div>
                    <button type="submit" style="height: 40px;">🔍 Filter</button>
                </form>
            </div>

            @if($attendance->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Intern Name</th>
                                <th>School</th>
                                <th>Time</th>
                                <th>Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendance as $record)
                                <tr>
                                    <td class="intern-name">
                                        <a href="{{ route('admin.intern-detail', $record->intern_id) }}" style="color: var(--primary-color); text-decoration: none;">
                                            {{ $record->intern->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $record->intern->school_name }}</td>
                                    <td><span class="time-badge">{{ $record->scan_time->format('H:i:s') }}</span></td>
                                    <td>{{ $record->scan_time->format('l') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    {{ $attendance->links('pagination::simple-bootstrap-4') }}
                </div>
            @else
                <div class="no-data">
                    <p>No attendance records for this date</p>
                </div>
            @endif
    </div>
@endsection
