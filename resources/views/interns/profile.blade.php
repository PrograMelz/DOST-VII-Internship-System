@extends('layouts.intern')

@section('title', 'Personal Details')

@section('extra-css')
<style>
    .profile-header-card {
        background: linear-gradient(135deg, var(--primary-color, #2E86C1) 0%, var(--primary-color, #1B4F72) 100%);
        border: none;
        border-radius: 12px;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        border: 3px solid rgba(255,255,255,0.5);
    }
    .profile-detail-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .profile-detail-row:last-child { border-bottom: none; }
    .profile-detail-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #e7f3ff;
        color: var(--primary-color, #2E86C1);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }
    .profile-detail-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 2px;
    }
    .profile-detail-value {
        font-weight: 600;
        color: #212529;
    }
    .profile-section-card {
        border-radius: 12px;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .profile-section-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        padding: 12px 1rem;
    }
    .profile-section-card .card-header i {
        margin-right: 8px;
        opacity: 0.9;
    }
    .profile-section-card .card-body { padding: 1rem; }
    .qr-display-card {
        background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        padding: 1.25rem;
        text-align: center;
        cursor: pointer;
    }
    .qr-display-code {
        font-family: ui-monospace, monospace;
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        color: #495057;
    }
    .qr-display-card-sm {
        padding: 0.5rem 0.75rem;
    }
    .qr-display-code-sm {
        font-size: 0.75rem;
    }
    .qr-display-card-header {
        background: var(--secondary-color, #3498DB);
        border: 1px solid rgba(255, 255, 255, 0.5);
        padding: 0.75rem 1rem;
        min-width: 80px;
    }
    .editable-profile-field {
        display: inline-block;
        min-width: 40px;
        padding: 2px 3px;
        border-bottom: 1px dashed transparent;
        cursor: text;
    }
    .editable-profile-field:focus {
        outline: none;
        border-bottom-color: var(--primary-color, #2E86C1);
        background-color: #fff9e6;
        border-radius: 4px;
    }
    .editable-profile-field.empty-placeholder {
        color: #adb5bd;
        font-style: italic;
    }
    .email-code-boxes {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin: 1rem 0;
    }
    .email-code-box {
        width: 2.75rem;
        height: 2.75rem;
        text-align: center;
        font-size: 1.35rem;
        font-weight: 700;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        background: #fff;
        color: #212529;
    }
    .email-code-box:focus {
        outline: none;
        border-color: var(--primary-color, #2E86C1);
        box-shadow: 0 0 0 3px rgba(46, 134, 193, 0.25);
    }
    .email-code-box.is-invalid {
        border-color: #dc3545;
    }
</style>
@endsection

@section('content')
    <div class="content-header d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-person-vcard"></i> Personal Details</h1>
            <p class="mb-0 text-muted">Your profile information</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="bi bi-shield-lock"></i> Change Password
            </button>
        </div>
    </div>

    <!-- Profile header -->
    <div class="card profile-header-card mb-4">
        <div class="card-body d-flex align-items-center gap-4 flex-wrap py-4">
            <div class="profile-avatar" id="profileAvatar">
                {{ strtoupper(mb_substr($intern->full_name, 0, 1)) }}
            </div>
            <div class="flex-grow-1">
                <h2 class="mb-1 text-white" id="profileHeaderName">{{ $intern->full_name }}</h2>
                <p class="mb-0 opacity-90 small"
                   id="profileHeaderSchool"
                   data-school-id="{{ $intern->school_id ?? '' }}"
                   data-school-name="{{ $intern->school_name ?? '' }}">
                    {{ $intern->school_id }} | {{ $intern->school_name }}
                </p>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                @if($intern->qr_code)
                    <button type="button"
                            class="qr-display-card qr-display-card-header"
                            data-bs-toggle="modal"
                            data-bs-target="#qrCodeModal">
                        <i class="bi bi-qr-code" style="font-size: 1.5rem;"></i>
                        <div class="opacity-90 small mt-1">View QR</div>
                    </button>
                @else
                    <div class="qr-display-card qr-display-card-header opacity-50" title="No QR code assigned">
                        <i class="bi bi-qr-code text-white" style="font-size: 1.5rem;"></i>
                        <div class="text-white opacity-90 small mt-1">—</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Personal Information (60%) -->
        <div class="col-12 col-lg-7">
            <div class="card profile-section-card h-100">
                <div class="card-header">
                    <i class="bi bi-person-lines-fill"></i> Personal information
                </div>
                <div class="card-body">
                    <div class="profile-detail-row">
                        <div class="profile-detail-icon"><i class="bi bi-person"></i></div>
                        <div class="flex-grow-1">
                            <div class="profile-detail-value row g-2">
                                <div class="col-12 col-md-4">
                                    <span class="editable-profile-field {{ $intern->first_name ? '' : 'empty-placeholder' }}"
                                          contenteditable="true"
                                          data-field="first_name">
                                        {{ $intern->first_name ?? '—' }}
                                    </span>
                                    <div class="small text-muted mt-1">First name</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <span class="editable-profile-field {{ $intern->middle_name ? '' : 'empty-placeholder' }}"
                                          contenteditable="true"
                                          data-field="middle_name">
                                        {{ $intern->middle_name ?? '—' }}
                                    </span>
                                    <div class="small text-muted mt-1">Middle name</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <span class="editable-profile-field {{ $intern->last_name ? '' : 'empty-placeholder' }}"
                                          contenteditable="true"
                                          data-field="last_name">
                                        {{ $intern->last_name ?? '—' }}
                                    </span>
                                    <div class="small text-muted mt-1">Last name</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profile-detail-row">
                        <div class="profile-detail-icon"><i class="bi bi-person-badge"></i></div>
                        <div class="flex-grow-1">
                            <div class="profile-detail-value row g-2 align-items-start">
                                <div class="col-12 col-md-4">
                                    <select class="form-select form-select-sm"
                                            data-field="sex"
                                            data-original="{{ $intern->sex ?? '' }}">
                                        <option value="male" @selected(($intern->sex ?? '') === 'male')>Male</option>
                                        <option value="female" @selected(($intern->sex ?? '') === 'female')>Female</option>
                                    </select>
                                    <div class="small text-muted mt-1">Sex</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="input-group input-group-sm">
                                        <input type="date"
                                               class="form-control"
                                               data-field="birthday"
                                               value="{{ $intern->birthday ?? '' }}"
                                               data-original="{{ $intern->birthday ?? '' }}">
                                    </div>
                                    <div class="small text-muted mt-1">Birthday</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div>{{ $intern->school_id ?? '—' }}</div>
                                    <div class="small text-muted mt-1">School ID</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profile-detail-row">
                        <div class="profile-detail-icon"><i class="bi bi-building"></i></div>
                        <div class="flex-grow-1">
                            <div class="profile-detail-value row g-2 align-items-start">
                                <div class="col-12 col-md-12">
                                    <div>{{ $intern->school_name ?? '—' }}</div>
                                    <div class="small text-muted mt-1">School name</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account & Contacts (40%) -->
        <div class="col-12 col-lg-5">
            <div class="card profile-section-card h-100">
                <div class="card-header">
                    <i class="bi bi-key"></i> Account & contacts
                </div>
                <div class="card-body">
                    <div class="profile-detail-row">
                        <div class="profile-detail-icon"><i class="bi bi-person-circle"></i></div>
                        <div class="flex-grow-1">
                            <div class="profile-detail-value row g-2">
                                <div class="col-12 col-md-6">
                                    <div class="profile-detail-label">Username</div>
                                    <span class="editable-profile-field {{ $intern->username ? '' : 'empty-placeholder' }}"
                                          contenteditable="true"
                                          data-field="username">
                                        {{ $intern->username ?? '—' }}
                                    </span>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="profile-detail-label">Phone number</div>
                                    <span class="editable-profile-field {{ $intern->phone_number ? '' : 'empty-placeholder' }}"
                                          contenteditable="true"
                                          data-field="phone_number">
                                        {{ $intern->phone_number ?? '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profile-detail-row">
                        <div class="profile-detail-icon"><i class="bi bi-envelope"></i></div>
                        <div class="flex-grow-1">
                            <div class="profile-detail-label">Email</div>
                            <div class="profile-detail-value">
                                <span class="editable-profile-field {{ $intern->email ? '' : 'empty-placeholder' }}"
                                      contenteditable="true"
                                      data-field="email">
                                    {{ $intern->email ?? '—' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="profile-detail-row">
                        <div class="profile-detail-icon"><i class="bi bi-shield-check"></i></div>
                        <div class="flex-grow-1">
                            <div class="profile-detail-label d-flex align-items-center gap-1">
                                Attendance PIN (optional)
                                <button type="button"
                                        class="btn btn-link btn-sm p-0 text-muted text-decoration-none"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Used only when scanning your attendance QR code. Leave blank and save to remove your PIN."
                                        aria-label="Help">
                                    <i class="bi bi-question-circle"></i>
                                </button>
                            </div>
                            <div class="profile-detail-value">
                                <form method="POST" action="{{ route('intern.attendance-pin') }}" class="row g-2 align-items-center">
                                    @csrf
                                    <div class="col-8">
                                        <input
                                            type="password"
                                            name="pin"
                                            maxlength="4"
                                            inputmode="numeric"
                                            pattern="[0-9]{4}"
                                            class="form-control form-control-sm @error('pin') is-invalid @enderror"
                                            placeholder="{{ $intern->att_code ? 'You\'ve already set a PIN.' : '4 digit PIN not set.' }}"
                                        >
                                        @error('pin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if(session('success_attendance_pin'))
                                            <div class="text-success small mt-1">
                                                {{ session('success_attendance_pin') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-4 text-end">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            Save PIN
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeModalLabel">
                        <i class="bi bi-qr-code me-2"></i>Your Attendance QR Code
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    @if($intern->qr_code)
                        @php
                            $qrUrl = 'https://quickchart.io/qr?text=' . urlencode($intern->qr_code) . '&dark=1c3f72&size=500&centerImageUrl=https%3A%2F%2Fi.ibb.co%2FhR6GWNn9%2Flogo.png';
                        @endphp
                        <div class="d-flex justify-content-center mb-3">
                            <img src="{{ $qrUrl }}"
                                 alt="QR Code"
                                 class="img-fluid"
                                 style="max-width: 320px; height: auto;" />
                        </div>
                        <div class="qr-display-code mb-2">
                            {{ $intern->qr_code }}
                        </div>
                        <p class="text-muted small mb-0">
                            This QR code is used for your attendance. Do not share it with others.
                        </p>
                    @else
                        <p class="mb-0 text-muted">No QR code is assigned to your account.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        <i class="bi bi-shield-lock me-2"></i>Change Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('intern.change-password') }}">
                    @csrf
                    <div class="modal-body">
                        @if(session('success_password'))
                            <div class="alert alert-success">
                                {{ session('success_password') }}
                            </div>
                        @endif
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current password</label>
                            <input type="password" name="current_password" id="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New password</label>
                            <input type="password" name="new_password" id="new_password"
                                   class="form-control @error('new_password') is-invalid @enderror" required minlength="8">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-0">
                            <label for="new_password_confirmation" class="form-label">Confirm new password</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                   class="form-control" required minlength="8">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Update password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Email Confirmation Modal (two steps: password → 6-digit code) -->
    <div class="modal fade" id="changeEmailModal" tabindex="-1" aria-labelledby="changeEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeEmailModalLabel">
                        <i class="bi bi-envelope me-2"></i>Confirm email change
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Step 1: Current password -->
                    <form id="changeEmailForm">
                        <div id="changeEmailStep1">
                            <p class="mb-3">You are changing your email address. Enter your current password to receive a 6-digit verification code at your new email.</p>
                            <div class="mb-3">
                                <label for="change_email_new" class="form-label">New email address</label>
                                <input type="email" id="change_email_new" class="form-control" readonly>
                            </div>
                            <div class="mb-0">
                                <label for="change_email_password" class="form-label">Current password</label>
                                <input type="password" name="current_password" id="change_email_password"
                                       class="form-control" required
                                       placeholder="Enter your current password">
                                <div id="changeEmailPasswordError" class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="modal-footer px-0 pb-0 pt-3" id="changeEmailStep1Footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="changeEmailSubmitBtn">
                                <i class="bi bi-send me-1"></i>Send verification code
                            </button>
                        </div>
                    </form>
                    <!-- Step 2: Enter 6-digit code (6 boxes, auto-verify when complete) -->
                    <div id="changeEmailStep2" class="d-none">
                        <p class="mb-3">A 6-digit verification code was sent to <strong id="changeEmailSentTo"></strong>. Enter it below to confirm you own this email. If the code is wrong or expired, your email will not be changed.</p>
                        <div class="mb-3">
                            <label class="form-label d-block text-center">Verification code</label>
                            <div class="email-code-boxes" id="changeEmailCodeBoxes">
                                <input type="text" inputmode="numeric" maxlength="1" class="email-code-box" data-idx="0" autocomplete="off" aria-label="Digit 1">
                                <input type="text" inputmode="numeric" maxlength="1" class="email-code-box" data-idx="1" autocomplete="off" aria-label="Digit 2">
                                <input type="text" inputmode="numeric" maxlength="1" class="email-code-box" data-idx="2" autocomplete="off" aria-label="Digit 3">
                                <input type="text" inputmode="numeric" maxlength="1" class="email-code-box" data-idx="3" autocomplete="off" aria-label="Digit 4">
                                <input type="text" inputmode="numeric" maxlength="1" class="email-code-box" data-idx="4" autocomplete="off" aria-label="Digit 5">
                                <input type="text" inputmode="numeric" maxlength="1" class="email-code-box" data-idx="5" autocomplete="off" aria-label="Digit 6">
                            </div>
                            <div id="changeEmailCodeError" class="invalid-feedback d-block text-center"></div>
                        </div>
                        <div class="modal-footer px-0 pb-0 pt-3">
                            <button type="button" class="btn btn-outline-secondary" id="changeEmailBackBtn">Back</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inlineUpdateUrl = "{{ route('intern.profile.inline-update') }}";
            const sendEmailCodeUrl = "{{ route('intern.profile.email-change.send-code') }}";
            const verifyEmailCodeUrl = "{{ route('intern.profile.email-change.verify') }}";
            const csrfToken = "{{ csrf_token() }}";

            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                new bootstrap.Tooltip(el);
            });

            function cleanValue(text) {
                if (!text) {
                    return '';
                }
                const trimmed = text.trim();
                return trimmed === '—' ? '' : trimmed;
            }

            function getFieldValue(field) {
                const el = document.querySelector(`[data-field="${field}"]`);
                if (!el) {
                    return '';
                }

                if (el.matches('select, input')) {
                    return String(el.value ?? '').trim();
                }

                return cleanValue(el.textContent);
            }

            function refreshProfileHeader() {
                const first = getFieldValue('first_name');
                const middle = getFieldValue('middle_name');
                const last = getFieldValue('last_name');
                const parts = [first, middle, last].filter(Boolean);
                const fullName = parts.join(' ').trim();

                const nameEl = document.getElementById('profileHeaderName');
                if (nameEl) {
                    nameEl.textContent = fullName || '—';
                }

                const avatarEl = document.getElementById('profileAvatar');
                if (avatarEl) {
                    const letter = (fullName || '—').trim().charAt(0).toUpperCase();
                    avatarEl.textContent = letter || '—';
                }

                const navNameEl = document.getElementById('internNavbarName');
                if (navNameEl) {
                    navNameEl.textContent = fullName || 'Intern';
                }

                const navAvatarEl = document.getElementById('internNavbarAvatar');
                if (navAvatarEl) {
                    const navLetter = (fullName || 'Intern').trim().charAt(0).toUpperCase();
                    navAvatarEl.textContent = navLetter || 'I';
                }

                const schoolEl = document.getElementById('profileHeaderSchool');
                if (schoolEl) {
                    const left = String(schoolEl.dataset.schoolId ?? '').trim() || '—';
                    const right = String(schoolEl.dataset.schoolName ?? '').trim() || '—';
                    schoolEl.textContent = `${left} | ${right}`;
                }
            }

            function updatePlaceholderState(el) {
                const value = cleanValue(el.textContent);
                if (!value) {
                    el.classList.add('empty-placeholder');
                    if (!el.textContent.trim()) {
                        el.textContent = '—';
                    }
                } else {
                    el.classList.remove('empty-placeholder');
                }
            }

            let pendingEmailElement = null;

            async function saveField(el) {
                const field = el.getAttribute('data-field');
                if (!field) {
                    return;
                }

                const original = el.getAttribute('data-original') ?? '';
                let value = '';
                if (el.matches('select, input')) {
                    value = String(el.value ?? '').trim();
                } else {
                    value = cleanValue(el.textContent);
                }

                if (value === original) {
                    if (!el.matches('select, input')) {
                        updatePlaceholderState(el);
                    }
                    return;
                }

                if (field === 'email') {
                    pendingEmailElement = el;
                    document.getElementById('change_email_new').value = value;
                    document.getElementById('change_email_password').value = '';
                    document.getElementById('changeEmailPasswordError').textContent = '';
                    document.getElementById('change_email_password').classList.remove('is-invalid');
                    const modal = new bootstrap.Modal(document.getElementById('changeEmailModal'));
                    modal.show();
                    return;
                }

                el.classList.add('opacity-50');

                try {
                    const response = await fetch(inlineUpdateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ field, value }),
                    });

                    if (!response.ok) {
                        const data = await response.json().catch(() => null);
                        let message = 'Unable to save changes.';
                        if (data && data.errors && data.errors.value && data.errors.value.length) {
                            message = data.errors.value[0];
                        }
                        alert(message);
                        if (el.matches('select, input')) {
                            el.value = original;
                        } else {
                            el.textContent = original || '—';
                            updatePlaceholderState(el);
                        }
                        return;
                    }

                    const data = await response.json();
                    const newValue = data.value ?? '';
                    if (el.matches('select, input')) {
                        el.value = newValue || '';
                        el.setAttribute('data-original', String(el.value ?? '').trim());
                    } else {
                        el.textContent = newValue || '—';
                        el.setAttribute('data-original', cleanValue(el.textContent));
                        updatePlaceholderState(el);
                    }

                    if (['first_name', 'middle_name', 'last_name'].includes(field)) {
                        refreshProfileHeader();
                    }
                } catch (e) {
                    alert('Network error while saving. Please try again.');
                    if (el.matches('select, input')) {
                        el.value = original;
                    } else {
                        el.textContent = original || '—';
                        updatePlaceholderState(el);
                    }
                } finally {
                    el.classList.remove('opacity-50');
                }
            }

            function showEmailStep1() {
                document.getElementById('changeEmailStep1').classList.remove('d-none');
                document.getElementById('changeEmailStep1Footer').classList.remove('d-none');
                document.getElementById('changeEmailStep2').classList.add('d-none');
            }

            function showEmailStep2() {
                document.getElementById('changeEmailStep1').classList.add('d-none');
                document.getElementById('changeEmailStep1Footer').classList.add('d-none');
                document.getElementById('changeEmailStep2').classList.remove('d-none');
            }

            document.getElementById('changeEmailForm').addEventListener('submit', async function (evt) {
                evt.preventDefault();
                const newEmail = document.getElementById('change_email_new').value.trim();
                const currentPassword = document.getElementById('change_email_password').value;
                const submitBtn = document.getElementById('changeEmailSubmitBtn');
                const errEl = document.getElementById('changeEmailPasswordError');

                errEl.textContent = '';
                document.getElementById('change_email_password').classList.remove('is-invalid');
                if (!currentPassword) {
                    document.getElementById('change_email_password').classList.add('is-invalid');
                    errEl.textContent = 'Current password is required to change your email.';
                    return;
                }

                submitBtn.disabled = true;
                try {
                    const response = await fetch(sendEmailCodeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            new_email: newEmail,
                            current_password: currentPassword,
                        }),
                    });

                    const data = await response.json().catch(() => null);
                    if (!response.ok) {
                        const msg = (data && data.errors && data.errors.current_password && data.errors.current_password[0])
                            ? data.errors.current_password[0]
                            : (data && data.errors && data.errors.new_email && data.errors.new_email[0])
                                ? data.errors.new_email[0]
                                : (data && data.message) ? data.message : 'Unable to send verification code.';
                        document.getElementById('change_email_password').classList.add('is-invalid');
                        errEl.textContent = msg;
                        submitBtn.disabled = false;
                        return;
                    }

                    document.getElementById('changeEmailSentTo').textContent = newEmail;
                    document.querySelectorAll('.email-code-box').forEach(function (b) {
                        b.value = '';
                        b.classList.remove('is-invalid');
                    });
                    document.getElementById('changeEmailCodeError').textContent = '';
                    showEmailStep2();
                    const firstBox = document.querySelector('.email-code-box[data-idx="0"]');
                    if (firstBox) firstBox.focus();
                } catch (e) {
                    errEl.textContent = 'Network error. Please try again.';
                    document.getElementById('change_email_password').classList.add('is-invalid');
                }
                submitBtn.disabled = false;
            });

            document.getElementById('changeEmailBackBtn').addEventListener('click', function () {
                showEmailStep1();
            });

            let emailVerifyInProgress = false;

            async function doVerifyEmailCode(code) {
                const errEl = document.getElementById('changeEmailCodeError');
                const boxes = document.querySelectorAll('.email-code-box');
                errEl.textContent = '';
                boxes.forEach(function (b) { b.classList.remove('is-invalid'); });
                if (code.length !== 6) {
                    errEl.textContent = 'Enter the 6-digit code from your email.';
                    boxes.forEach(function (b) { b.classList.add('is-invalid'); });
                    return;
                }
                if (emailVerifyInProgress) return;
                emailVerifyInProgress = true;
                boxes.forEach(function (b) { b.disabled = true; });
                try {
                    const response = await fetch(verifyEmailCodeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ code: code }),
                    });

                    const data = await response.json().catch(() => null);
                    if (!response.ok) {
                        const msg = (data && data.errors && data.errors.code && data.errors.code[0])
                            ? data.errors.code[0]
                            : (data && data.message) ? data.message : 'Invalid or expired code. Email was not changed.';
                        errEl.textContent = msg;
                        boxes.forEach(function (b) {
                            b.classList.add('is-invalid');
                            b.value = '';
                            b.disabled = false;
                        });
                        if (boxes[0]) boxes[0].focus();
                        emailVerifyInProgress = false;
                        return;
                    }

                    const savedValue = (data.value ?? '').trim();
                    if (pendingEmailElement) {
                        pendingEmailElement.textContent = savedValue || '—';
                        pendingEmailElement.setAttribute('data-original', savedValue);
                        updatePlaceholderState(pendingEmailElement);
                        pendingEmailElement = null;
                    }
                    bootstrap.Modal.getInstance(document.getElementById('changeEmailModal')).hide();
                } catch (e) {
                    errEl.textContent = 'Network error. Please try again.';
                    boxes.forEach(function (b) {
                        b.classList.add('is-invalid');
                        b.disabled = false;
                    });
                }
                emailVerifyInProgress = false;
            }

            (function initEmailCodeBoxes() {
                const container = document.getElementById('changeEmailCodeBoxes');
                if (!container) return;
                const boxes = container.querySelectorAll('.email-code-box');
                boxes.forEach(function (box, i) {
                    box.addEventListener('input', function (e) {
                        const v = e.target.value.replace(/\D/g, '').slice(0, 1);
                        e.target.value = v;
                        if (v.length === 1) {
                            if (i < 5) {
                                boxes[i + 1].focus();
                            } else {
                                const code = Array.from(boxes).map(function (b) { return b.value; }).join('');
                                doVerifyEmailCode(code);
                            }
                        }
                    });
                    box.addEventListener('keydown', function (e) {
                        if (e.key === 'Backspace' && !e.target.value && i > 0) {
                            boxes[i - 1].focus();
                        }
                    });
                    box.addEventListener('paste', function (e) {
                        e.preventDefault();
                        const pasted = (e.clipboardData?.getData('text') || '').replace(/\D/g, '').slice(0, 6);
                        pasted.split('').forEach(function (ch, j) {
                            if (boxes[j]) boxes[j].value = ch;
                        });
                        const idx = Math.min(pasted.length, 5);
                        boxes[idx].focus();
                        if (pasted.length === 6) {
                            doVerifyEmailCode(pasted);
                        }
                    });
                });
            })();

            document.getElementById('changeEmailModal').addEventListener('hidden.bs.modal', function () {
                if (pendingEmailElement) {
                    const original = pendingEmailElement.getAttribute('data-original') ?? '';
                    pendingEmailElement.textContent = original || '—';
                    updatePlaceholderState(pendingEmailElement);
                    pendingEmailElement = null;
                }
                showEmailStep1();
                document.getElementById('change_email_password').value = '';
                document.getElementById('changeEmailPasswordError').textContent = '';
                document.getElementById('change_email_password').classList.remove('is-invalid');
                document.querySelectorAll('.email-code-box').forEach(function (b) {
                    b.value = '';
                    b.disabled = false;
                    b.classList.remove('is-invalid');
                });
                document.getElementById('changeEmailCodeError').textContent = '';
            });

            document.querySelectorAll('.editable-profile-field').forEach(function (el) {
                el.setAttribute('data-original', cleanValue(el.textContent));

                el.addEventListener('focus', function () {
                    if (el.classList.contains('empty-placeholder')) {
                        el.textContent = '';
                    }
                });

                el.addEventListener('blur', function () {
                    saveField(el);
                });

                el.addEventListener('keydown', function (evt) {
                    if (evt.key === 'Enter') {
                        evt.preventDefault();
                        el.blur();
                    }
                });
            });

            document.querySelectorAll('select[data-field], input[data-field]').forEach(function (el) {
                el.addEventListener('change', function () {
                    saveField(el);
                });

                el.addEventListener('blur', function () {
                    saveField(el);
                });
            });

            document.querySelectorAll('[data-birthday-picker]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const input = btn.closest('.input-group')?.querySelector('input[type="date"][data-field="birthday"]');
                    if (!input) {
                        return;
                    }
                    if (typeof input.showPicker === 'function') {
                        input.showPicker();
                    } else {
                        input.focus();
                    }
                });
            });

            refreshProfileHeader();
        });
    </script>
@endsection
