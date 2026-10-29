<script>
    $(function() {
        const form = document.getElementById('kt_form');
        const submitButton = $('#btn-submit-revision, #btn-submit-rekualifikasi');
        const isRevisionMode = @json($isRevisionMode);

        function revisionContainer(marker) {
            return marker.closest('.doc-item, tr, .list-container, .form-group, .question-wrapper') || marker;
        }

        function scrollToRevision(target) {
            if (!target) {
                return;
            }

            target.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            target.classList.add('revision-focus-pulse');

            window.setTimeout(function() {
                target.classList.remove('revision-focus-pulse');
                const input = target.querySelector(
                    'input:not([type="hidden"]), select, textarea, button');

                if (input && !input.disabled) {
                    input.focus({
                        preventScroll: true
                    });
                }
            }, 700);
        }

        function initializeRevisionHighlights() {
            if (!isRevisionMode) {
                return;
            }

            const markers = Array.from(document.querySelectorAll('.revision-note-message'));

            markers.forEach(function(marker) {
                const container = revisionContainer(marker);
                const section = marker.closest('.vendor-profile-section');

                container.classList.add('revision-field-highlight');
                if (section) {
                    section.classList.add('has-revision');
                }
            });

            document.querySelectorAll('.vendor-profile-section').forEach(function(section) {
                const count = section.querySelectorAll('.revision-note-message').length;
                const navLink = document.querySelector('.vendor-profile-nav a[href="#' + section.id +
                    '"]');

                if (!count || !navLink) {
                    return;
                }

                navLink.classList.add('has-revision');
                const badge = document.createElement('span');
                badge.className = 'revision-nav-count';
                badge.textContent = count;
                navLink.appendChild(badge);
            });

            const firstMarker = markers[0];
            const firstTarget = firstMarker ? revisionContainer(firstMarker) : null;

            $('#btn-first-revision').on('click', function() {
                scrollToRevision(firstTarget);
            });

            if (firstTarget) {
                window.setTimeout(function() {
                    scrollToRevision(firstTarget);
                }, 450);
            }
        }

        initializeRevisionHighlights();

        function showErrors(xhr) {
            const errors = xhr.responseJSON && xhr.responseJSON.errors;

            if (xhr.status === 422 && errors) {
                const messages = Object.values(errors)
                    .flat()
                    .map(message => `<li class="text-left">${$('<div>').text(message).html()}</li>`)
                    .join('');

                Object.keys(errors).forEach(function(field) {
                    form.querySelectorAll(`[name="${field}"], [name^="${field}."]`)
                        .forEach(element => element.classList.add('is-invalid'));
                });

                Swal.fire({
                    icon: 'error',
                    title: @json(__('validation_failed')),
                    html: `<ul class="mb-0 pl-5">${messages}</ul>`
                });
                return;
            }

            Swal.fire({
                icon: 'error',
                title: @json(__('failed')),
                text: xhr.responseJSON?.message || @json(__('data_failed_to_send'))
            });
        }

        function request(url, data) {
            return $.ajax({
                url: url,
                method: 'POST',
                data: data,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }

        submitButton.on('click', function() {
            const button = $(this);
            const originalHtml = button.html();
            const isRekualifikasi = button.attr('id') === 'btn-submit-rekualifikasi';

            Swal.fire({
                icon: 'question',
                title: isRekualifikasi ? @json(__('send_requalification_question')) : @json(__('resubmit_revision_question')),
                text: isRekualifikasi ? @json(__('send_requalification_confirm_desc')) : @json(__('resubmit_revision_confirm_desc')),
                showCancelButton: true,
                confirmButtonText: @json(__('yes_submit')),
                cancelButtonText: @json(__('cancel'))
            }).then(function(result) {
                if (result.isConfirmed || result.value) {
                    Swal.fire({
                        title: isRekualifikasi ? @json(__('sending_requalification')) : @json(__('sending_revision')),
                        text: @json(__('swal_please_wait')),
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        onOpen: function() {
                            Swal.showLoading();
                        },
                        didOpen: function() {
                            Swal.showLoading();
                        }
                    });

                    button.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm mr-2"></span> ' + @json(__('processing'))
                    );

                    form.querySelectorAll('.is-invalid').forEach(element => element.classList
                        .remove('is-invalid'));

                    if (typeof window.prepareProductRowsForSubmit === 'function') {
                        window.prepareProductRowsForSubmit();
                    }

                    const formData = new FormData(form);

                    if (typeof window.restoreProductRowsAfterSubmit === 'function') {
                        window.restoreProductRowsAfterSubmit();
                    }

                    formData.set('action', 'submit');
                    formData.set('application_id', $('#application_id').val());

                    request(@json(route('registrasi.save-umum')), formData)
                        .then(function(response) {
                            formData.set('application_id', response.application_id);
                            return request(@json(route('registrasi.save-specific')), formData);
                        })
                        .then(function(response) {
                            const finalData = new FormData();
                            finalData.set('_token', $('input[name="_token"]').val());
                            finalData.set('application_id', response.application_id || $(
                                '#application_id').val());

                            return request(@json(route('registrasi.submit')), finalData);
                        })
                        .then(function(response) {
                            return Swal.fire({
                                icon: 'success',
                                title: isRekualifikasi ? @json(__('requalification_submitted_title')) : @json(__('revision_submitted_title')),
                                text: response.message
                            }).then(function() {
                                window.location.href = response.redirect;
                            });
                        })
                        .fail(showErrors)
                        .always(function() {
                            button.prop('disabled', false).html(originalHtml);
                        });
                }
            });
        });

        function keepNavInView(link) {
            const nav = link.closest('.vendor-profile-nav');
            if (!nav) return;
            if (window.innerWidth < 768) {
                const navRect = nav.getBoundingClientRect();
                const linkRect = link.getBoundingClientRect();
                const currentScrollLeft = nav.scrollLeft;
                const targetLeft = currentScrollLeft + (linkRect.left - navRect.left) - (nav.clientWidth / 2) + (link.clientWidth / 2);
                nav.scrollTo({
                    left: Math.max(0, targetLeft),
                    behavior: 'smooth'
                });
            } else {
                const navTop = nav.scrollTop;
                const linkTop = link.offsetTop;
                if (linkTop < navTop || linkTop > (navTop + nav.clientHeight - 40)) {
                    nav.scrollTo({
                        top: Math.max(0, linkTop - 50),
                        behavior: 'smooth'
                    });
                }
            }
        }

        // Initialize Scrollspy for Sidebar Navigation
        const sections = document.querySelectorAll('.vendor-profile-section');
        const navLinks = document.querySelectorAll('.vendor-profile-nav a');
        
        if (sections.length > 0 && "IntersectionObserver" in window) {
            const observerOptions = {
                root: null,
                rootMargin: '-15% 0px -65% 0px',
                threshold: 0
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.getAttribute('id');
                        navLinks.forEach(link => {
                            if (link.getAttribute('href') === '#' + id) {
                                if (!link.classList.contains('active')) {
                                    navLinks.forEach(l => l.classList.remove('active'));
                                    link.classList.add('active');
                                    keepNavInView(link);
                                }
                            }
                        });
                    }
                });
            }, observerOptions);
            
            sections.forEach(section => observer.observe(section));
        }

        // Smooth scroll and active state on nav link click
        $('.vendor-profile-nav a').on('click', function(e) {
            const targetId = $(this).attr('href');
            if (targetId && targetId.startsWith('#')) {
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                    keepNavInView(this);
                    if (history.pushState) {
                        history.pushState(null, null, targetId);
                    }
                }
            }
        });
    });
</script>
