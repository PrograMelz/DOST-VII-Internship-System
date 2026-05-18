@extends('layouts.intern')

@section('title', 'Dashboard')

@section('content')
    <div class="content-header">
        <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
        <p>Your OJT progress overview</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Required OJT Hours</h6>
                    <h3 class="mb-0">
                        @if(!is_null($requiredHours))
                            {{ number_format($requiredHours) }} hrs
                        @else
                            <span class="text-muted">Not set</span>
                        @endif
                    </h3>
                    <p class="mb-0 mt-2 small text-muted">
                        This is the total OJT hours required by your school.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Rendered OJT Time</h6>
                    <h3 class="mb-0">
                        {{ $renderedHours }}h {{ str_pad((string) $renderedMinutesPart, 2, '0', STR_PAD_LEFT) }}m
                    </h3>
                    <p class="mb-0 mt-2 small text-muted">
                        Only complete IN/OUT pairs are counted. Any time-in without a time-out is ignored.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Remaining OJT Time</h6>
                    @if(!is_null($remainingMinutesTotal) && !is_null($requiredHours))
                        <h3 class="mb-0">
                            {{ $remainingHours }}h {{ str_pad((string) $remainingMinutesPart, 2, '0', STR_PAD_LEFT) }}m
                        </h3>
                        @if($remainingMinutesTotal === 0)
                            <p class="mb-0 mt-2 small text-success fw-semibold">
                                OJT requirement fulfilled. Great job!
                            </p>
                        @else
                            <p class="mb-0 mt-2 small text-muted">
                                Time left to complete your required OJT hours.
                            </p>
                        @endif
                    @else
                        <h3 class="mb-0 text-muted">—</h3>
                        <p class="mb-0 mt-2 small text-muted">
                            No OJT hour requirement has been set for your profile yet.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(!is_null($progressPercent))
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">OJT Completion Progress</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-1">
                    <span class="small text-muted">Progress</span>
                    <span class="small fw-semibold">{{ $progressPercent }}%</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div
                        class="progress-bar"
                        role="progressbar"
                        style="width: {{ $progressPercent }}%;"
                        aria-valuenow="{{ $progressPercent }}"
                        aria-valuemin="0"
                        aria-valuemax="100"
                    ></div>
                </div>

                @if(!is_null($estimatedDaysLeft) && $estimatedDaysLeft > 0)
                    <p class="mt-3 mb-0 small text-muted">
                        Estimated time to finish:
                        <strong>{{ $estimatedDaysLeft }} day{{ $estimatedDaysLeft === 1 ? '' : 's' }}</strong>
                        @if(!is_null($estimatedMonthsLeft) && $estimatedMonthsLeft > 0)
                            (≈ {{ $estimatedMonthsLeft }} month{{ $estimatedMonthsLeft === 1 ? '' : 's' }})
                        @endif
                        @if($expectedFinishDate)
                            — expected to finish on <strong>{{ $expectedFinishDate->format('F j, Y') }}</strong>.
                        @endif
                    </p>
                @elseif(!is_null($requiredHours) && $remainingMinutesTotal === 0)
                    <p class="mt-3 mb-0 small text-success fw-semibold">
                        You have already completed your required OJT hours.
                    </p>
                @endif
            </div>
        </div>
    @endif
@endsection
