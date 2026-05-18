@extends('layouts.admin')

@section('title', 'Holidays Management')

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

    .day-col-btn {
      width: 100%;
      text-align: left;
      cursor: pointer;
    }

    .day-col-btn:hover {
      outline: 2px solid rgba(46, 134, 193, 0.25);
      outline-offset: -2px;
    }

    .day-col-btn:focus-visible {
      outline: 2px solid var(--primary-color, #2E86C1);
      outline-offset: -2px;
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

    .holiday-block {
      border-radius: 4px;
      background: #cce5ff;
      border: 1px solid #99c2ff;
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
      color: var(--primary-color, #2E86C1);
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
    <div class="content-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1><i class="bi bi-calendar-event"></i> Holidays Management</h1>
            <p class="mb-0">Manage holidays and non-working days.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
                <i class="bi bi-plus-lg"></i> Add Holiday
            </button>
        </div>
    </div>

    <div class="schedule-card">
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
                <a href="{{ route('admin.holidays', ['year' => $prevYear, 'month' => $prevMonth]) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Prev
                </a>
                <a href="{{ route('admin.holidays') }}" class="btn btn-sm btn-outline-secondary">Today</a>
                <a href="{{ route('admin.holidays', ['year' => $nextYear, 'month' => $nextMonth]) }}" class="btn btn-sm btn-outline-secondary">
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
                                $holidayItems = $dateStr ? ($holidays[$dateStr] ?? collect()) : collect();
                                $holidayCount = $holidayItems instanceof \Illuminate\Support\Collection ? $holidayItems->count() : (is_array($holidayItems) ? count($holidayItems) : 0);
                            @endphp
                            @if ($dateStr)
                                <button type="button"
                                        class="day-col day-col-btn {{ !$isInMonth ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }}"
                                        data-holiday-date="{{ $dateStr }}"
                                        data-day-num="{{ $dayNum }}"
                                        data-calendar-cell>
                                    <div class="day-num">{{ $isInMonth ? $dayNum : '' }}</div>
                                    @foreach (($holidayItems instanceof \Illuminate\Support\Collection ? $holidayItems->take(2) : array_slice($holidayItems, 0, 2)) as $holiday)
                                        <div class="holiday-block">
                                            <strong>{{ $holiday->holiday_name ?? '' }}</strong>
                                        </div>
                                    @endforeach

                                    @if ($holidayCount > 2)
                                        <span class="holiday-more">+{{ $holidayCount - 2 }} more</span>
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

        @if ($holidays->isEmpty())
            <div class="alert alert-info mt-3 mb-0">
                <i class="bi bi-info-circle"></i>
                No holidays configured yet. Use "Add Holiday" to add one.
            </div>
        @endif
    </div>

    <div class="modal fade" id="addHolidayModal" tabindex="-1" aria-labelledby="addHolidayModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addHolidayModalLabel">Add Holiday</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.holiday.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="holiday_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('holiday_date') is-invalid @enderror" id="holiday_date" name="holiday_date" value="{{ old('holiday_date') }}" required>
                            @error('holiday_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="holiday_name" class="form-label">Holiday name</label>
                            <input type="text" class="form-control @error('holiday_name') is-invalid @enderror" id="holiday_name" name="holiday_name" value="{{ old('holiday_name') }}" maxlength="100" placeholder="e.g. Christmas Day">
                            @error('holiday_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Holiday</button>
                    </div>
                </form>
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
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="holidayListModalAddBtn">
                            <i class="bi bi-plus-lg"></i> Add holiday for this date
                        </button>
                    </div>
                    <div class="d-flex flex-column gap-2" id="holidayListModalItems"></div>
                </div>
            </div>
        </div>
    </div>

        @section('extra-js')
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if ($errors->has('holiday_date') || $errors->has('holiday_name'))
                    var addModalEl = document.getElementById('addHolidayModal');
                    if (addModalEl) {
                        var addModal = new bootstrap.Modal(addModalEl);
                        addModal.show();
                    }
                @endif

                const holidaysByDate = @json($holidaysForJs ?? []);
                const addHolidayModalEl = document.getElementById('addHolidayModal');
                const holidayDateInput = document.getElementById('holiday_date');
                const modalEl = document.getElementById('holidayListModal');
                const calendarWrapper = document.querySelector('.timeline-container');

                if (calendarWrapper) {
                    calendarWrapper.addEventListener('click', function (e) {
                        const btn = e.target.closest('[data-calendar-cell]');
                        if (!btn) {
                            return;
                        }
                        const date = btn.getAttribute('data-holiday-date');
                        if (!date) {
                            return;
                        }
                        e.preventDefault();
                        const items = holidaysByDate[date];
                        const hasHolidays = items && items.length > 0;
                        if (hasHolidays) {
                            modalEl.dataset.selectedDate = date;
                            bootstrap.Modal.getOrCreateInstance(modalEl).show();
                        } else {
                            if (holidayDateInput) {
                                holidayDateInput.value = date;
                            }
                            bootstrap.Modal.getOrCreateInstance(addHolidayModalEl).show();
                        }
                    });
                }

                const holidayListModalAddBtn = document.getElementById('holidayListModalAddBtn');
                if (holidayListModalAddBtn && addHolidayModalEl) {
                    holidayListModalAddBtn.addEventListener('click', function () {
                        const date = modalEl.dataset.listModalDate || modalEl.dataset.selectedDate;
                        if (date && holidayDateInput) {
                            holidayDateInput.value = date;
                        }
                        bootstrap.Modal.getInstance(modalEl).hide();
                        bootstrap.Modal.getOrCreateInstance(addHolidayModalEl).show();
                    });
                }

                if (!modalEl) {
                    return;
                }

                const dateEl = document.getElementById('holidayListModalDate');
                const itemsEl = document.getElementById('holidayListModalItems');
                const csrf = @json(csrf_token());
                const updateUrlTemplate = @json(route('admin.holiday.update', ['holiday' => '__ID__']));
                const destroyUrlTemplate = @json(route('admin.holiday.destroy', ['holiday' => '__ID__']));
                const saveTimers = new Map();

                function setStatus(statusEl, text, kind) {
                    if (!statusEl) {
                        return;
                    }
                    statusEl.textContent = text || '';
                    statusEl.className = 'small ms-1 ' + (kind === 'error' ? 'text-danger' : kind === 'saving' ? 'text-muted' : 'text-success');
                }

                function renderCalendarCell(date) {
                    const cell = document.querySelector('[data-holiday-date="' + date + '"]');
                    if (!cell) {
                        return;
                    }

                    const items = holidaysByDate[date] || [];
                    const dayNum = cell.getAttribute('data-day-num') || (date ? String(parseInt(date.slice(-2), 10) || '') : '');
                    const isToday = date === new Date().toISOString().slice(0, 10);

                    if (!items.length) {
                        const replacement = document.createElement('button');
                        replacement.type = 'button';
                        replacement.className = 'day-col day-col-btn' + (isToday ? ' today' : '');
                        replacement.setAttribute('data-holiday-date', date);
                        replacement.setAttribute('data-day-num', dayNum);
                        replacement.setAttribute('data-calendar-cell', '');
                        const day = document.createElement('div');
                        day.className = 'day-num';
                        day.textContent = dayNum;
                        replacement.appendChild(day);
                        cell.replaceWith(replacement);
                        return;
                    }

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'day-col day-col-btn' + (isToday ? ' today' : '');
                    btn.setAttribute('data-holiday-date', date);
                    btn.setAttribute('data-day-num', dayNum);
                    btn.setAttribute('data-calendar-cell', '');

                    const day = document.createElement('div');
                    day.className = 'day-num';
                    day.textContent = dayNum;
                    btn.appendChild(day);

                    items.slice(0, 2).forEach(function (h) {
                        const block = document.createElement('div');
                        block.className = 'holiday-block';
                        const strong = document.createElement('strong');
                        strong.textContent = (h && h.name) ? String(h.name) : '';
                        block.appendChild(strong);
                        btn.appendChild(block);
                    });

                    if (items.length > 2) {
                        const more = document.createElement('span');
                        more.className = 'holiday-more';
                        more.textContent = '+' + (items.length - 2) + ' more';
                        btn.appendChild(more);
                    }

                    cell.replaceWith(btn);
                }

                async function autoSaveHoliday(id, name, statusEl, date, index) {
                    setStatus(statusEl, 'Saving…', 'saving');

                    const url = updateUrlTemplate.replace('__ID__', id);
                    const body = new URLSearchParams();
                    body.set('_token', csrf);
                    body.set('_method', 'PATCH');
                    body.set('holiday_name', name);

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: body.toString(),
                        });

                        if (!res.ok) {
                            setStatus(statusEl, 'Failed to save', 'error');
                            return;
                        }

                        if (date && typeof index === 'number' && holidaysByDate[date] && holidaysByDate[date][index]) {
                            holidaysByDate[date][index].name = name;
                            renderCalendarCell(date);
                        }

                        setStatus(statusEl, 'Saved', 'ok');
                        window.setTimeout(function () {
                            if (statusEl && statusEl.textContent === 'Saved') {
                                setStatus(statusEl, '', 'ok');
                            }
                        }, 1200);
                    } catch (e) {
                        setStatus(statusEl, 'Failed to save', 'error');
                    }
                }

                modalEl.addEventListener('show.bs.modal', function (event) {
                    const trigger = event.relatedTarget;
                    const date = (trigger && trigger.getAttribute('data-holiday-date')) || modalEl.dataset.selectedDate || null;
                    delete modalEl.dataset.selectedDate;
                    if (date) {
                        modalEl.dataset.listModalDate = date;
                    }
                    const items = (date && holidaysByDate[date]) ? holidaysByDate[date] : [];

                    if (dateEl) {
                        dateEl.textContent = date ? ('Date: ' + date) : '';
                    }

                    if (itemsEl) {
                        itemsEl.innerHTML = '';
                        items.forEach(function (holiday, index) {
                            const id = holiday && holiday.id ? String(holiday.id) : '';
                            const name = holiday && holiday.name ? String(holiday.name) : '';
                            if (!id) {
                                return;
                            }

                            const row = document.createElement('div');
                            row.className = 'border rounded p-2';

                            const nameInput = document.createElement('input');
                            nameInput.type = 'text';
                            nameInput.name = 'holiday_name';
                            nameInput.maxLength = 100;
                            nameInput.value = name;
                            nameInput.className = 'form-control form-control-sm';
                            nameInput.placeholder = 'Holiday name';

                            const status = document.createElement('span');
                            status.className = 'small ms-1';

                            nameInput.addEventListener('input', function () {
                                const timerKey = 't:' + id;
                                const existing = saveTimers.get(timerKey);
                                if (existing) {
                                    window.clearTimeout(existing);
                                }

                                setStatus(status, 'Saving…', 'saving');
                                const t = window.setTimeout(function () {
                                    autoSaveHoliday(id, nameInput.value, status, date, index);
                                }, 600);
                                saveTimers.set(timerKey, t);
                            });

                            nameInput.addEventListener('blur', function () {
                                const timerKey = 't:' + id;
                                const existing = saveTimers.get(timerKey);
                                if (existing) {
                                    window.clearTimeout(existing);
                                }
                                autoSaveHoliday(id, nameInput.value, status, date, index);
                            });

                            const deleteForm = document.createElement('form');
                            deleteForm.method = 'POST';
                            deleteForm.action = destroyUrlTemplate.replace('__ID__', id);
                            deleteForm.addEventListener('submit', async function (e) {
                                e.preventDefault();
                                if (typeof Swal === 'undefined') {
                                    return;
                                }

                                const result = await Swal.fire({
                                    icon: 'warning',
                                    title: 'Delete holiday?',
                                    text: 'This will remove the holiday from this date.',
                                    showCancelButton: true,
                                    confirmButtonText: 'Delete',
                                    cancelButtonText: 'Cancel',
                                    confirmButtonColor: '#dc3545',
                                });

                                if (!result.isConfirmed) {
                                    return;
                                }

                                try {
                                    const url = destroyUrlTemplate.replace('__ID__', id);
                                    const body = new URLSearchParams();
                                    body.set('_token', csrf);
                                    body.set('_method', 'DELETE');

                                    const res = await fetch(url, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                                            'X-Requested-With': 'XMLHttpRequest',
                                        },
                                        body: body.toString(),
                                    });

                                    if (!res.ok) {
                                        await Swal.fire({
                                            icon: 'error',
                                            title: 'Delete failed',
                                            text: 'Please try again.',
                                        });
                                        return;
                                    }

                                    if (date && holidaysByDate[date]) {
                                        holidaysByDate[date].splice(index, 1);
                                        if (!holidaysByDate[date].length) {
                                            delete holidaysByDate[date];
                                        }
                                        renderCalendarCell(date);
                                    }

                                    row.remove();
                                } catch (err) {
                                    await Swal.fire({
                                        icon: 'error',
                                        title: 'Delete failed',
                                        text: 'Please try again.',
                                    });
                                }
                            });

                            const dCsrf = document.createElement('input');
                            dCsrf.type = 'hidden';
                            dCsrf.name = '_token';
                            dCsrf.value = csrf;

                            const dMethod = document.createElement('input');
                            dMethod.type = 'hidden';
                            dMethod.name = '_method';
                            dMethod.value = 'DELETE';

                            const delBtn = document.createElement('button');
                            delBtn.type = 'submit';
                            delBtn.className = 'btn btn-sm btn-outline-danger flex-shrink-0';
                            delBtn.textContent = 'Delete';

                            const top = document.createElement('div');
                            top.className = 'd-flex align-items-center gap-2 flex-wrap flex-md-nowrap';
                            top.style.width = '100%';

                            const inputWrap = document.createElement('div');
                            inputWrap.className = 'd-flex align-items-center gap-2 flex-grow-1';
                            inputWrap.style.minWidth = '220px';

                            nameInput.classList.add('flex-grow-1');
                            nameInput.style.minWidth = '180px';

                            deleteForm.appendChild(dCsrf);
                            deleteForm.appendChild(dMethod);
                            deleteForm.appendChild(delBtn);

                            inputWrap.appendChild(nameInput);
                            inputWrap.appendChild(status);
                            top.appendChild(inputWrap);
                            top.appendChild(deleteForm);

                            row.appendChild(top);
                            itemsEl.appendChild(row);
                        });
                    }
                });
                });
            </script>
        @endsection
@endsection
