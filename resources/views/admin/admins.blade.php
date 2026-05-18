@extends('layouts.admin')

@section('title', 'Admin Management')

@section('content')
    <div class="content-header d-flex justify-content-between align-items-start">
        <div>
            <h1><i class="bi bi-shield-check"></i> Admin Management</h1>
            <p class="text-muted mb-0">Manage administrator accounts</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adminModal" id="addAdminBtn">
                <i class="bi bi-plus-circle"></i> Add Admin
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($admins->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $admin)
                                <tr>
                                    <td class="fw-semibold">{{ $admin->name }}</td>
                                    <td>{{ $admin->email }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-warning edit-admin-btn"
                                            data-id="{{ $admin->id }}"
                                            data-name="{{ $admin->name }}"
                                            data-email="{{ $admin->email }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        @if($admin->id !== Auth::id())
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-admin-btn" data-id="{{ $admin->id }}" data-name="{{ $admin->name }}">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        @else
                                            <span class="text-muted small">(current)</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted"><i class="bi bi-shield-x"></i> No admin accounts yet. Add one above.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Add/Edit Admin Modal -->
    <div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminModalLabel">Add Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="adminForm" method="POST" action="{{ route('admin.admin.store') }}">
                    @csrf
                    <div id="adminFormMethod" class="d-none"></div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="adminName" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="adminName" name="name" value="{{ old('name') }}" required maxlength="255">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="adminEmail" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="adminEmail" name="email" value="{{ old('email') }}" required maxlength="255">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3" id="adminPasswordGroup">
                            <label for="adminPassword" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="adminPassword" name="password" minlength="8">
                            <small class="text-muted">Min 8 characters. Leave blank when editing to keep current.</small>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3" id="adminPasswordConfGroup">
                            <label for="adminPasswordConf" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="adminPasswordConf" name="password_confirmation" minlength="8">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const modal = new bootstrap.Modal(document.getElementById('adminModal'));
            const form = document.getElementById('adminForm');
            const formMethod = document.getElementById('adminFormMethod');
            const modalLabel = document.getElementById('adminModalLabel');
            const nameInput = document.getElementById('adminName');
            const emailInput = document.getElementById('adminEmail');
            const passwordInput = document.getElementById('adminPassword');
            const passwordConfInput = document.getElementById('adminPasswordConf');
            const passwordGroup = document.getElementById('adminPasswordGroup');
            const passwordConfGroup = document.getElementById('adminPasswordConfGroup');

            document.getElementById('addAdminBtn').addEventListener('click', function() {
                modalLabel.textContent = 'Add Admin';
                form.action = '{{ route('admin.admin.store') }}';
                form.method = 'POST';
                formMethod.innerHTML = '';
                nameInput.value = '';
                emailInput.value = '';
                passwordInput.value = '';
                passwordInput.required = true;
                passwordConfInput.value = '';
                passwordConfInput.required = true;
            });

            document.querySelectorAll('.edit-admin-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const email = this.dataset.email;
                    modalLabel.textContent = 'Edit Admin';
                    form.action = '{{ url('admin/admin') }}/' + id;
                    form.method = 'POST';
                    formMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                    nameInput.value = name;
                    emailInput.value = email;
                    passwordInput.value = '';
                    passwordInput.required = false;
                    passwordConfInput.value = '';
                    passwordConfInput.required = false;
                    modal.show();
                });
            });

            document.querySelectorAll('.delete-admin-btn').forEach(function(btn) {
                btn.addEventListener('click', async function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const result = await Swal.fire({
                        icon: 'warning',
                        title: 'Delete admin?',
                        text: 'Remove "' + name + '"? This cannot be undone.',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Yes, delete'
                    });
                    if (!result.isConfirmed) return;
                    try {
                        const res = await fetch('{{ url('admin/admin') }}/' + id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json().catch(function() { return {}; });
                        if (!res.ok) {
                            await Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Delete failed', showConfirmButton: true });
                            return;
                        }
                        await Swal.fire({ icon: 'success', title: 'Success', text: 'Admin removed', timer: 2000, timerProgressBar: true, showConfirmButton: true });
                        location.reload();
                    } catch (err) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Error deleting admin', showConfirmButton: true });
                    }
                });
            });
        });
    </script>
@endsection
