<!-- Modal Edit User -->
<div class="modal fade" id="modalEditUser" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalEditUserLabel">Edit Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="formEditUser">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id" name="user_id" />
                <div class="modal-body">
                    <x-vendor-input name="name" id="edit_name" label="Nama Lengkap" required
                        labelClass="font-weight-bold" leftIcon="fas fa-user text-muted" />

                    <x-vendor-input name="username" id="edit_username" label="Username" required
                        labelClass="font-weight-bold" leftIcon="fas fa-id-card text-muted" />

                    <x-vendor-input name="email" id="edit_email" type="email" label="Email" required
                        labelClass="font-weight-bold" leftIcon="fas fa-envelope text-muted" />

                    <x-vendor-input name="password" id="edit_password" type="password"
                        label="Password Baru (Kosongkan jika tidak diubah)" placeholder="Password baru..."
                        labelClass="font-weight-bold" leftIcon="fas fa-lock text-muted" />

                    <div class="form-group mb-0">
                        <x-vendor-select name="role" id="edit_select_role" label="Pilih Role"
                            :options="isset($roles) ? $roles->pluck('name', 'name')->toArray() : []"
                            placeholder="-- Pilih Role --" :isSimple="true" class="select2"
                            labelClass="font-weight-bold" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="btnUpdateUser">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Select2 Role Edit
        $('#edit_select_role').select2({
            placeholder: '-- Pilih Role --',
            dropdownParent: $('#modalEditUser'),
            width: '100%'
        });

        // Open Edit Modal
        $(document).on('click', '.btn-edit-user', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var username = $(this).data('username');
            var email = $(this).data('email');
            var role = $(this).data('role');

            $('#edit_user_id').val(id);
            $('#edit_name').val(name);
            $('#edit_username').val(username);
            $('#edit_email').val(email);
            $('#edit_password').val('');
            $('#edit_select_role').val(role).trigger('change');

            $('#modalEditUser').modal('show');
        });

        // Handle Update User Submit
        $('#formEditUser').on('submit', function(e) {
            e.preventDefault();
            var id = $('#edit_user_id').val();
            var btn = $('#btnUpdateUser');
            btn.addClass('spinner spinner-white spinner-right').attr('disabled', true);

            $.ajax({
                url: "{{ route('user.update', ':id') }}".replace(':id', id),
                method: "POST",
                dataType: 'json',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                data: $(this).serialize(),
                success: function(res) {
                    $('#modalEditUser').modal('hide');
                    if (typeof showToast === 'function') {
                        showToast('success', 'Berhasil', res.message || 'Data user telah diperbarui.');
                    }
                    if ($.fn.DataTable.isDataTable('#kt_datatable')) {
                        $('#kt_datatable').DataTable().ajax.reload();
                    }
                },
                error: function(err) {
                    var msg = "Terjadi kesalahan saat memperbarui user.";
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
                    btn.removeClass('spinner spinner-white spinner-right').removeAttr('disabled');
                }
            });
        });
    });
</script>
@endpush
