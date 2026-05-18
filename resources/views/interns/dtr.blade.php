@extends('layouts.intern')

@section('title', 'DTR')

@section('content')
    <div class="content-header">
        <h1><i class="bi bi-download"></i> DTR</h1>
        <p>Download your Daily Time Record</p>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">DTR Generator</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dtr.generate') }}">
                        <div class="row g-3">
                            <div class="col-3">
                                <label for="year" class="form-label">Year</label>
                                <select name="year" id="year" class="form-select" required>
                                    @for ($y = date('Y'); $y >= 2020; $y--)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="month" class="form-label">Month</label>
                                <select name="month" id="month" class="form-select" required>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="incharge" class="form-label">In-Charge / Supervisor</label>
                                <input type="text" name="incharge" id="incharge" class="form-control" placeholder="e.g. Juan Dela Cruz" required>
                            </div>

                            <div class="col-12">
                                <div class="border rounded p-3 bg-light">
                                    <div class="fw-semibold mb-2">Official schedule</div>
                                    <div class="row g-3">
                                        <div class="col-3">
                                            <label for="am_official_arrival" class="form-label mb-1">AM Arrival</label>
                                            <input type="time" name="am_official_arrival" id="am_official_arrival" class="form-control" value="08:00" required>
                                        </div>
                                        <div class="col-3">
                                            <label for="am_official_departure" class="form-label mb-1">AM Departure</label>
                                            <input type="time" name="am_official_departure" id="am_official_departure" class="form-control" value="12:00" required>
                                        </div>
                                        <div class="col-3">
                                            <label for="pm_official_arrival" class="form-label mb-1">PM Arrival</label>
                                            <input type="time" name="pm_official_arrival" id="pm_official_arrival" class="form-control" value="13:00" required>
                                        </div>
                                        <div class="col-3">
                                            <label for="pm_official_departure" class="form-label mb-1">PM Departure</label>
                                            <input type="time" name="pm_official_departure" id="pm_official_departure" class="form-control" value="17:00" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <input type="hidden" name="intern_id" value="{{ $intern->id }}">
                                <button type="submit" class="btn btn-primary w-100 w-md-auto">
                                    <i class="bi bi-file-earmark-arrow-down"></i> Generate DTR
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
