@extends('layouts.app', ['title' => 'Detail Audit'])

@section('breadcrumb', 'Quality Assurance')
@section('step', 'Detail Audit')
@section('page_title', 'Detail Audit Vendor')
@section('page_desc', 'Pantau progres audit, verifikasi hasil, dan ambil keputusan akhir.')

@push('style')
    @include('admin.verifikasi.partials.show.styles')
    <style>
        .audit-info-panel {
            background-color: var(--surface-1);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-light);
        }

        .audit-info-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .audit-info-value {
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.1rem;
        }
    </style>
@endpush

@php
    use App\Models\VendorAudit;

    $vendorName =
        optional(optional($application)->general)->nama_perusahaan ??
        (optional(optional($application)->user)->name ?? '—');
    $vendorEmail =
        optional(optional($application)->general)->email_perusahaan ??
        (optional(optional($application)->user)->email ?? '—');

    $isOnDesk = $audit->isOnDesk();

    $categoryLabels = $application->categories
        ->pluck('category_id')
        ->map(fn($id) => \App\Models\VendorApplication::CATEGORY_LABELS[$id] ?? $id)
        ->implode(', ');
@endphp

@section('content')
    <div id="vnd-flash" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-warning="{{ session('warning') }}" data-info="{{ session('info') }}" hidden></div>

    {{-- ════ HEADER ════ --}}
    <div class="card verif-header-card mb-5">
        <div class="card-body py-6 px-7">
            <div class="d-flex flex-wrap justify-content-between align-items-start">
                <div class="d-flex align-items-start">
                    <a href="{{ route('qa.audit.index') }}" class="btn btn-icon btn-sm btn-light-primary mr-5 mt-1"
                        title="Kembali ke daftar">
                        <i class="ki ki-arrow-back icon-sm"></i>
                    </a>
                    <div>
                        <div class="text-muted font-size-xs font-weight-bold text-uppercase mb-1">
                            Nomor Permohonan &middot; Audit #{{ $audit->id }}
                            ({{ strtoupper(str_replace('_', '-', $audit->audit_type)) }})
                        </div>
                        <h4 class="font-weight-bolder mb-1" style="color:var(--brand-primary); font-size:1.2rem;">
                            {{ $application->application_number ?? '-' }}
                        </h4>
                        <div class="font-size-sm mb-1" style="color:var(--text-secondary);">
                            <i class="flaticon2-group icon-xs mr-1"></i>
                            {{ $vendorName }}
                        </div>
                        <div class="font-size-xs" style="color:var(--text-muted);">
                            <i class="flaticon2-user icon-xs mr-1"></i>
                            {{ $vendorEmail }}
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column align-items-end mt-4 mt-lg-0" style="gap:.75rem;">
                    <span class="verif-status-badge is-pending">
                        {{ strtoupper(str_replace('_', ' ', $audit->status)) }}
                    </span>
                </div>
            </div>

            <div class="verif-summary-row mt-6">
                <span class="verif-pill pending">
                    <i class="flaticon-profile-1 icon-xs"></i>
                    QA Lead: {{ optional($audit->qaLead)->name ?? '—' }}
                </span>
                <span class="verif-pill pending">
                    <i class="flaticon-calendar-with-a-clock-time-tools icon-xs"></i>
                    Dibuat: {{ optional($audit->created_at)->format('d M Y H:i') ?? '—' }}
                </span>
                <span class="verif-pill {{ $audit->completed_at ? 'approved' : 'pending' }}">
                    <i class="flaticon2-check-mark icon-xs"></i>
                    Selesai: {{ optional($audit->completed_at)->format('d M Y H:i') ?? '—' }}
                </span>
                <span class="verif-pill warning">
                    <i class="flaticon-warning icon-xs"></i>
                    Risk Level: {{ strtoupper($application->risk_level ?? '—') }}
                </span>
            </div>
        </div>
    </div>

    @if ($isOnDesk)
        @include('admin.audit.partials.show.on-desk')
    @endif

    @if (!$isOnDesk)
        @include('admin.audit.partials.show.on-site')
    @endif

    {{-- ==================== VERIFIKASI KUESIONER (ON DESK) ==================== --}}
    @if (
        $audit->audit_type === \App\Models\VendorAudit::TYPE_ON_DESK &&
            $audit->status === \App\Models\VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED)
        <div class="card card-custom mb-5"
            style="border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:none;">
            <div class="card-header border-bottom-0 pt-6 pb-0">
                <div class="card-title">
                    <span class="card-icon">
                        <i class="flaticon2-writing" style="color:var(--brand-primary);"></i>
                    </span>
                    <h5 class="card-label font-weight-bolder" style="color:var(--text-primary);">Verifikasi Kuesioner &
                        Keputusan QA</h5>
                </div>
            </div>

            <div class="card-body">
                <div class="alert alert-custom alert-light-primary mb-5" role="alert">
                    <div class="alert-icon"><i class="flaticon-info"></i></div>
                    <div class="alert-text">Silakan verifikasi jawaban kuesioner dari vendor. Anda dapat menyetujui,
                        menolak, atau meminta revisi jika data belum sesuai.</div>
                </div>
                <form method="POST" action="{{ route('qa.audit.questionnaire.verify', $audit->id) }}">
                    @csrf
                    <div class="form-group row">
                        <label class="col-3 col-form-label font-weight-bold text-right text-dark">Keputusan</label>
                        <div class="col-6">
                            <x-vendor-select name="verdict" :options="[
                                'approved' => 'Setujui & Approve Vendor',
                                'need_revision' => 'Kembalikan untuk Revisi',
                                'rejected' => 'Tolak Vendor',
                            ]" selected="approved" required />
                        </div>
                    </div>

                    {{-- Dynamic Revision Notes List --}}
                    <div class="form-group row" id="revision-notes-group" style="display: none;">
                        <label class="col-3 col-form-label font-weight-bold text-right text-dark">Catatan Revisi <span
                                class="text-danger">*</span></label>
                        <div class="col-6">
                            <div id="notes-container">
                                <div class="d-flex mb-2 note-item">
                                    <input type="text" name="revision_notes[]" class="form-control mr-2"
                                        placeholder="Tulis catatan revisi kuesioner..." required disabled>
                                    <button type="button" class="btn btn-icon btn-light-danger btn-remove-note"><i
                                            class="ki ki-close"></i></button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-light-primary font-weight-bold mt-2"
                                id="btn-add-note">
                                <i class="la la-plus"></i> Tambah Catatan
                            </button>
                            <span class="form-text text-muted">Sebutkan bagian kuesioner atau pertanyaan mana yang perlu
                                diperbaiki oleh vendor.</span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-3 col-form-label font-weight-bold text-right text-dark">Catatan Umum /
                            Ringkasan</label>
                        <div class="col-6">
                            <x-vendor-input type="textarea" name="summary"
                                placeholder="Masukkan ringkasan evaluasi..." />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3"></div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-success font-weight-bold">
                                <i class="flaticon2-check-mark"></i> Kirim Keputusan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <x-document-preview />

@endsection

@push('scripts')
    <script>
        $(function() {
            // Flash messages
            (function() {
                var $el = $('#vnd-flash');
                if (!$el.length || typeof Swal === 'undefined') return;

                var messages = [{
                        key: 'success',
                        icon: 'success',
                        title: 'Sukses'
                    },
                    {
                        key: 'error',
                        icon: 'error',
                        title: 'Gagal'
                    },
                    {
                        key: 'warning',
                        icon: 'warning',
                        title: 'Peringatan'
                    },
                    {
                        key: 'info',
                        icon: 'info',
                        title: 'Informasi'
                    },
                ];

                messages.forEach(function(m) {
                    var msg = $el.data(m.key);
                    if (msg) {
                        Swal.fire({
                            html: '<div class="vnd-swal-toast-body">' +
                                '<div class="btn btn-icon btn-outline-success btn-circle btn-sm m-0">' +
                                '<i class="flaticon2-check-mark" style="font-size:1rem;"></i>' +
                                '</div>' +
                                '<div class="vnd-swal-toast-content">' +
                                '<div class="vnd-swal-toast__title">' + m.title + '</div>' +
                                '<div class="vnd-swal-toast__text">' + msg + '</div>' +
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
                });
            })();

            // Horizontal scroll with mouse wheel for tabs
            var tabContainer = document.querySelector('.verif-tabs');
            if (tabContainer) {
                tabContainer.addEventListener('wheel', function(e) {
                    e.preventDefault();
                    this.scrollLeft += e.deltaY;
                }, {
                    passive: false
                });
            }

            // Handle verdict change for questionnaire verification
            $(document).on('change', '#verdict_select', function() {
                var val = $(this).val();
                if (val === 'need_revision') {
                    $('#revision-notes-group').slideDown();
                    $('#notes-container .form-control').prop('disabled', false).prop('required', true);
                } else {
                    $('#revision-notes-group').slideUp();
                    $('#notes-container .form-control').prop('disabled', true).prop('required', false);
                }
            });

            // Add new revision note
            $('#btn-add-note').on('click', function() {
                var html = '<div class="d-flex mb-2 note-item">' +
                    '<input type="text" name="revision_notes[]" class="form-control mr-2" placeholder="Tulis catatan revisi kuesioner..." required>' +
                    '<button type="button" class="btn btn-icon btn-light-danger btn-remove-note"><i class="ki ki-close"></i></button>' +
                    '</div>';
                $('#notes-container').append(html);
            });

            // Remove revision note
            $(document).on('click', '.btn-remove-note', function() {
                if ($('.note-item').length > 1) {
                    $(this).closest('.note-item').remove();
                } else {
                    $(this).siblings('input').val('');
                }
            });

            // Initialize verdict status
            $('#verdict_select').trigger('change');
        });
    </script>
@endpush
