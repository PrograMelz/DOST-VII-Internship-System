@extends('layouts.admin')

@section('title', 'Interns')

@section('extra-css')
    <style>
        .btn-add { background: var(--button-color); color: white; border-color: var(--button-color); }
        .btn-add:hover { background: var(--button-color); border-color: var(--button-color); filter: brightness(0.9); }
        .intern-name { font-weight: 600; }

        /* Intern profile-style modal (mirrors interns/profile.blade.php) */
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
        .qr-display-card-header {
            background: var(--secondary-color, #3498DB);
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 0.75rem 1rem;
            min-width: 80px;
            border-radius: 12px;
            color: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="content-header d-flex justify-content-between align-items-start">
        <div>
            <h1><i class="bi bi-people"></i> All Interns</h1>
            <p class="text-muted mb-0">Total: {{ $interns->total() }} interns</p>
        </div>
        <div>
            <button type="button" class="btn btn-success btn-add me-2" data-bs-toggle="modal" data-bs-target="#internModal">
                <i class="bi bi-plus-circle"></i> Add Intern
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="row g-3 mb-4" method="GET" action="{{ route('admin.interns-list') }}">
                <div class="col-md-6">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="Search by name, email, or school..." 
                        value="{{ request('search') }}"
                    >
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>

            @if($interns->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th hidden>School ID</th>
                                <th>School Name</th>
                                <th hidden>Email</th>
                                <th hidden>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($interns as $intern)
                                <tr>
                                    <td class="intern-name">{{ $intern->full_name }}</td>
                                    <td hidden>{{ $intern->school_id }}</td>
                                    <td>{{ $intern->school_name }}</td>
                                    <td hidden>{{ $intern->email }}</td>
                                    <td hidden>{{ $intern->phone_number ?? 'N/A' }}</td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary me-1 view-btn"
                                            data-id="{{ $intern->id }}"
                                        >
                                            <i class="bi bi-eye"></i> Details
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info me-1 qr-btn"
                                            data-qr="{{ $intern->qr_code }}"
                                            data-name="{{ $intern->first_name }} {{ $intern->last_name }}">
                                            <i class="bi bi-qr-code"></i> QR
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $intern->id }}">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Page navigation" class="mt-4">
                    {{ $interns->links('pagination::bootstrap-5') }}
                </nav>
            @else
                <div class="text-center py-5">
                    <p class="text-muted"><i class="bi bi-inbox"></i> No match record for an intern.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- View/Edit Intern Modal -->
    <div class="modal fade" id="internViewModal" tabindex="-1" aria-labelledby="internViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="internViewModalLabel">Intern Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="internViewLoading" class="text-center py-5">
                        <div class="spinner-border" role="status" aria-hidden="true"></div>
                        <div class="text-muted mt-2">Loading intern details...</div>
                    </div>

                    <div id="internViewContent" class="d-none">
                        <form id="adminInternEditForm">
                            <input type="hidden" id="adminInternId" />

                            <!-- Profile header (interns/profile.blade.php style) -->
                            <div class="card profile-header-card mb-4">
                                <div class="card-body d-flex align-items-center gap-4 flex-wrap py-4">
                                    <div class="profile-avatar" id="adminProfileAvatar">—</div>
                                    <div class="flex-grow-1">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-12 col-md-4">
                                                <input type="text"
                                                       class="form-control form-control-sm"
                                                       id="adminFirstNameInput"
                                                       placeholder="First name"
                                                       required>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <input type="text"
                                                       class="form-control form-control-sm"
                                                       id="adminMiddleNameInput"
                                                       placeholder="Middle name">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <input type="text"
                                                       class="form-control form-control-sm"
                                                       id="adminLastNameInput"
                                                       placeholder="Last name"
                                                       required>
                                            </div>
                                        </div>
                                        <p class="mb-0 opacity-90 small mt-2">
                                            <span id="adminProfileHeaderSchoolId">—</span>
                                            |
                                            <span id="adminProfileHeaderSchoolName">—</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Personal Information (60%) -->
                                <div class="col-12 col-lg-8">
                                    <div class="card profile-section-card h-100">
                                        <div class="card-header">
                                            <i class="bi bi-person-lines-fill"></i> Personal information
                                        </div>
                                        <div class="card-body">
                                            <div class="profile-detail-row">
                                                <div class="profile-detail-icon"><i class="bi bi-person-badge"></i></div>
                                                <div class="flex-grow-1">
                                                    <div class="profile-detail-value row g-2 align-items-start">
                                                        <div class="col-12 col-md-2">
                                                            <label class="profile-detail-label mb-1">Sex</label>
                                                            <select class="form-select form-select-sm" id="adminSexInput">
                                                                <option value="male">Male</option>
                                                                <option value="female">Female</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-md-3">
                                                            <label class="profile-detail-label mb-1">Birthday</label>
                                                            <input type="date"
                                                                   class="form-control form-control-sm"
                                                                   id="adminBirthdayInput"
                                                                   required>
                                                        </div>
                                                        <div class="col-12 col-md-2">
                                                            <label class="profile-detail-label mb-1">OJT hours</label>
                                                            <input type="number"
                                                                   min="0"
                                                                   max="65535"
                                                                   class="form-control form-control-sm"
                                                                   id="adminOjtHoursInput"
                                                                   required>
                                                        </div>
                                                        <div class="col-12 col-md-2">
                                                            <label class="profile-detail-label mb-1">School ID</label>
                                                            <input type="text"
                                                                   class="form-control form-control-sm"
                                                                   id="adminSchoolIdInput"
                                                                   required>
                                                        </div>

                                                        <div class="col-12 col-md-3">
                                                            <label class="profile-detail-label mb-1">Phone number</label>
                                                            <input type="text"
                                                                   class="form-control form-control-sm"
                                                                   id="adminPhoneInput">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="profile-detail-row">
                                                <div class="profile-detail-icon"><i class="bi bi-telephone"></i></div>
                                                <div class="flex-grow-1">
                                                    <div class="profile-detail-value row g-2 align-items-start">
                                                        <div class="col-12 col-md-5">
                                                            <label class="profile-detail-label mb-1">Email</label>
                                                            <input type="email"
                                                                   class="form-control form-control-sm"
                                                                   id="adminEmailInput"
                                                                   required>
                                                        </div>    
                                                        <div class="col-12 col-md-7">
                                                            <label class="profile-detail-label mb-1">School name</label>
                                                            <input type="text"
                                                                   class="form-control form-control-sm"
                                                                   id="adminSchoolNameInput">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Account & Contacts (40%) -->
                                <div class="col-12 col-lg-4">
                                    <div class="card profile-section-card h-100">
                                        <div class="card-header">
                                            <i class="bi bi-key"></i> Account
                                        </div>
                                        <div class="card-body">
                                            <div class="profile-detail-row">
                                                <div class="profile-detail-icon"><i class="bi bi-person-circle"></i></div>
                                                <div class="flex-grow-1">
                                                    <div class="profile-detail-label">Username</div>
                                                    <input type="text"
                                                           class="form-control form-control-sm"
                                                           id="adminUsernameInput"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="profile-detail-row">
                                                <div class="profile-detail-icon"><i class="bi bi-shield-lock"></i></div>
                                                <div class="flex-grow-1">
                                                    <div class="profile-detail-label">New password</div>
                                                    <input type="password"
                                                           class="form-control form-control-sm"
                                                           id="adminPasswordInput"
                                                           placeholder="Leave blank to keep current password">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="adminSaveInternBtn">
                        <i class="bi bi-save me-1"></i> Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal for Add/Edit Intern -->
    <div class="modal fade" id="internModal" tabindex="-1" aria-labelledby="internModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="internModalLabel">Add Intern</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="internForm">
                    <div class="modal-body">
                        <input type="hidden" id="internId" />
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="firstName" placeholder="First Name" required />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="middleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middleName" placeholder="Middle Name" />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="lastName" placeholder="Last Name" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="schoolId" class="form-label">School ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="schoolId" placeholder="School ID" required />
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="schoolName" class="form-label">School Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="schoolName" placeholder="School Name" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" placeholder="Email" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phoneNumber" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phoneNumber" placeholder="Phone Number" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="birthday" class="form-label">Birthday <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="birthday" required />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sex" class="form-label">Sex <span class="text-danger">*</span></label>
                                <select class="form-select" id="sex" required>
                                    <option value="">Select Sex</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ojtHoursRequired" class="form-label">OJT Hours<span class="text-danger">*</span></label>
                                <input type="number" min="0" max="65535" class="form-control" id="ojtHoursRequired" placeholder="e.g. 486" required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <!-- Intern name displayed above the code -->
                    <h5 id="qrInternName" hidden></h5>
                    <div id="qrCodeContainer">
                        <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid" style="max-width: 100%; height: auto;" />
                    </div>
                    <!-- Raw text value of the QR code (useful for copying) -->
                    <p id="qrCodeText" hidden></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const modal = new bootstrap.Modal(document.getElementById('internModal'));
            const viewModalEl = document.getElementById('internViewModal');
            const viewModal = new bootstrap.Modal(viewModalEl);
            const modalTitle = document.getElementById('internModalLabel');
            const internForm = document.getElementById('internForm');
            const internFormSaveButton = internForm ? internForm.querySelector('button[type="submit"]') : null;
            const firstNameInput = document.getElementById('firstName');
            const middleNameInput = document.getElementById('middleName');
            const lastNameInput = document.getElementById('lastName');
            const schoolIdInput = document.getElementById('schoolId');
            const schoolNameInput = document.getElementById('schoolName');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phoneNumber');
            const birthdayInput = document.getElementById('birthday');
            const sexInput = document.getElementById('sex');
            const ojtHoursRequiredInput = document.getElementById('ojtHoursRequired');

            const internViewLoading = document.getElementById('internViewLoading');
            const internViewContent = document.getElementById('internViewContent');
            const adminSaveInternBtn = document.getElementById('adminSaveInternBtn');

            const adminInternIdInput = document.getElementById('adminInternId');
            const adminFirstNameInput = document.getElementById('adminFirstNameInput');
            const adminMiddleNameInput = document.getElementById('adminMiddleNameInput');
            const adminLastNameInput = document.getElementById('adminLastNameInput');
            const adminHeaderSchoolId = document.getElementById('adminProfileHeaderSchoolId');
            const adminHeaderSchoolName = document.getElementById('adminProfileHeaderSchoolName');
            const adminSexInput = document.getElementById('adminSexInput');
            const adminBirthdayInput = document.getElementById('adminBirthdayInput');
            const adminOjtHoursInput = document.getElementById('adminOjtHoursInput');
            const adminSchoolIdInput = document.getElementById('adminSchoolIdInput');
            const adminSchoolNameInput = document.getElementById('adminSchoolNameInput');
            const adminUsernameInput = document.getElementById('adminUsernameInput');
            const adminEmailInput = document.getElementById('adminEmailInput');
            const adminPhoneInput = document.getElementById('adminPhoneInput');
            const adminPasswordInput = document.getElementById('adminPasswordInput');

            function safeText(el, value) {
                el.textContent = (value === null || value === undefined || value === '') ? '—' : String(value);
            }

            function titleCase(str) {
                if (!str) return '';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            async function openInternViewModal(id) {
                internViewLoading.classList.remove('d-none');
                internViewContent.classList.add('d-none');
                document.getElementById('internViewModalLabel').textContent = 'Intern Details';
                viewModal.show();

                try {
                    const res = await fetch(`/admin/intern/${id}`, {
                        method: 'GET',
                        headers: { 'Accept': 'application/json' },
                    });
                    const json = await res.json().catch(() => ({}));
                    if (!res.ok || !json.ok) {
                        throw new Error(json.message || 'Failed to load intern details');
                    }

                    const intern = json.intern || {};

                    document.getElementById('internViewModalLabel').textContent = `${intern.full_name || 'Intern'} - Details & Edit`;

                    // Header avatar
                    const avatarLetter = String(intern.full_name || '—').trim().charAt(0).toUpperCase() || '—';
                    safeText(document.getElementById('adminProfileAvatar'), avatarLetter);

                    // Populate editable fields
                    adminInternIdInput.value = intern.id || '';
                    adminFirstNameInput.value = intern.first_name ?? '';
                    adminMiddleNameInput.value = intern.middle_name ?? '';
                    adminLastNameInput.value = intern.last_name ?? '';

                    adminSchoolIdInput.value = intern.school_id ?? '';
                    adminSchoolNameInput.value = intern.school_name ?? '';
                    adminHeaderSchoolId.textContent = adminSchoolIdInput.value || '—';
                    adminHeaderSchoolName.textContent = adminSchoolNameInput.value || '—';

                    adminSexInput.value = (intern.sex ?? '').toLowerCase() === 'female' ? 'female' : 'male';
                    adminBirthdayInput.value = intern.birthday ?? '';
                    adminOjtHoursInput.value = intern.ojt_hours_required ?? 0;

                    adminUsernameInput.value = intern.username ?? '';
                    adminEmailInput.value = intern.email ?? '';
                    adminPhoneInput.value = intern.phone_number ?? '';
                    adminPasswordInput.value = '';

                    internViewLoading.classList.add('d-none');
                    internViewContent.classList.remove('d-none');
                } catch (err) {
                    console.error(err);
                    internViewLoading.classList.add('d-none');
                    internViewContent.classList.add('d-none');
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Error loading intern details', showConfirmButton: true });
                    viewModal.hide();
                }
            }

            // Add button
            document.querySelector('[data-bs-target="#internModal"]').addEventListener('click', function() {
                modalTitle.textContent = 'Add Intern';
                firstNameInput.value = '';
                middleNameInput.value = '';
                lastNameInput.value = '';
                schoolIdInput.value = '';
                schoolNameInput.value = '';
                emailInput.value = '';
                phoneInput.value = '';
                birthdayInput.value = '';
                sexInput.value = '';
                ojtHoursRequiredInput.value = '0';
            });

            // Delete buttons
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const result = await Swal.fire({
                        icon: 'warning',
                        title: 'Delete intern?',
                        text: 'Are you sure you want to delete this intern? This cannot be undone.',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Yes, delete'
                    });
                    if (!result.isConfirmed) return;
                    const id = this.dataset.id;
                    try {
                        const res = await fetch(`/admin/intern/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                        });
                        if (!res.ok) {
                            const j = await res.json().catch(() => ({}));
                            await Swal.fire({ icon: 'error', title: 'Delete failed', text: j.message || 'Delete failed', showConfirmButton: true });
                            return;
                        }
                        await Swal.fire({ icon: 'success', title: 'Success', text: 'Intern removed', timer: 2000, timerProgressBar: true, showConfirmButton: true });
                        location.reload();
                    } catch (err) {
                        console.error(err);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Error deleting intern', showConfirmButton: true });
                    }
                });
            });

            // Form submit
            internForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (internFormSaveButton) {
                    internFormSaveButton.disabled = true;
                    internFormSaveButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
                }

                Swal.fire({
                    title: 'Saving intern...',
                    text: 'Please wait while we save the intern and send the email.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const payload = {
                    first_name: firstNameInput.value,
                    middle_name: middleNameInput.value,
                    last_name: lastNameInput.value,
                    school_id: schoolIdInput.value,
                    school_name: schoolNameInput.value,
                    email: emailInput.value,
                    phone_number: phoneInput.value,
                    birthday: birthdayInput.value,
                    sex: sexInput.value,
                    ojt_hours_required: ojtHoursRequiredInput.value,
                };
                try {
                    const res = await fetch(`/admin/interns`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(payload),
                    });
                    const json = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        Swal.close();
                        await Swal.fire({ icon: 'error', title: 'Save failed', text: json.message || 'Failed to add intern', showConfirmButton: true });
                        return;
                    }
                    Swal.close();
                    await Swal.fire({ icon: 'success', title: 'Success', text: 'Intern added', timer: 2000, timerProgressBar: true, showConfirmButton: true });
                    modal.hide();
                    location.reload();
                } catch (err) {
                    console.error(err);
                    Swal.close();
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Error saving intern', showConfirmButton: true });
                }

                if (internFormSaveButton) {
                    internFormSaveButton.disabled = false;
                    internFormSaveButton.innerHTML = 'Save';
                }
            });

            // QR Code buttons
            document.querySelectorAll('.qr-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const qrCode = this.dataset.qr;
                    const name = this.dataset.name;

                    // Generate QR code URL (fixed logo image)
                    const qrUrl = `https://quickchart.io/qr?text=${encodeURIComponent(qrCode)}&dark=1c3f72&size=500&centerImageUrl=https%3A%2F%2Fi.ibb.co%2FhR6GWNn9%2Flogo.png`;

                    // Update modal content
                    document.getElementById('qrModalLabel').textContent = `${name}'s QR Code`;
                    document.getElementById('qrCodeImage').src = qrUrl;
                    document.getElementById('qrInternName').textContent = name;
                    document.getElementById('qrCodeText').textContent = qrCode;

                    // Show modal
                    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
                    qrModal.show();
                });
            });

            // View buttons (open modal)
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    openInternViewModal(this.dataset.id);
                });
            });

            // Save changes from view/edit modal
            if (adminSaveInternBtn) {
                adminSaveInternBtn.addEventListener('click', async function () {
                    const id = adminInternIdInput.value;
                    if (!id) {
                        return;
                    }

                    const payload = {
                        first_name: adminFirstNameInput.value,
                        middle_name: adminMiddleNameInput.value,
                        last_name: adminLastNameInput.value,
                        username: adminUsernameInput.value,
                        school_id: adminSchoolIdInput.value,
                        school_name: adminSchoolNameInput.value,
                        email: adminEmailInput.value,
                        phone_number: adminPhoneInput.value,
                        birthday: adminBirthdayInput.value,
                        sex: adminSexInput.value,
                        ojt_hours_required: adminOjtHoursInput.value,
                        password: adminPasswordInput.value || null,
                    };

                    try {
                        const res = await fetch(`/admin/intern/${id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify(payload),
                        });
                        const json = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            await Swal.fire({
                                icon: 'error',
                                title: 'Save failed',
                                text: json.message || 'Failed to update intern',
                                showConfirmButton: true
                            });
                            return;
                        }

                        await Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Intern updated',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: true
                        });
                        viewModal.hide();
                        location.reload();
                    } catch (err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving intern changes',
                            showConfirmButton: true
                        });
                    }
                });
            }
        });
    </script>
@endsection
