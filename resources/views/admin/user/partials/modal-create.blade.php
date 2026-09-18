<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalTambahUserLabel">Tambah User Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="formTambahUser">
                @csrf
                <div class="modal-body">
                    <!-- Toggle Jenis Akun Menggunakan Component vendor-radio -->
                    <div class="mb-4">
                        <x-vendor-radio name="user_type" label="Jenis Akun" :options="[
                            ['value' => 'internal', 'label' => 'Karyawan Internal'],
                            ['value' => 'supplier', 'label' => 'Supplier Eksternal'],
                        ]" selected="internal"
                            radioClass="user-type-radio" />
                    </div>

                    <!-- PANE: Karyawan Internal -->
                    <div id="pane_internal">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Cari Karyawan <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="select_internal_user" style="width: 100%">
                                <option value="">Ketik minimal 2 huruf nama atau NIK...</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-vendor-input name="name" id="internal_name" label="Nama Lengkap"
                                    placeholder="Otomatis terisi" required readonly class="bg-light"
                                    labelClass="font-weight-bold" leftIcon="fas fa-user text-muted" />
                            </div>
                            <div class="col-md-6">
                                <x-vendor-input name="username" id="internal_username" label="Username / NIK"
                                    placeholder="Otomatis terisi" required readonly class="bg-light"
                                    labelClass="font-weight-bold" leftIcon="fas fa-id-card text-muted" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-vendor-input name="email" id="internal_email" type="email" label="Email"
                                    placeholder="Otomatis terisi" required readonly class="bg-light"
                                    labelClass="font-weight-bold" leftIcon="fas fa-envelope text-muted" />
                            </div>
                            <div class="col-md-6">
                                <x-vendor-select name="role" id="select_role" label="Pilih Role" :options="isset($roles) ? $roles->pluck('name', 'name')->toArray() : []"
                                    placeholder="-- Pilih Role --" :isSimple="true" class="select2"
                                    labelClass="font-weight-bold" />
                            </div>
                        </div>
                    </div>

                    <!-- PANE: Supplier Eksternal -->
                    <div id="pane_supplier" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <x-vendor-input name="supplier_name" id="supplier_name" label="Nama Perusahaan / Vendor"
                                    placeholder="Nama Perusahaan / Vendor" labelClass="font-weight-bold"
                                    leftIcon="fas fa-building text-muted" />
                            </div>
                            <div class="col-md-6">
                                <x-vendor-input name="supplier_username" id="supplier_username" label="Username"
                                    placeholder="Username untuk login" labelClass="font-weight-bold"
                                    leftIcon="fas fa-user text-muted" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-vendor-input name="supplier_email" id="supplier_email" type="email" label="Email"
                                    placeholder="Email aktif vendor" labelClass="font-weight-bold"
                                    leftIcon="fas fa-envelope text-muted" />
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="font-weight-bold mb-0">Password Akun <span
                                                class="text-danger">*</span></label>
                                        <button type="button"
                                            class="btn btn-xs btn-light-primary font-weight-bolder py-0 px-2"
                                            id="btn_generate_password">
                                            <i class="fas fa-dice mr-1"></i> Generate
                                        </button>
                                    </div>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="fas fa-lock text-muted"></i></span>
                                        </div>
                                        <input type="text" name="password" id="supplier_password"
                                            class="form-control" placeholder="Password (min. 6 karakter)"
                                            autocomplete="off" />
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary border" type="button"
                                                id="btn_copy_password" title="Salin Password">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0 mt-2">
                            <label class="font-weight-bold">Role Akun</label>
                            <div>
                                <span class="label label-lg label-light-primary label-inline font-weight-bold">
                                    Supplier
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light-danger font-weight-bold"
                        data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="btnSimpan">Simpan
                        User</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Select2 Role Create
            $('#select_role').select2({
                placeholder: '-- Pilih Role --',
                dropdownParent: $('#modalTambahUser'),
                width: '100%'
            });

            // Select2 AJAX for DB Master internal user search
            $('#select_internal_user').select2({
                placeholder: 'Ketik minimal 2 huruf nama atau NIK...',
                allowClear: true,
                dropdownParent: $('#modalTambahUser'),
                width: '100%',
                ajax: {
                    url: "{{ route('user.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results || []
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });

            // On employee selected from DB Master
            $('#select_internal_user').on('select2:select', function(e) {
                var data = e.params.data;
                $('#internal_name').val(data.nama || '');
                $('#internal_username').val(data.nik || '');
                $('#internal_email').val(data.email || '');

                if (data.suggested_role) {
                    $('#select_role').val(data.suggested_role).trigger('change');
                }
            });

            $('#select_internal_user').on('select2:clear', function() {
                $('#internal_name').val('');
                $('#internal_username').val('');
                $('#internal_email').val('');
                $('#select_role').val('').trigger('change');
            });

            // User Type Switcher logic
            function switchUserType(type) {
                if (type === 'internal') {
                    $('#pane_internal').show();
                    $('#pane_supplier').hide();

                    $('#supplier_password').removeAttr('required').val('');
                    $('#supplier_name').removeAttr('required').val('');
                    $('#supplier_username').removeAttr('required').val('');
                    $('#supplier_email').removeAttr('required').val('');
                } else {
                    $('#pane_internal').hide();
                    $('#pane_supplier').show();

                    $('#supplier_password').attr('required', true);
                    $('#supplier_name').attr('required', true);
                    $('#supplier_username').attr('required', true);
                    $('#supplier_email').attr('required', true);
                }
            }

            $('input[name="user_type"]').on('change', function() {
                switchUserType($(this).val());
            });

            $('#btn_switch_to_supplier').on('click', function(e) {
                e.preventDefault();
                $('input[name="user_type"][value="supplier"]').prop('checked', true).trigger('change');
            });

            // Password Generator
            $('#btn_generate_password').on('click', function() {
                var charsUpper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
                var charsLower = 'abcdefghijkmnpqrstuvwxyz';
                var charsNum = '23456789';
                var charsSpecial = '!@#$%&*';

                var pass = 'Vnd';
                for (var i = 0; i < 2; i++) pass += charsUpper.charAt(Math.floor(Math.random() * charsUpper
                    .length));
                for (var i = 0; i < 2; i++) pass += charsLower.charAt(Math.floor(Math.random() * charsLower
                    .length));
                for (var i = 0; i < 2; i++) pass += charsNum.charAt(Math.floor(Math.random() * charsNum
                    .length));
                pass += charsSpecial.charAt(Math.floor(Math.random() * charsSpecial.length));

                $('#supplier_password').val(pass);
                if (typeof showToast === 'function') {
                    showToast('info', 'Password Dibuat', 'Password acak terisi: ' + pass);
                }
            });

            // Password Copy
            $('#btn_copy_password').on('click', function() {
                var pass = $('#supplier_password').val();
                if (!pass) {
                    if (typeof showToast === 'function') {
                        showToast('warning', 'Perhatian', 'Password masih kosong.');
                    }
                    return;
                }
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(pass).then(function() {
                        if (typeof showToast === 'function') {
                            showToast('success', 'Tersalin',
                                'Password berhasil disalin ke clipboard.');
                        }
                    }).catch(function() {
                        copyFallback(pass);
                    });
                } else {
                    copyFallback(pass);
                }
            });

            function copyFallback(text) {
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(text).select();
                document.execCommand('copy');
                $temp.remove();
                if (typeof showToast === 'function') {
                    showToast('success', 'Tersalin', 'Password berhasil disalin ke clipboard.');
                }
            }

            // Reset modal on close
            $('#modalTambahUser').on('hidden.bs.modal', function() {
                $('#formTambahUser')[0].reset();
                $('input[name="user_type"][value="internal"]').prop('checked', true);
                switchUserType('internal');
                $('#select_internal_user').val(null).trigger('change');
                $('#select_role').val('').trigger('change');
            });

            // Handle Create User Submit
            $('#formTambahUser').on('submit', function(e) {
                e.preventDefault();
                var userType = $('input[name="user_type"]:checked').val();

                if (userType === 'internal' && !$('#internal_username').val()) {
                    if (typeof showToast === 'function') {
                        showToast('warning', 'Peringatan',
                            'Silakan pilih karyawan internal dari hasil pencarian DB Master terlebih dahulu.'
                        );
                    }
                    return;
                }

                var btn = $('#btnSimpan');
                btn.addClass('spinner spinner-white spinner-right').attr('disabled', true);

                var postData = {
                    _token: "{{ csrf_token() }}",
                    user_type: userType
                };

                if (userType === 'internal') {
                    postData.name = $('#internal_name').val();
                    postData.username = $('#internal_username').val();
                    postData.email = $('#internal_email').val();
                    postData.role = $('#select_role').val();
                } else {
                    postData.name = $('#supplier_name').val();
                    postData.username = $('#supplier_username').val();
                    postData.email = $('#supplier_email').val();
                    postData.password = $('#supplier_password').val();
                }

                $.ajax({
                    url: "{{ route('user.store') }}",
                    method: "POST",
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: postData,
                    success: function(res) {
                        $('#modalTambahUser').modal('hide');
                        $('#formTambahUser')[0].reset();
                        if (typeof showToast === 'function') {
                            showToast('success', 'Berhasil', res.message ||
                                'User berhasil ditambahkan.');
                        }
                        if ($.fn.DataTable.isDataTable('#kt_datatable')) {
                            $('#kt_datatable').DataTable().ajax.reload();
                        }
                    },
                    error: function(err) {
                        var msg = "Terjadi kesalahan saat menyimpan.";
                        if (err.responseJSON && err.responseJSON.errors) {
                            var errs = err.responseJSON.errors;
                            var firstKey = Object.keys(errs)[0];
                            msg = errs[firstKey][0];
                        } else if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        if (typeof showToast === 'function') {
                            showToast('error', 'Gagal', msg);
                        }
                    },
                    complete: function() {
                        btn.removeClass('spinner spinner-white spinner-right').removeAttr(
                            'disabled');
                    }
                });
            });
        });
    </script>
@endpush
