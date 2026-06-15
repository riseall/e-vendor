@once
    <div class="modal fade" id="documentPreviewModal" tabindex="-1" role="dialog"
        aria-labelledby="documentPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="documentPreviewModalLabel">Preview Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body p-0 bg-light" style="height: 80vh;">
                    <div id="documentPreviewContainer"
                        class="h-100 d-flex align-items-center justify-content-center">
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="documentPreviewOpen" href="#" target="_blank" rel="noopener"
                        class="btn btn-light-primary font-weight-bold">
                        <i class="flaticon2-new-tab"></i> Buka di Tab Baru
                    </a>
                    <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function() {
                const modal = $('#documentPreviewModal');
                const container = $('#documentPreviewContainer');
                const title = $('#documentPreviewModalLabel');
                const openLink = $('#documentPreviewOpen');

                function showLoading() {
                    container.html('<div class="spinner spinner-primary spinner-lg"></div>');
                }

                function showUnavailable() {
                    container.html(
                        '<div class="text-center text-muted">' +
                        '<i class="flaticon-file-2 display-1"></i>' +
                        '<p class="mt-4 mb-0">Dokumen tidak tersedia.</p>' +
                        '</div>'
                    );
                }

                $(document)
                    .off('click.documentPreview', '.btn-preview-doc')
                    .on('click.documentPreview', '.btn-preview-doc', function(event) {
                        event.preventDefault();

                        const button = $(this);
                        const url = button.data('url');
                        const documentTitle = button.data('title') || button.attr('title') ||
                            'Preview Dokumen';

                        title.text(documentTitle);
                        openLink.attr('href', url || '#').toggleClass('d-none', !url);
                        showLoading();
                        modal.modal('show');

                        if (!url) {
                            showUnavailable();
                            return;
                        }

                        const frame = $('<iframe>', {
                            src: url,
                            title: documentTitle,
                            frameborder: 0,
                            class: 'w-100 h-100 border-0'
                        }).css('background', '#fff');

                        container.empty().append(frame);
                    });

                modal.on('hidden.bs.modal.documentPreview', function() {
                    container.empty();
                    openLink.attr('href', '#').removeClass('d-none');
                    title.text('Preview Dokumen');
                });
            });
        </script>
    @endpush
@endonce
