@extends('layouts.app', ['title' => 'Users'])



@section('content')
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">Master User</h3>
            </div>
            <div class="card-toolbar">
                <!--begin::Dropdown-->
                <div class="dropdown dropdown-inline mr-2">
                    <button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="svg-icon svg-icon-md">
                            <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                        fill="#000000" opacity="0.3" />
                                    <path
                                        d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                        fill="#000000" />
                                </g>
                            </svg>
                            <!--end::Svg Icon-->
                        </span>Export</button>
                    <!--begin::Dropdown Menu-->
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                        <!--begin::Navigation-->
                        <ul class="navi flex-column navi-hover py-2">
                            <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">Choose
                                an option:</li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="la la-print"></i>
                                    </span>
                                    <span class="navi-text">Print</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="la la-copy"></i>
                                    </span>
                                    <span class="navi-text">Copy</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="la la-file-excel-o"></i>
                                    </span>
                                    <span class="navi-text">Excel</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="la la-file-text-o"></i>
                                    </span>
                                    <span class="navi-text">CSV</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="la la-file-pdf-o"></i>
                                    </span>
                                    <span class="navi-text">PDF</span>
                                </a>
                            </li>
                        </ul>
                        <!--end::Navigation-->
                    </div>
                    <!--end::Dropdown Menu-->
                </div>
                <!--end::Dropdown-->
                <!--begin::Button-->
                <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder" data-toggle="modal"
                    data-target="#modalTambahUser">
                    <i class="la la-user-plus icon-xl"></i> New User
                </a>
                <!--end::Button-->
            </div>
        </div>

        <div class="modal fade" id="modalTambahUser" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah User Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <form id="formTambahUser">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Cari Pegawai (Internal):</label>
                                <select class="form-control select2" id="select_pegawai" name="employee_id"
                                    style="width: 100%">
                                    <option value="">Ketik Nama atau NIK...</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Pilih Role:</label>
                                <select class="form-control select2" id="select_role" name="role" style="width: 100%">
                                    <option value="Verifikator">Verifikator</option>
                                    <option value="Procurement">Procurement</option>
                                    <option value="QA">QA</option>
                                    <option value="Apoteker">Apoteker</option>
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

        <div class="card-body">
            <!--begin: Datatable-->
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
            <!--end: Datatable-->
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/widgets/select2.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#kt_datatable').DataTable({
                responsive: true,
                // searchDelay: 500,
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
                        render: function(data, type, full, meta) {
                            if (data && data.length > 0) {
                                var badges = '';
                                $.each(data, function(index, role) {
                                    var color = '';

                                    switch (role.name) {
                                        case 'Super Admin':
                                            color =
                                                'label label-danger';
                                            break;
                                        case 'Admin IT':
                                            color =
                                                'label label-dark';
                                            break;
                                        case 'Verifikator':
                                            color =
                                                'label label-danger';
                                            break;
                                        case 'Procurement':
                                            color =
                                                'label label-success';
                                            break;
                                        case 'Quality Assurance':
                                            color =
                                                'label label-info';
                                            break;
                                        case 'Apoteker':
                                            color =
                                                'label label-warning';
                                            break;
                                        case 'Specialist':
                                            color =
                                                'label label-info';
                                            break;
                                        case 'Supplier':
                                            color =
                                                'label label-primary';
                                            break;
                                    }

                                    badges +=
                                        '<span class="label label-lg font-weight-bold ' +
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
                    targets: -1, // Kolom terakhir (Actions)
                    title: 'Actions',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        return `
                            <div class="d-flex justify-content-center">
                                <a href="/users/edit/${data}" class="btn btn-sm btn-icon btn-outline-warning mr-2" title="Edit">
                                    <i class="la la-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-icon btn-outline-danger" title="Delete">
                                    <i class="la la-trash"></i>
                                </button>
                            </div>`;
                    },
                }, ],
            });


            // $('#select_pegawai').select2({
            //     placeholder: "Cari Nama/NIK...",
            //     allowClear: true,
            //     ajax: {
            //         url: "{{ route('user.search') }}",
            //         dataType: 'json',
            //         delay: 250,
            //         data: function(params) {
            //             return {
            //                 term: params.term
            //             };
            //         },
            //         processResults: function(data) {
            //             return {
            //                 results: $.map(data, function(item) {
            //                     return {
            //                         text: item.nik + ' - ' + item.name,
            //                         id: item.id,
            //                         email: item.email // simpan data tambahan jika perlu
            //                     }
            //                 })
            //             };
            //         }
            //     }
            // });

            // // 2. Handle Submit Form
            // $('#formTambahUser').on('submit', function(e) {
            //     e.preventDefault();
            //     let btn = $('#btnSimpan');

            //     btn.addClass('spinner spinner-white spinner-right').attr('disabled', true);

            //     $.ajax({
            //         url: "{{ route('user.store') }}",
            //         method: "POST",
            //         data: $(this).serialize(),
            //         success: function(res) {
            //             $('#modalTambahUser').modal('hide');
            //             Swal.fire("Berhasil!", "User telah ditambahkan.", "success");
            //             table.ajax.reload(); // Refresh datatable
            //         },
            //         error: function(err) {
            //             Swal.fire("Error!", "Terjadi kesalahan saat menyimpan.", "error");
            //         },
            //         complete: function() {
            //             btn.removeClass('spinner spinner-white spinner-right').removeAttr(
            //                 'disabled');
            //         }
            //     });
            // });
        });
    </script>
@endpush
