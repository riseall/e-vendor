    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('success')),
                timer: 1800,
                showConfirmButton: false
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: {!! json_encode(
                    collect($errors->all())->map(function ($error) {
                            return '<div class="text-left">' . e($error) . '</div>';
                        })->implode(''),
                ) !!}
            });
        @endif

        function refreshVerificationContent() {
            var activeTabId = $('#verificationMainCard .nav-link.active').attr('href');

            return $.ajax({
                url: window.location.href,
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function(html) {
                try {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(html, 'text/html');
                    var newHeaderEl = doc.querySelector('#verificationHeader');
                    var newMainCardEl = doc.querySelector('#verificationMainCard');

                    if (newHeaderEl) {
                        $('#verificationHeader').replaceWith(newHeaderEl);
                    }

                    if (newMainCardEl) {
                        $('#verificationMainCard').replaceWith(newMainCardEl);

                        if (activeTabId) {
                            var tabBtn = document.querySelector('#verificationMainCard .nav-link[href="' + activeTabId + '"]');
                            if (tabBtn) {
                                $(tabBtn).tab('show');
                                var navWrap = document.querySelector('.tab-nav-wrap .nav');
                                if (navWrap) {
                                    navWrap.scrollLeft = tabBtn.offsetLeft - 20;
                                }
                            }
                        }
                    }
                } catch (err) {
                    console.error('Error updating verification content:', err);
                    window.location.reload();
                }
            }).catch(function(err) {
                console.error('Failed to fetch verification content:', err);
                window.location.reload();
            });
        }

        function submitVerificationAction($form, successTitle) {
            Swal.fire({
                title: '',
                html: '<div class="swal-verif-loading">' +
                    '<div class="swal-verif-loader" aria-hidden="true"></div>' +
                    '<div class="swal-verif-loading-text">Memproses' +
                    '<span class="swal-verif-loading-dots"><span></span><span></span><span></span></span>' +
                    '</div>' +
                    '</div>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false
            });

            return $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function(response) {
                return refreshVerificationContent().then(function() {
                    Swal.fire({
                        icon: 'success',
                        title: successTitle || 'Berhasil',
                        text: response.message || 'Data verifikasi berhasil diperbarui.',
                        timer: 1600,
                        showConfirmButton: false
                    });
                });
            }).catch(function(xhrOrErr) {
                console.error('Verification action failed:', xhrOrErr);
                var response = (xhrOrErr && xhrOrErr.responseJSON) ? xhrOrErr.responseJSON : {};
                var message = response.message || 'Gagal memproses data verifikasi.';

                if (response.errors) {
                    message = Object.keys(response.errors).map(function(key) {
                        return response.errors[key].join('<br>');
                    }).join('<br>');

                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: message
                    });
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: message
                });
            });
        }

        // Gunakan AJAX supaya keputusan verifikasi tidak reload halaman penuh.
        $(document).on('submit', '.js-confirm-approve', function(e) {
            e.preventDefault();
            var $form = $(this);
            var label = $form.data('label') || 'item ini';

            Swal.fire({
                title: 'Setujui Data?',
                html: 'Anda akan menyetujui <strong>' + label +
                    '</strong>.<br>Keputusan masih bisa diubah setelahnya.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setuju',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#10b981',
                reverseButtons: true,
            }).then(function(result) {
                if (result.isConfirmed || result.value) {
                    submitVerificationAction($form, 'Disetujui');
                }
            });
        });

        // Inject action + label ke modal reject
        $('#modalRejectItem').on('show.bs.modal', function(event) {
            var btn = $(event.relatedTarget);
            $('#formRejectItem').attr('action', btn.data('action'));
            $('#rejectItemLabel').text(btn.data('label') || '-');
            $('#formRejectItem textarea[name="note"]').val('');

            var $sourcePanel = btn.closest('.verification-panel');
            if (!$sourcePanel.length) {
                $sourcePanel = btn.closest('.specific-category-header').nextAll('.verification-panel').first();
            }

            var fields = [];
            $sourcePanel.find('.js-revision-field').each(function() {
                var field = String($(this).data('field') || '').trim();
                var label = String($(this).data('label') || field).trim();

                if (!field || fields.some(function(item) {
                        return item.field === field;
                    })) {
                    return;
                }

                fields.push({
                    field: field,
                    label: label
                });
            });

            var fieldHtml = '';
            fields.forEach(function(item, index) {
                fieldHtml +=
                    '<label class="reject-field-option">' +
                    '<input type="checkbox" name="revision_fields[]" value="' + $('<div>').text(item.field)
                    .html() + '">' +
                    '<span>' +
                    $('<div>').text(item.label).html() +
                    '</span>' +
                    '<input type="hidden" name="revision_labels[' + $('<div>').text(item.field).html() +
                    ']" value="' + $('<div>').text(item.label).html() + '">' +
                    '</label>';
            });

            $('#rejectFieldList').html(fieldHtml ||
                '<div class="text-muted font-size-sm">Tidak ada field yang dapat dipilih.</div>');
        });

        $('#formRejectItem').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);

            if ($form.find('input[name="revision_fields[]"]:checked').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Field Revisi',
                    text: 'Pilih minimal satu field atau dokumen yang harus diperbaiki vendor.'
                });
                return;
            }

            submitVerificationAction($form, 'Tidak Disetujui').then(function() {
                $('#modalRejectItem').modal('hide');
                $('#formRejectItem textarea[name="note"]').val('');
                $('#rejectFieldList').empty();
            });
        });

        // Hide scrollbar tapi masih bisa scroll (tab nav)
        document.querySelector('.nav-tabs')?.addEventListener('wheel', function(e) {
            e.preventDefault();
            this.scrollLeft += e.deltaY;
        }, {
            passive: false
        });
    </script>
