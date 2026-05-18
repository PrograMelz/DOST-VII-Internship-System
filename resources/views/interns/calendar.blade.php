@extends('layouts.intern')

@section('title', 'Calendar')

@section('extra-css')
  <style>
    .schedule-card {
      background: #fff;
      padding: 10px 14px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      border-radius: 8px;
      border: 1px solid #e9ecef;
    }

    .timeline-wrapper {
      width: 100%;
      max-width: 100%;
    }

    .timeline-scroller {
      width: 100%;
      max-width: 100%;
      overflow: visible;
      padding-bottom: 0.5rem;
    }

    .timeline-content {
      width: 100%;
      min-width: 0;
    }

    .days-header {
      display: grid;
      grid-template-columns: 28px repeat(7, minmax(0, 1fr));
      text-align: center;
      font-weight: 600;
      background: #f8f9fa;
      border-bottom: 1px solid #ddd;
      margin-bottom: 2px;
      padding: 4px 2px;
      font-size: 10px;
    }

    .timeline-container {
      display: grid;
      grid-template-columns: 28px repeat(7, minmax(0, 1fr));
      gap: 1px;
      position: relative;
      font-size: 10px;
    }

    .week-label {
      border-right: 1px solid #ddd;
      background: #fff;
      padding: 2px 4px;
      text-align: right;
      color: #666;
      font-size: 10px;
      display: flex;
      align-items: center;
      justify-content: flex-end;
    }

    .day-col {
      position: relative;
      border: 1px solid #eee;
      background: #fafafa;
      min-height: 48px;
      padding: 3px;
    }

    .day-col.other-month {
      background: #f0f0f0;
      color: #999;
    }

    .day-col.today {
      background: #e7f3ff;
      border-color: var(--primary-color, #2E86C1);
      font-weight: 700;
    }

    .day-num {
      font-weight: 600;
      margin-bottom: 2px;
      font-size: 10px;
    }

    .present-day {
      background: #d1e7dd;
      border-color: #a3cfbb;
    }

    .present-day:hover {
      outline: 2px solid rgba(25, 135, 84, 0.25);
      outline-offset: -2px;
    }

    .clickable-day {
      cursor: pointer;
      transition: box-shadow 0.15s ease, outline 0.15s ease;
    }

    .clickable-day:hover {
      outline: 2px solid rgba(46, 134, 193, 0.35);
      outline-offset: -2px;
    }

    .clickable-day.holiday-day:hover {
      outline: 2px solid rgba(232, 166, 76, 0.45);
      outline-offset: -2px;
    }

    .day-col.holiday-day {
      background: #ffecd2;
      border-color: #e8a64c;
    }

    .day-col.holiday-day.today {
      background: #ffd9a3;
      border-color: var(--primary-color, #2E86C1);
    }

    .day-col-btn {
      width: 100%;
      text-align: left;
      cursor: pointer;
    }

    .day-col-btn:focus-visible {
      outline: 2px solid var(--primary-color, #2E86C1);
      outline-offset: -2px;
    }

    .scan-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 2px 6px;
      border-radius: 999px;
      font-size: 9px;
      background: rgba(255,255,255,0.6);
      border: 1px solid rgba(0,0,0,0.06);
      margin-top: 2px;
    }

    .holiday-block {
      border-radius: 4px;
      background: #ffd9a3;
      border: 1px solid #e8a64c;
      box-shadow: 0 1px 2px rgba(0,0,0,0.08);
      font-size: 9px;
      padding: 3px 4px;
      overflow: hidden;
      line-height: 1.2;
      margin-top: 2px;
    }

    .holiday-more {
      display: inline-block;
      margin-top: 2px;
      font-size: 9px;
      color: #b35c00;
      text-decoration: underline;
      cursor: pointer;
      background: transparent;
      border: 0;
      padding: 0;
    }

    .holiday-block strong {
      display: block;
      font-weight: 600;
      font-size: 9px;
    }

    .calendar-legend {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 1rem;
      font-size: 0.875rem;
    }

    .calendar-legend span {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
    }

    .calendar-legend .legend-attendance { color: #198754; }
    .calendar-legend .legend-holiday { color: #c26b00; }

    .attendance-day-table { font-size: 0.9rem; }
    .attendance-day-table th, .attendance-day-table td { min-width: 4rem; }

    @media (max-width: 768px) {
      .timeline-scroller {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }
      .timeline-content {
        min-width: 480px;
      }
    }
  </style>
@endsection

@section('content')
    <div class="content-header">
        <h1><i class="bi bi-calendar3"></i> Calendar</h1>
        <p>Your attendance and holidays in one view</p>
    </div>

    <div class="schedule-card">
        <div class="calendar-legend">
            <span class="legend-attendance"><span class="badge bg-success">Attendance</span> Days with scans</span>
            <span class="legend-holiday"><span class="badge bg-primary">Holiday</span> Company holidays</span>
            <span class="text-muted small">Click any day to view or edit IN/OUT times.</span>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h5 class="mb-0">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h5>
            </div>
            @php
                $prevMonth = $month <= 1 ? 12 : $month - 1;
                $prevYear = $month <= 1 ? $year - 1 : $year;
                $nextMonth = $month >= 12 ? 1 : $month + 1;
                $nextYear = $month >= 12 ? $year + 1 : $year;
            @endphp
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('intern.calendar', ['year' => $prevYear, 'month' => $prevMonth]) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Prev
                </a>
                <a href="{{ route('intern.calendar') }}" class="btn btn-sm btn-outline-secondary">Today</a>
                <a href="{{ route('intern.calendar', ['year' => $nextYear, 'month' => $nextMonth]) }}" class="btn btn-sm btn-outline-secondary">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>

        @php
            $first = \Carbon\Carbon::createFromDate($year, $month, 1);
            $last = $first->copy()->endOfMonth();
            $daysInMonth = $last->day;
            $startWeekday = $first->dayOfWeek;
        @endphp

        <div class="timeline-wrapper">
            <div class="timeline-scroller">
                <div class="timeline-content">
                    <div class="days-header">
                        <div></div>
                        <div>Sun</div>
                        <div>Mon</div>
                        <div>Tue</div>
                        <div>Wed</div>
                        <div>Thu</div>
                        <div>Fri</div>
                        <div>Sat</div>
                    </div>
                    <div class="timeline-container">
                        @php
                            $row = 0;
                            $totalCells = (int) ceil(($startWeekday + $daysInMonth) / 7) * 7;
                        @endphp
                        @for ($i = 0; $i < $totalCells; $i++)
                            @if ($i % 7 === 0)
                                <div class="week-label">{{ $row + 1 }}</div>
                            @endif
                            @php
                                $dayNum = $i - $startWeekday + 1;
                                $isInMonth = $dayNum >= 1 && $dayNum <= $daysInMonth;
                                $dateStr = $isInMonth ? sprintf('%04d-%02d-%02d', $year, $month, $dayNum) : null;
                                $isToday = $dateStr && $dateStr === now()->format('Y-m-d');
                                $logs = $dateStr ? ($dailyAttendance[$dateStr] ?? collect()) : collect();
                                $logCount = $logs instanceof \Illuminate\Support\Collection ? $logs->count() : 0;
                                $renderedMinutes = $dateStr && isset($renderedMinutesByDate[$dateStr])
                                    ? (int) $renderedMinutesByDate[$dateStr]
                                    : 0;
                                $renderedHours = intdiv($renderedMinutes, 60);
                                $renderedMinutesPart = $renderedMinutes % 60;
                                $holidayItems = $dateStr ? ($holidays[$dateStr] ?? collect()) : collect();
                                $holidayCount = $holidayItems instanceof \Illuminate\Support\Collection ? $holidayItems->count() : (is_array($holidayItems) ? count($holidayItems) : 0);
                                $hasAttendance = $isInMonth && $logCount > 0 && $dateStr;
                                $hasHolidays = $holidayCount > 0;
                            @endphp

                            @if ($isInMonth && $dateStr)
                                <button type="button"
                                        class="day-col day-col-btn clickable-day {{ $hasAttendance ? 'present-day' : '' }} {{ $hasHolidays ? 'holiday-day' : '' }} {{ $isToday ? 'today' : '' }}"
                                        data-attendance-date="{{ $dateStr }}"
                                        data-day-num="{{ $dayNum }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#attendanceDayModal">
                                    <div class="day-num">{{ $dayNum }}</div>
                                    @if ($hasAttendance)
                                        <div class="scan-pill">
                                            <strong>{{ $logCount }}</strong> scan{{ $logCount === 1 ? '' : 's' }}
                                        </div>
                                        @if ($renderedMinutes > 0)
                                            <div class="scan-pill">
                                                <span>Time: {{ $renderedHours }}h {{ str_pad((string) $renderedMinutesPart, 2, '0', STR_PAD_LEFT) }}m</span>
                                            </div>
                                        @endif
                                    @endif
                                    @if ($hasHolidays)
                                        @foreach (($holidayItems instanceof \Illuminate\Support\Collection ? $holidayItems->take(2) : array_slice($holidayItems, 0, 2)) as $holiday)
                                            <div class="holiday-block">
                                                <strong>{{ $holiday->holiday_name ?? '' }}</strong>
                                            </div>
                                        @endforeach
                                        @if ($holidayCount > 2)
                                            <button type="button"
                                                    class="holiday-more"
                                                    data-holiday-date="{{ $dateStr }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#holidayListModal"
                                                    onclick="event.stopPropagation()">
                                                +{{ $holidayCount - 2 }} more
                                            </button>
                                        @endif
                                    @endif
                                </button>
                            @else
                                <div class="day-col other-month">
                                    <div class="day-num"></div>
                                </div>
                            @endif

                            @if ($i % 7 === 6)
                                @php $row++; @endphp
                            @endif
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        @if ($dailyAttendance->isEmpty() && $holidays->isEmpty())
            <div class="alert alert-info mt-3 mb-0">
                <i class="bi bi-info-circle"></i>
                No attendance records or holidays for this month.
            </div>
        @endif
    </div>

    <div class="modal fade" id="attendanceDayModal" tabindex="-1" aria-labelledby="attendanceDayModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceDayModalLabel">Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="small text-muted mb-3" id="attendanceDayModalDate"></div>
                    <input type="hidden" id="attendanceDayModalDateValue" value="">
                    <p class="small text-muted mb-2">Times use 24-hour format (local). Leave empty where there is no scan. Clear all and save to remove that day’s records.</p>
                    <table class="table table-bordered table-sm attendance-day-table mb-3">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center bg-light">AM</th>
                                <th colspan="2" class="text-center bg-light">PM</th>
                            </tr>
                            <tr>
                                <th class="text-center">IN</th>
                                <th class="text-center">OUT</th>
                                <th class="text-center">IN</th>
                                <th class="text-center">OUT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="attendanceDayModalRow">
                                <td class="text-center align-middle">
                                    <input type="time" class="form-control form-control-sm mx-auto" id="input-am-in" style="max-width: 7.5rem;">
                                </td>
                                <td class="text-center align-middle">
                                    <input type="time" class="form-control form-control-sm mx-auto" id="input-am-out" style="max-width: 7.5rem;">
                                </td>
                                <td class="text-center align-middle">
                                    <input type="time" class="form-control form-control-sm mx-auto" id="input-pm-in" style="max-width: 7.5rem;">
                                </td>
                                <td class="text-center align-middle">
                                    <input type="time" class="form-control form-control-sm mx-auto" id="input-pm-out" style="max-width: 7.5rem;">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="border rounded p-2 bg-light small" id="attendanceDayModalAnalytics">
                        <div><strong>AM rendered:</strong> <span id="analytics-am">0h 00m</span></div>
                        <div><strong>PM rendered:</strong> <span id="analytics-pm">0h 00m</span></div>
                        <div class="mb-0"><strong>Total:</strong> <span id="analytics-total">0h 00m</span></div>
                    </div>
                    <div id="attendanceDayModalError" class="alert alert-danger py-2 small d-none mt-2 mb-0" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="attendanceDayModalClearBtn">Clear times</button>
                    <button type="button" class="btn btn-primary" id="attendanceDayModalSaveBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="attendanceDayModalSaveSpinner" role="status" aria-hidden="true"></span>
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="holidayListModal" tabindex="-1" aria-labelledby="holidayListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="holidayListModalLabel">Holidays</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="small text-muted mb-2" id="holidayListModalDate"></div>
                    <ul class="list-group" id="holidayListModalItems"></ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const attendanceByDate = @json($attendanceForJs ?? []);
      const holidaysByDate = @json($holidaysForJs ?? []);
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
      const calendarDayUrl = @json(route('intern.calendar.day'));

      function formatMinutes(minutes) {
        const h = Math.floor(minutes / 60);
        const m = minutes % 60;
        return h + 'h ' + String(m).padStart(2, '0') + 'm';
      }

      function parseTimeToMinutes(t) {
        if (!t || typeof t !== 'string') return null;
        const parts = t.split(':');
        if (parts.length < 2) return null;
        const h = parseInt(parts[0], 10);
        const m = parseInt(parts[1], 10);
        if (isNaN(h) || isNaN(m)) return null;
        return h * 60 + m;
      }

      function updateAnalyticsFromInputs() {
        var amIn = document.getElementById('input-am-in');
        var amOut = document.getElementById('input-am-out');
        var pmIn = document.getElementById('input-pm-in');
        var pmOut = document.getElementById('input-pm-out');
        var analyticsAm = document.getElementById('analytics-am');
        var analyticsPm = document.getElementById('analytics-pm');
        var analyticsTotal = document.getElementById('analytics-total');
        var amInM = amIn && amIn.value ? parseTimeToMinutes(amIn.value) : null;
        var amOutM = amOut && amOut.value ? parseTimeToMinutes(amOut.value) : null;
        var pmInM = pmIn && pmIn.value ? parseTimeToMinutes(pmIn.value) : null;
        var pmOutM = pmOut && pmOut.value ? parseTimeToMinutes(pmOut.value) : null;
        var amMin = (amInM != null && amOutM != null && amOutM > amInM) ? (amOutM - amInM) : 0;
        var pmMin = (pmInM != null && pmOutM != null && pmOutM > pmInM) ? (pmOutM - pmInM) : 0;
        if (analyticsAm) analyticsAm.textContent = formatMinutes(amMin);
        if (analyticsPm) analyticsPm.textContent = formatMinutes(pmMin);
        if (analyticsTotal) analyticsTotal.textContent = formatMinutes(amMin + pmMin);
      }

      const attendanceModalEl = document.getElementById('attendanceDayModal');
      if (attendanceModalEl) {
        const dateEl = document.getElementById('attendanceDayModalDate');
        const dateHidden = document.getElementById('attendanceDayModalDateValue');
        const inputAmIn = document.getElementById('input-am-in');
        const inputAmOut = document.getElementById('input-am-out');
        const inputPmIn = document.getElementById('input-pm-in');
        const inputPmOut = document.getElementById('input-pm-out');
        const analyticsAm = document.getElementById('analytics-am');
        const analyticsPm = document.getElementById('analytics-pm');
        const analyticsTotal = document.getElementById('analytics-total');
        const errEl = document.getElementById('attendanceDayModalError');
        const saveBtn = document.getElementById('attendanceDayModalSaveBtn');
        const clearBtn = document.getElementById('attendanceDayModalClearBtn');
        const saveSpinner = document.getElementById('attendanceDayModalSaveSpinner');

        function hideError() {
          if (errEl) {
            errEl.classList.add('d-none');
            errEl.textContent = '';
          }
        }

        function showError(msg) {
          if (errEl) {
            errEl.textContent = msg || 'Something went wrong.';
            errEl.classList.remove('d-none');
          }
        }

        ['input-am-in', 'input-am-out', 'input-pm-in', 'input-pm-out'].forEach(function (id) {
          var el = document.getElementById(id);
          if (el) el.addEventListener('input', updateAnalyticsFromInputs);
        });

        attendanceModalEl.addEventListener('show.bs.modal', function (event) {
          hideError();
          const trigger = event.relatedTarget;
          const date = trigger ? trigger.getAttribute('data-attendance-date') : null;
          const detail = (date && attendanceByDate[date]) ? attendanceByDate[date] : null;
          const edit = detail && detail.edit ? detail.edit : null;

          if (dateEl) dateEl.textContent = date ? ('Date: ' + date) : '';
          if (dateHidden) dateHidden.value = date || '';

          function setInput(el, v) {
            if (!el) return;
            el.value = v && typeof v === 'string' ? v : '';
          }

          if (edit) {
            setInput(inputAmIn, edit.am_in);
            setInput(inputAmOut, edit.am_out);
            setInput(inputPmIn, edit.pm_in);
            setInput(inputPmOut, edit.pm_out);
          } else {
            setInput(inputAmIn, null);
            setInput(inputAmOut, null);
            setInput(inputPmIn, null);
            setInput(inputPmOut, null);
          }

          updateAnalyticsFromInputs();
        });

        if (clearBtn) {
          clearBtn.addEventListener('click', function () {
            if (inputAmIn) inputAmIn.value = '';
            if (inputAmOut) inputAmOut.value = '';
            if (inputPmIn) inputPmIn.value = '';
            if (inputPmOut) inputPmOut.value = '';
            hideError();
            updateAnalyticsFromInputs();
          });
        }

        if (saveBtn) {
          saveBtn.addEventListener('click', async function () {
            hideError();
            var date = dateHidden ? dateHidden.value : '';
            if (!date) {
              showError('No date selected.');
              return;
            }
            var payload = {
              date: date,
              am_in: inputAmIn && inputAmIn.value ? inputAmIn.value : null,
              am_out: inputAmOut && inputAmOut.value ? inputAmOut.value : null,
              pm_in: inputPmIn && inputPmIn.value ? inputPmIn.value : null,
              pm_out: inputPmOut && inputPmOut.value ? inputPmOut.value : null,
            };
            saveBtn.disabled = true;
            if (saveSpinner) saveSpinner.classList.remove('d-none');
            try {
              var res = await fetch(calendarDayUrl, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrfToken,
                  'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
              });
              var data = await res.json().catch(function () { return {}; });
              if (!res.ok) {
                var msg = data.message || (data.errors && data.errors.date && data.errors.date[0]) || 'Could not save.';
                showError(msg);
                return;
              }
              if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: 'Saved', text: data.message || 'Attendance updated.', timer: 1600, showConfirmButton: false });
              }
              var modal = bootstrap.Modal.getInstance(attendanceModalEl);
              if (modal) modal.hide();
              window.location.reload();
            } catch (e) {
              showError('Network error. Please try again.');
            } finally {
              saveBtn.disabled = false;
              if (saveSpinner) saveSpinner.classList.add('d-none');
            }
          });
        }
      }

      const holidayModalEl = document.getElementById('holidayListModal');
      if (holidayModalEl) {
        const dateEl = document.getElementById('holidayListModalDate');
        const itemsEl = document.getElementById('holidayListModalItems');
        holidayModalEl.addEventListener('show.bs.modal', function (event) {
          const trigger = event.relatedTarget;
          const date = trigger ? trigger.getAttribute('data-holiday-date') : null;
          const items = (date && holidaysByDate[date]) ? holidaysByDate[date] : [];
          if (dateEl) dateEl.textContent = date ? ('Date: ' + date) : '';
          if (itemsEl) {
            itemsEl.innerHTML = '';
            items.forEach(function (name) {
              const li = document.createElement('li');
              li.className = 'list-group-item';
              li.textContent = name || '(No name)';
              itemsEl.appendChild(li);
            });
          }
        });
      }
    });
  </script>
@endsection
