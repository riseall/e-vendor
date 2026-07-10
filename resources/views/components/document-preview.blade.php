@once
    @push('scripts')
        <script>
            (function() {
                'use strict';

                function initDocumentPreview() {
                    if (window.__documentPreviewInit) {
                        return;
                    }
                    window.__documentPreviewInit = true;

                    // Cari atau buat modal element
                    let modalEl = document.getElementById('documentPreviewModal');
                    let containerEl = document.getElementById('documentPreviewContainer');
                    let titleEl = document.getElementById('documentPreviewModalLabel');
                    let openLinkEl = document.getElementById('documentPreviewOpen');

                    // Kalau modal belum ada, buat secara manual
                    if (!modalEl) {
                        console.log('[DocumentPreview] Modal not found, creating...');
                        modalEl = document.createElement('div');
                        modalEl.id = 'documentPreviewModal';
                        modalEl.className = 'modal fade';
                        modalEl.tabIndex = -1;
                        modalEl.setAttribute('role', 'dialog');
                        modalEl.setAttribute('aria-hidden', 'true');
                        modalEl.setAttribute('data-backdrop', 'true');
                        modalEl.innerHTML =
                            '<div class="modal-dialog modal-xl modal-dialog-centered" role="document">' +
                            '<div class="modal-content border-0">' +
                            '<div class="modal-header">' +
                            '<h5 class="modal-title font-weight-bold" id="documentPreviewModalLabel">Preview Dokumen</h5>' +
                            '<button type="button" class="close" data-dismiss="modal" aria-label="Tutup">' +
                            '<i aria-hidden="true" class="ki ki-close"></i>' +
                            '</button>' +
                            '</div>' +
                            '<div class="modal-body p-0 bg-light" style="height: 80vh; position: relative;">' +
                            '<div id="documentPreviewContainer" class="h-100 d-flex align-items-center justify-content-center" style="overflow: auto;"></div>' +
                            '</div>' +
                            '<div class="modal-footer">' +
                            '<a id="documentPreviewOpen" href="#" target="_blank" rel="noopener" class="btn btn-light-primary font-weight-bold">' +
                            '<i class="flaticon2-new-tab"></i> Buka di Tab Baru' +
                            '</a>' +
                            '<button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal">Tutup</button>' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                        document.body.appendChild(modalEl);
                    }

                    containerEl = document.getElementById('documentPreviewContainer');
                    titleEl = document.getElementById('documentPreviewModalLabel');
                    openLinkEl = document.getElementById('documentPreviewOpen');

                    if (!modalEl || !containerEl || !titleEl || !openLinkEl) {
                        console.error('[DocumentPreview] Missing required modal elements');
                        return;
                    }

                    function showLoading() {
                        containerEl.innerHTML = '<div class="spinner spinner-primary spinner-lg"></div>';
                    }

                    function showUnavailable() {
                        containerEl.innerHTML =
                            '<div class="text-center text-muted p-5">' +
                            '<i class="flaticon-file-2 display-1 mb-3"></i><br>' +
                            '<p class="mt-4 mb-0">Dokumen tidak tersedia di preview.</p>' +
                            '<p class="small text-muted mt-2">Klik "Buka di Tab Baru" untuk membuka di browser.</p>' +
                            '</div>';
                    }

                    function showError(msg) {
                        containerEl.innerHTML =
                            '<div class="text-center text-danger p-5">' +
                            '<i class="flaticon-warning display-1 mb-3"></i><br>' +
                            '<p class="mt-4 mb-0">' + msg + '</p>' +
                            '</div>';
                    }

                    function openModal() {
                        if (window.jQuery && window.jQuery(modalEl).modal) {
                            try {
                                window.jQuery(modalEl).modal('show');
                                return;
                            } catch (e) {}
                        }
                        if (window.bootstrap && window.bootstrap.Modal) {
                            const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.show();
                            return;
                        }
                        // Manual fallback - force show
                        modalEl.style.display = 'block';
                        modalEl.style.paddingRight = '0';
                        modalEl.classList.add('show');
                        modalEl.setAttribute('aria-hidden', 'false');
                        modalEl.removeAttribute('aria-hidden');
                        modalEl.setAttribute('aria-modal', 'true');
                        modalEl.setAttribute('role', 'dialog');
                        modalEl.style.zIndex = '1050';
                        document.body.classList.add('modal-open');
                        document.body.style.overflow = 'hidden';
                        document.body.style.paddingRight = '0';
                        let backdrop = document.querySelector('.modal-backdrop');
                        if (!backdrop) {
                            backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop fade show';
                            backdrop.style.zIndex = '1040';
                            document.body.appendChild(backdrop);
                        }
                    }

                    function closeModal() {
                        if (window.jQuery && window.jQuery(modalEl).modal) {
                            try {
                                window.jQuery(modalEl).modal('hide');
                                return;
                            } catch (e) {}
                        }
                        if (window.bootstrap && window.bootstrap.Modal) {
                            const modal = window.bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                            return;
                        }
                        modalEl.classList.remove('show');
                        modalEl.style.display = 'none';
                        modalEl.setAttribute('aria-hidden', 'true');
                        document.body.classList.remove('modal-open');
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();
                    }

                    function handlePreviewClick(button) {
                        const url = button.getAttribute('data-url') || '';
                        const documentTitle = button.getAttribute('data-title') ||
                            button.getAttribute('title') || 'Preview Dokumen';

                        console.log('[DocumentPreview] Click:', {
                            url,
                            documentTitle
                        });

                        titleEl.textContent = documentTitle;
                        if (url) {
                            openLinkEl.setAttribute('href', url);
                            openLinkEl.classList.remove('d-none');
                        } else {
                            openLinkEl.setAttribute('href', '#');
                            openLinkEl.classList.add('d-none');
                        }

                        showLoading();
                        openModal();

                        if (!url) {
                            setTimeout(showUnavailable, 300);
                            return;
                        }

                        const lowerUrl = url.toLowerCase();
                        const isImage = /\.(jpg|jpeg|png|gif|webp|bmp|svg)(\?|$)/i.test(lowerUrl);

                        if (isImage) {
                            setTimeout(() => {
                                containerEl.innerHTML = '';
                                const img = document.createElement('img');
                                img.src = url;
                                img.alt = documentTitle;
                                img.style.maxWidth = '100%';
                                img.style.maxHeight = '100%';
                                img.style.objectFit = 'contain';
                                img.onerror = function() {
                                    showError('Gagal memuat gambar.');
                                };
                                containerEl.appendChild(img);
                            }, 300);
                            return;
                        }

                        setTimeout(() => {
                            containerEl.innerHTML = '';
                            const iframe = document.createElement('iframe');
                            iframe.src = url;
                            iframe.title = documentTitle;
                            iframe.setAttribute('frameborder', '0');
                            iframe.style.width = '100%';
                            iframe.style.height = '100%';
                            iframe.style.border = '0';
                            iframe.style.background = '#fff';
                            iframe.onload = function() {
                                console.log('[DocumentPreview] Iframe loaded');
                            };
                            iframe.onerror = function() {
                                showError('Gagal memuat dokumen. Klik "Buka di Tab Baru".');
                            };
                            containerEl.appendChild(iframe);
                        }, 300);
                    }

                    // Event delegation dengan capture
                    document.addEventListener('click', function(e) {
                        const target = e.target.closest('.btn-preview-doc');
                        if (!target) return;

                        if (target.disabled) {
                            e.preventDefault();
                            return;
                        }

                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        handlePreviewClick(target);
                    }, true);

                    // Close handler
                    document.addEventListener('click', function(e) {
                        if (e.target.closest('[data-dismiss="modal"]')) {
                            const modal = e.target.closest('.modal');
                            if (modal && modal.id === 'documentPreviewModal') {
                                closeModal();
                            }
                        }
                    });

                    modalEl.addEventListener('click', function(e) {
                        if (e.target === modalEl) {
                            closeModal();
                        }
                    });

                    modalEl.addEventListener('hidden.bs.modal', function() {
                        containerEl.innerHTML = '';
                        openLinkEl.setAttribute('href', '#');
                        openLinkEl.classList.remove('d-none');
                        titleEl.textContent = 'Preview Dokumen';
                    });

                    console.log('[DocumentPreview] Ready');
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initDocumentPreview);
                } else {
                    initDocumentPreview();
                }
            })
            ();
        </script>
    @endpush
@endonce
