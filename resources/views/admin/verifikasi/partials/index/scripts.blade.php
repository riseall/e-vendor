    <script>
        $(function() {
            var table = $('#tbl-permohonan').DataTable({
                pageLength: 15,
                lengthMenu: [10, 15, 25, 50],
                order: [
                    [4, 'desc']
                ], // default sort: tgl submit terbaru
                language: {
                    search: '',
                    searchPlaceholder: 'Cari di tabel…',
                    lengthMenu: 'Tampilkan _MENU_ baris',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(difilter dari _MAX_ total)',
                    zeroRecords: 'Tidak ada hasil yang cocok',
                    paginate: {
                        first: '«',
                        last: '»',
                        next: '›',
                        previous: '‹',
                    }
                },
                columnDefs: [{
                        orderable: false,
                        targets: -1
                    }, // kolom Aksi tidak bisa sort
                ],
                // Sembunyikan built-in search DataTables (kita sudah ada filter sendiri di header)
                dom: '<"d-flex justify-content-between align-items-center mb-4"lf>rtip',
                initComplete: function() {
                    // Pindah built-in search DT ke toolbar kita (opsional — bisa dihapus)
                    // Jika ingin pakai DT search terpisah, uncomment baris bawah:
                    // $('#tbl-permohonan_filter').prependTo('.vp-filter');
                }
            });

            // ── Sinkronisasi filter server-side (q & status) dengan DT search ──
            // Ketik di filter header → filter DT juga (untuk filter di halaman saat ini)
            $('#filterQ').on('keyup', function() {
                table.search($(this).val()).draw();
            });
            $('#filterStatus').on('change', function() {
                var val = $(this).val();
                if (val === 'all') {
                    table.column(3).search('').draw();
                } else {
                    // Search kolom Status berdasar text badge
                    var labelMap = {
                        'submitted': 'Submitted',
                        'need_revision': 'Need Revision',
                        'verified': 'Verified',
                    };
                    table.column(3).search(labelMap[val] || '').draw();
                }
            });
        });
    </script>
