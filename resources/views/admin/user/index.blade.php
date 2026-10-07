@extends('layouts.app', ['title' => 'Users'])

@section('content')
    {{-- Flash container untuk SweetAlert --}}
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fas fa-users text-primary"></i>
                </span>
                <h3 class="card-label">Master User</h3>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-primary font-weight-bolder" data-toggle="modal"
                    data-target="#modalTambahUser">
                    <i class="fas fa-plus mr-1"></i> User Baru
                </button>
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
                        <th class="text-nowrap">Roles</th>
                        <th class="text-nowrap">Status</th>
                        <th class="text-nowrap">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modals Partial -->
    @include('admin.user.partials.modal-create')
    @include('admin.user.partials.modal-edit')
@endsection

@push('scripts')
    <script src="{{ asset('js/widgets/select2.js') }}"></script>
    <script>
        // Shared toast notification helper standard vnd-swal-toast
        function showToast(type, title, message) {
            var iconClass = 'fa-check text-success';
            var btnClass = 'btn-outline-success';
            if (type === 'error') {
                iconClass = 'fa-times text-danger';
                btnClass = 'btn-outline-danger';
            } else if (type === 'warning') {
                iconClass = 'fa-exclamation text-warning';
                btnClass = 'btn-outline-warning';
            } else if (type === 'info') {
                iconClass = 'fa-info text-info';
                btnClass = 'btn-outline-info';
            }

            Swal.fire({
                html: '<div class="vnd-swal-toast-body">' +
                    '<div class="btn btn-icon ' + btnClass + ' btn-circle btn-sm m-0">' +
                    '<i class="fas ' + iconClass + '" style="font-size:1rem;"></i>' +
                    '</div>' +
                    '<div class="vnd-swal-toast-content">' +
                    '<div class="vnd-swal-toast__title">' + (title || 'Notifikasi') + '</div>' +
                    '<div class="vnd-swal-toast__text">' + message + '</div>' +
                    '</div>' +
                    '</div>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                showCloseButton: true,
                timer: 3500,
                timerProgressBar: true,
                width: 360,
                padding: '0',
                customClass: {
                    popup: 'vnd-swal-toast shadow-sm',
                    closeButton: 'vnd-swal-toast__close',
                },
            });
        }

        $(document).ready(function() {
            // Flash Session helper jika ada redirect with flash message
            (function() {
                var $el = $('#vnd-flash');
                if (!$el.length) return;
                var messages = [
                    { key: 'success', type: 'success', title: 'Sukses' },
                    { key: 'error', type: 'error', title: 'Gagal' },
                    { key: 'warning', type: 'warning', title: 'Peringatan' },
                    { key: 'info', type: 'info', title: 'Informasi' },
                ];
                messages.forEach(function(m) {
                    var msg = $el.data(m.key);
                    if (msg) {
                        showToast(m.type, m.title, msg);
                    }
                });
            })();

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
                        className: 'text-center text-nowrap',
                        title: 'Roles',
                        render: function(data) {
                            if (data && data.length > 0) {
                                var badges = '';
                                $.each(data, function(index, role) {
                                    var color = 'label-info';
                                    switch (role.name) {
                                        case 'Super Admin':
                                            color = 'label-danger';
                                            break;
                                        case 'Admin IT':
                                            color = 'label-dark';
                                            break;
                                        case 'Verifikator':
                                            color = 'label-danger';
                                            break;
                                        case 'Procurement':
                                            color = 'label-success';
                                            break;
                                        case 'Quality Assurance':
                                            color = 'label-info';
                                            break;
                                        case 'Apoteker':
                                            color = 'label-warning';
                                            break;
                                        case 'Supplier':
                                            color = 'label-primary';
                                            break;
                                    }
                                    badges +=
                                        '<span class="label label-lg font-weight-bold ' +
                                        color + ' label-inline text-nowrap mr-1">' + role.name +
                                        '</span>';
                                });
                                return badges;
                            }
                            return '<span class="text-muted">No Role</span>';
                        }
                    },
                    {
                        data: 'is_active',
                        className: 'text-center text-nowrap',
                        render: function(data) {
                            return data == 1 ?
                                '<div><span class="label label-success label-dot mr-2"></span><span class="font-weight-bold text-success">Aktif</span></div>' :
                                '<div><span class="label label-danger label-dot mr-2"></span><span class="font-weight-bold text-danger">Non Aktif</span></div>';
                        }
                    },
                    {
                        data: 'id',
                        responsivePriority: -1,
                        className: 'text-center text-nowrap',
                        orderable: false,
                        render: function(data, type, full) {
                            var roleName = (full.roles && full.roles.length) ? full.roles[0].name : '';
                            return `
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-sm btn-icon btn-outline-warning mr-2 btn-edit-user" 
                                        data-id="${data}" 
                                        data-name="${full.name || ''}" 
                                        data-username="${full.username || ''}" 
                                        data-email="${full.email || ''}" 
                                        data-role="${roleName}" 
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-outline-danger btn-delete-user" 
                                        data-id="${data}" 
                                        data-name="${full.name || ''}" 
                                        title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>`;
                        },
                    }],
            });

            // Handle Delete User
            $(document).on('click', '.btn-delete-user', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

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
                            url: "{{ route('user.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            dataType: 'json',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                showToast('success', 'Terhapus', res.message || 'User berhasil dihapus.');
                                table.ajax.reload();
                            },
                            error: function() {
                                showToast('error', 'Gagal', 'Gagal menghapus user.');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
