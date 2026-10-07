@extends('layouts.app', ['title' => 'Role & Permission Management'])

@section('content')
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fas fa-user-shield text-primary"></i>
                </span>
                <h3 class="card-label">Master Role & Permission</h3>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-primary font-weight-bolder" data-toggle="modal" data-target="#modalTambahRole">
                    <i class="fas fa-plus mr-1"></i> Role Baru
                </button>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-custom alert-light-success fade show mb-5" role="alert">
                    <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="alert-text">{{ session('success') }}</div>
                    <div class="alert-close">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="ki ki-close"></i></span>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
                    <div class="alert-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="alert-text">{{ session('error') }}</div>
                    <div class="alert-close">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="ki ki-close"></i></span>
                        </button>
                    </div>
                </div>
            @endif

            <div class="row">
                @foreach ($roles as $role)
                    <div class="col-md-6 col-lg-4 mb-5">
                        <div class="card card-custom card-border card-shadowless h-100">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title font-weight-bolder text-dark">
                                    {{ $role->name }}
                                </h3>
                                <div class="card-toolbar">
                                    @if (!in_array($role->name, ['Super Admin', 'Admin IT']))
                                        <button class="btn btn-sm btn-icon btn-light-danger btn-delete-role" data-id="{{ $role->id }}" data-name="{{ $role->name }}" title="Hapus Role">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body pt-2">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="text-dark-50 font-weight-bold mr-2">Jumlah User:</span>
                                    <span class="label label-inline label-light-info font-weight-bold">{{ $role->users_count }} User</span>
                                </div>
                                <div class="d-flex align-items-center mb-4">
                                    <span class="text-dark-50 font-weight-bold mr-2">Akses Permission:</span>
                                    <span class="label label-inline label-light-success font-weight-bold">{{ $role->permissions->count() }} Permission</span>
                                </div>

                                <div class="separator separator-dashed mb-4"></div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-sm btn-light-primary font-weight-bolder btn-manage-permissions" data-id="{{ $role->id }}" data-name="{{ $role->name }}">
                                        <i class="fas fa-key mr-1"></i> Kelola Permission
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal Tambah Role -->
    <div class="modal fade" id="modalTambahRole" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalTambahRoleLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahRoleLabel"><i class="fas fa-user-plus mr-2 text-primary"></i>Tambah Role Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <form action="{{ route('role.store') }}" method="POST" id="formTambahRole">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Manager QA" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary font-weight-bold">Simpan Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Assign Permission -->
    <div class="modal fade" id="modalAssignPermission" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalAssignPermissionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAssignPermissionLabel">
                        <i class="fas fa-shield-alt mr-2 text-primary"></i>Kelola Permission: <span id="targetRoleName" class="text-primary font-weight-bolder"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <form id="formAssignPermission">
                    @csrf
                    <input type="hidden" id="targetRoleId" name="role_id" />
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <span class="text-muted font-size-sm">Aktifkan permission yang diizinkan untuk role ini.</span>
                            <div>
                                <button type="button" class="btn btn-xs btn-outline-primary mr-1" id="btnSelectAllGlobal"><i class="fas fa-check-double mr-1"></i>Pilih Semua</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary" id="btnUnselectAllGlobal"><i class="fas fa-times mr-1"></i>Hapus Semua</button>
                            </div>
                        </div>

                        <div id="permissionMatrixContainer">
                            @foreach ($groupedPermissions as $moduleName => $permissions)
                                <div class="card card-custom border mb-4 shadow-none">
                                    <div class="card-header bg-light py-2 min-h-40px">
                                        <div class="card-title my-0">
                                            <h6 class="font-weight-bolder text-dark mb-0 font-size-sm">
                                                <i class="fas fa-layer-group text-primary mr-2"></i>{{ $moduleName }}
                                            </h6>
                                        </div>
                                        <div class="card-toolbar my-0">
                                            <button type="button" class="btn btn-xs btn-link text-primary btn-select-module" data-module="{{ Str::slug($moduleName) }}">
                                                Toggle Modul Ini
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body py-3">
                                        <div class="row">
                                            @foreach ($permissions as $permission)
                                                <div class="col-md-6 mb-3">
                                                    <div class="d-flex align-items-center justify-content-between p-2 rounded bg-hover-light">
                                                        <span class="font-weight-bold text-dark-75 font-size-sm">{{ $permission->name }}</span>
                                                        <span class="switch switch-outline switch-icon switch-success switch-sm">
                                                            <label>
                                                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="perm-checkbox perm-module-{{ Str::slug($moduleName) }}" />
                                                                <span></span>
                                                            </label>
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary font-weight-bold" id="btnSavePermissions">
                            <i class="fas fa-save mr-1"></i> Simpan Permission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Open Manage Permissions Modal
            $('.btn-manage-permissions').on('click', function() {
                let roleId = $(this).data('id');
                let roleName = $(this).data('name');

                $('#targetRoleId').val(roleId);
                $('#targetRoleName').text(roleName);

                // Uncheck all switches initially
                $('.perm-checkbox').prop('checked', false);

                // Fetch assigned permissions for this role
                $.ajax({
                    url: "{{ route('role.permissions.get', ':id') }}".replace(':id', roleId),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let assigned = response.assigned || [];
                            $.each(assigned, function(index, permName) {
                                $('.perm-checkbox[value="' + permName + '"]').prop('checked', true);
                            });
                            $('#modalAssignPermission').modal('show');
                        } else {
                            Swal.fire('Error', 'Gagal mengambil data permission.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                    }
                });
            });

            // Handle Submit Form Assign Permission
            $('#formAssignPermission').on('submit', function(e) {
                e.preventDefault();
                let roleId = $('#targetRoleId').val();
                let btn = $('#btnSavePermissions');

                btn.addClass('spinner spinner-white spinner-right').attr('disabled', true);

                $.ajax({
                    url: "{{ route('role.permissions.update', ':id') }}".replace(':id', roleId),
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response && response.success) {
                            $('#modalAssignPermission').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', (response && response.message) ? response.message : 'Gagal menyimpan permission.', 'error');
                        }
                    },
                    error: function(err) {
                        let msg = 'Terjadi kesalahan saat menyimpan permission.';
                        if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    },
                    complete: function() {
                        btn.removeClass('spinner spinner-white spinner-right').removeAttr('disabled');
                    }
                });
            });

            // Toggle Module Checkboxes
            $('.btn-select-module').on('click', function() {
                let moduleSlug = $(this).data('module');
                let checkboxes = $('.perm-module-' + moduleSlug);
                let allChecked = checkboxes.length === checkboxes.filter(':checked').length;
                checkboxes.prop('checked', !allChecked);
            });

            // Select All Global
            $('#btnSelectAllGlobal').on('click', function() {
                $('.perm-checkbox').prop('checked', true);
            });

            // Unselect All Global
            $('#btnUnselectAllGlobal').on('click', function() {
                $('.perm-checkbox').prop('checked', false);
            });

            // Handle Delete Role
            $('.btn-delete-role').on('click', function() {
                let roleId = $(this).data('id');
                let roleName = $(this).data('name');

                Swal.fire({
                    title: 'Hapus Role?',
                    text: 'Apakah Anda yakin ingin menghapus role "' + roleName + '"?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('role.destroy', ':id') }}".replace(':id', roleId),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire('Terhapus!', res.message, 'success').then(function() {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Gagal!', res.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Gagal menghapus role.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
