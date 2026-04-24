<h4 class="font-weight-bold text-dark mb-8">Review & Kirim Permohonan</h4>
<p class="text-muted mb-5">Periksa kembali seluruh data Anda sebelum mengirim ke sistem kami.</p>

<div id="reviewStepsContainer">
</div>

<div class="alert alert-custom alert-light-info fade show mt-8 border-0 shadow-sm">
    <div class="alert-icon"><i class="flaticon2-bell text-info"></i></div>
    <div class="alert-text">
        Dengan menekan <strong>Kirim Permohonan</strong>, Anda menyatakan seluruh data dan dokumen yang diberikan adalah
        <strong>benar dan dapat dipertanggungjawabkan</strong> sesuai hukum yang berlaku.
    </div>
</div>

@push('scripts')
    <script>
        // Mapping nama step agar lebih user-friendly
        const reviewStepNames = {
            'step-1': 'Kategori Bisnis',
            'step-2': 'Informasi Umum',
            'step-3': 'Informasi Pembayaran',
            'step-4': 'Komitmen Vendor',
            'step-5': 'Informasi Tambahan',
            'step-6': 'Informasi Lokal',
            'step-7': 'Daftar Produk',
            'step-8': 'Dokumen Legal',
            'cat-1': 'Detail: Bahan Baku & Alkes',
            'cat-2': 'Detail: Varia Teknik & Reagen',
            'cat-3': 'Detail: Jasa Transporter',
            'cat-4': 'Detail: Kontraktor & Maintenance',
            'cat-5': 'Detail: Pengujian & Kalibrasi',
            'cat-6': 'Detail: Facility & Katering',
            'cat-7': 'Detail: Pelatihan & Konsultan',
            'cat-8': 'Detail: Agency Advertising',
        };

        function generateReview() {
            const container = $('#reviewStepsContainer');
            container.html('<div class="text-center p-10"><div class="spinner spinner-primary"></div></div>');

            let fullHtml = '';
            const activeStepIds = (window.activeSteps || []).map(s => s.id).filter(id => id !== 'review');

            activeStepIds.forEach(stepId => {
                const $stepContent = $(`[data-step-id="${stepId}"]`);
                let stepRowsHtml = '';

                // 1. Ambil semua elemen input di dalam step ini
                // Kita cari .form-group atau wrapper input individu, bukan cuma .question-wrapper utama
                $stepContent.find('input, select, textarea').each(function() {
                    const $el = $(this);

                    // Skip input hidden atau tombol
                    if ($el.attr('type') === 'hidden' || $el.is('button')) return;

                    // Cari label terdekat (biasanya ada di class .form-label atau <label>)
                    // Pada komponen x-vendor-input, label biasanya ada di atas inputnya
                    let label = $el.closest('.form-group').find('label').first().text().replace('*', '')
                        .trim() ||
                        $el.attr('placeholder') ||
                        $el.attr('name');

                    let value = '-';

                    // Handle Radio & Checkbox
                    if ($el.is(':radio') || $el.is(':checkbox')) {
                        // Cari input yang dipilih dalam grup yang sama (name sama)
                        const name = $el.attr('name');
                        if ($(`input[name="${name}"]:checked`).length > 0) {
                            const selectedText = [];
                            $(`input[name="${name}"]:checked`).each(function() {
                                selectedText.push($(this).closest('label').text().trim());
                            });
                            value = selectedText.join(', ');
                            // Tandai agar tidak di-loop ulang untuk name yang sama
                            if ($el.data('reviewed')) return;
                            $(`input[name="${name}"]`).data('reviewed', true);
                        } else {
                            return; // Jangan tampilkan yang tidak dipilih
                        }
                    }
                    // Handle File
                    else if ($el.attr('type') === 'file') {
                        value = this.files.length > 0 ? Array.from(this.files).map(f => f.name).join(', ') :
                            'Belum upload';
                    }
                    // Handle Standard Input
                    else {
                        value = $el.val() || '-';
                    }

                    if (value !== '-' || $el.attr('required')) {
                        stepRowsHtml += `
                        <div class="d-flex align-items-center justify-content-between mb-3 py-2 border-bottom border-light">
                            <span class="text-muted font-weight-bold mr-4 w-40">${label}</span>
                            <span class="text-dark-75 font-weight-bolder text-right w-60">${value}</span>
                        </div>
                    `;
                    }
                });

                // 2. Handle Tabel/Repeater (Alat, Ijin, dll)
                $stepContent.find('table tbody').each(function() {
                    const rowCount = $(this).find('tr:visible').not('.empty-state').length;
                    if (rowCount > 0) {
                        stepRowsHtml += `
                        <div class="d-flex align-items-center justify-content-between mb-3 py-2 bg-light-primary px-3 rounded">
                            <span class="text-primary font-weight-bold">Data Tabel / Daftar</span>
                            <span class="badge badge-primary font-weight-bold">${rowCount} Baris Terisi</span>
                        </div>
                    `;
                    }
                });

                if (stepRowsHtml) {
                    fullHtml += `
                    <div class="card card-custom gutter-b shadow-none border mb-6">
                        <div class="card-header border-0 min-h-50px pt-4 pb-0 d-flex justify-content-between align-items-center">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark font-size-h6">${reviewStepNames[stepId] || stepId}</span>
                                <span class="text-muted mt-1 font-weight-bold font-size-sm">${stepId.startsWith('cat-') ? 'Kategori Khusus' : 'Informasi Dasar'}</span>
                            </h3>
                            <button type="button" class="btn btn-xs btn-light-primary font-weight-bold btn-edit-step" data-target="${stepId}">Ubah</button>
                        </div>
                        <div class="card-body pt-4">
                            ${stepRowsHtml}
                        </div>
                    </div>
                `;
                }
            });

            // Reset data reviewed untuk loop selanjutnya
            $('input').data('reviewed', false);

            container.html(fullHtml || '<div class="alert alert-warning">Belum ada data yang diisi.</div>');
        }

        // Trigger pindah step
        $(document).on('click', '.btn-edit-step', function() {
            const target = $(this).data('target');
            const targetIndex = window.activeSteps.findIndex(s => s.id === target);
            if (targetIndex >= 0) {
                window.currentIndex = targetIndex;
                renderNav();
                renderContent();
                window.scrollTo(0, 0);
            }
        });
    </script>
@endpush

<style>
    .w-40 {
        width: 40%;
    }

    .w-60 {
        width: 60%;
    }

    .card-custom {
        border-radius: 0.85rem !important;
    }

    .btn-edit-step {
        border-radius: 0.5rem;
    }
</style>
