@extends('layouts.app', ['title' => 'Users'])

@section('content')
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">Master User</h3>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-primary font-weight-bolder" data-toggle="modal"
                    data-target="#modalTambahUser">
                    <i class="la la-user-plus icon-xl"></i> User Baru
                </button>
            </div>
        </div>

        <!-- Modal Tambah User -->
        <div class="modal fade" id="modalTambahUser" data-backdrop="static" tabindex="-1" role="dialog"
            aria-labelledby="modalTambahUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahUserLabel">Tambah User Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <form id="formTambahUser">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Nama User"
                                    required />
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" placeholder="Username / NIK"
                                    required />
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="Email User"
                                    required />
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="Password"
                                    required />
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Pilih Role</label>
                                <select class="form-control select2" id="select_role" name="role" style="width: 100%">
                                    <option value="">-- Pilih Role --</option>
                                    @if (isset($roles))
                                        @foreach ($roles as $r)
                                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold"
                                data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary font-weight-bold" id="btnSimpan">Simpan
                                User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit User -->
        <div class="modal fade" id="modalEditUser" data-backdrop="static" tabindex="-1" role="dialog"
            aria-labelledby="modalEditUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditUserLabel">Edit Data User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <form id="formEditUser">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_user_id" name="user_id" />
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" id="edit_name" name="name" class="form-control" required />
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Username <span class="text-danger">*</span></label>
                                <input type="text" id="edit_username" name="username" class="form-control"
                                    required />
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" id="edit_email" name="email" class="form-control" required />
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Password Baru <span
                                        class="text-muted font-size-xs">(Kosongkan jika tidak diubah)</span></label>
                                <input type="password" id="edit_password" name="password" class="form-control"
                                    placeholder="Password baru..." />
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Pilih Role</label>
                                <select class="form-control select2" id="edit_select_role" name="role"
                                    style="width: 100%">
                                    <option value="">-- Pilih Role --</option>
                                    @if (isset($roles))
                                        @foreach ($roles as $r)
                                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold"
                                data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary font-weight-bold" id="btnUpdateUser">Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-striped table-hover table-checkable" id="kt_datatable">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/widgets/select2.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#kt_datatable').DataTable({
                responsive: true,
                processing: false,
                serverSide: false,
                ajax: {
                    url: "{{ route('user.data') }}",
                    type: 'GET',
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'roles',
                        className: 'text-center',
                        title: 'Roles',
                        render: function(data) {
                            if (data && data.length > 0) {
                                var badges = '';
                                $.each(data, function(index, role) {
                                    var color = 'label label-info';
                                    switch (role.name) {
                                        case 'Super Admin':
                                            color = 'label label-danger';
                                            break;
                                        case 'Admin IT':
                                            color = 'label label-dark';
                                            break;
                                        case 'Verifikator':
                                            color = 'label label-danger';
                                            break;
                                        case 'Procurement':
                                            color = 'label label-success';
                                            break;
                                        case 'Quality Assurance':
                                            color = 'label label-info';
                                            break;
                                        case 'Apoteker':
                                            color = 'label label-warning';
                                            break;
                                        case 'Supplier':
                                            color = 'label label-primary';
                                            break;
                                    }
                                    badges +=
                                        '<span class="label label-lg font-weight-bold label-' +
                                        color + ' label-inline mr-1">' + role.name +
                                        '</span>';
                                });
                                return badges;
                            }
                            return '<span class="text-muted">No Role</span>';
                        }
                    },
                    {
                        data: 'is_active',
                        className: 'text-center',
                        render: function(data) {
                            return data == 1 ?
                                '<div><span class="label label-success label-dot mr-2"></span><span class="font-weight-bold text-success">Aktif</span></div>' :
                                '<div><span class="label label-danger label-dot mr-2"></span><span class="font-weight-bold text-danger">Non Aktif</span></div>';
                        }
                    },
                    {
                        data: 'id',
                        responsivePriority: -1
                    },
                ],
                columnDefs: [{
                    targets: -1,
                    title: 'Actions',
                    orderable: false,
                    render: function(data, type, full) {
                        var roleName = (full.roles && full.roles.length) ? full.roles[0].name :
                            '';
                        return `
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-sm btn-icon btn-outline-warning mr-2 btn-edit-user" 
                                    data-id="${data}" 
                                    data-name="${full.name || ''}" 
                                    data-username="${full.username || ''}" 
                                    data-email="${full.email || ''}" 
                                    data-role="${roleName}" 
                                    title="Edit">
                                    <i class="la la-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-outline-danger btn-delete-user" 
                                    data-id="${data}" 
                                    data-name="${full.name || ''}" 
                                    title="Delete">
                                    <i class="la la-trash"></i>
                                </button>
                            </div>`;
                    },
                }],
            });

            // Handle Create User
            $('#formTambahUser').on('submit', function(e) {
                e.preventDefault();
                let btn = $('#btnSimpan');
                btn.addClass('spinner spinner-white spinner-right').attr('disabled', true);

                $.ajax({
                    url: "{{ route('user.store') }}",
                    method: "POST",
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#modalTambahUser').modal('hide');
                        $('#formTambahUser')[0].reset();
                        Swal.fire("Berhasil!", "User telah ditambahkan.", "success");
                        table.ajax.reload();
                    },
                    error: function(err) {
                        let msg = "Terjadi kesalahan saat menyimpan.";
                        if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        Swal.fire("Error!", msg, "error");
                    },
                    complete: function() {
                        btn.removeClass('spinner spinner-white spinner-right').removeAttr(
                            'disabled');
                    }
                });
            });

            // Open Edit Modal
            $(document).on('click', '.btn-edit-user', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let username = $(this).data('username');
                let email = $(this).data('email');
                let role = $(this).data('role');

                $('#edit_user_id').val(id);
                $('#edit_name').val(name);
                $('#edit_username').val(username);
                $('#edit_email').val(email);
                $('#edit_password').val('');
                $('#edit_select_role').val(role).trigger('change');

                $('#modalEditUser').modal('show');
            });

            // Handle Update User
            $('#formEditUser').on('submit', function(e) {
                e.preventDefault();
                let id = $('#edit_user_id').val();
                let btn = $('#btnUpdateUser');
                btn.addClass('spinner spinner-white spinner-right').attr('disabled', true);

                $.ajax({
                    url: "/user/" + id,
                    method: "POST",
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#modalEditUser').modal('hide');
                        Swal.fire("Berhasil!", "Data user telah diperbarui.", "success");
                        table.ajax.reload();
                    },
                    error: function(err) {
                        let msg = "Terjadi kesalahan saat memperbarui user.";
                        if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        Swal.fire("Error!", msg, "error");
                    },
                    complete: function() {
                        btn.removeClass('spinner spinner-white spinner-right').removeAttr(
                            'disabled');
                    }
                });
            });

            // Handle Delete User
            $(document).on('click', '.btn-delete-user', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');

                Swal.fire({
                    title: 'Hapus User?',
                    text: 'Apakah Anda yakin ingin menghapus user "' + name + '"?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/user/" + id,
                            type: 'DELETE',
                            dataType: 'json',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                Swal.fire('Terhapus!', res.message ||
                                    'User berhasil dihapus.', 'success');
                                table.ajax.reload();
                            },
                            error: function() {
                                Swal.fire('Error!', 'Gagal menghapus user.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
