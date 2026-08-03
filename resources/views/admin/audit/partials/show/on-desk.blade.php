        {{-- ==================== ON DESK ==================== --}}
        <div class="card card-custom mb-5"
            style="border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:none;">
            <div class="card-header border-bottom-0 pt-6 pb-0">
                <div class="card-title">
                    <span class="card-icon">
                        <i class="flaticon2-list-3" style="color:var(--brand-primary);"></i>
                    </span>
                    <h5 class="card-label font-weight-bolder" style="color:var(--text-primary);">Verifikasi Jawaban
                        Questionnaire</h5>
                </div>
            </div>

            <div class="card-body pt-3">
                {{-- Status Alerts --}}
                @if ($audit->status === \App\Models\VendorAudit::STATUS_NEED_REVISION)
                    <div class="alert alert-custom alert-light-warning mb-5" role="alert">
                        <div class="alert-icon"><i class="flaticon-warning"></i></div>
                        <div class="alert-text">
                            Questionnaire dikembalikan ke vendor untuk revisi.
                            @if ($audit->questionnaire_revision_notes)
                                <ul class="mb-0 mt-2">
                                    @foreach ($audit->questionnaire_revision_notes as $note)
                                        <li>{{ $note }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @elseif ($audit->status === \App\Models\VendorAudit::STATUS_COMPLETED)
                    <div class="alert alert-custom alert-light-success mb-5" role="alert">
                        <div class="alert-icon"><i class="flaticon2-check-mark"></i></div>
                        <div class="alert-text">Questionnaire disetujui. Status vendor: <strong>APPROVED</strong>.</div>
                    </div>
                @elseif ($audit->status === \App\Models\VendorAudit::STATUS_REJECTED)
                    <div class="alert alert-custom alert-light-danger mb-5" role="alert">
                        <div class="alert-icon"><i class="flaticon2-cross"></i></div>
                        <div class="alert-text">Vendor ditolak.</div>
                    </div>
                @endif

                {{-- Questionnaire Table --}}
                @php
                    $payload = $audit->questionnaire_payload ?? [];
                    $groupedQuestions = isset($questions) ? collect($questions)->groupBy('section') : collect();
                @endphp

                @if (empty($payload) || $groupedQuestions->isEmpty())
                    <div class="alert alert-custom alert-light-warning mb-5" role="alert">
                        <div class="alert-icon"><i class="flaticon-warning"></i></div>
                        <div class="alert-text">Belum ada jawaban dari vendor.</div>
                    </div>
                @else
                    {{-- Tab Nav --}}
                    <div class="tab-nav-wrap">
                        <ul class="nav verif-tabs nav-tabs mb-0 flex-nowrap overflow-auto" role="tablist">
                            @foreach ($groupedQuestions as $sectionName => $sectionQuestions)
                                @php $tabId = 'tab-desk-' . Str::slug($sectionName ?: 'general'); @endphp
                                <li class="nav-item flex-shrink-0">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab"
                                        href="#{{ $tabId }}" role="tab">
                                        {{ $sectionName ?: 'General' }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Tab Content --}}
                    <div class="tab-content pt-7">
                        @foreach ($groupedQuestions as $sectionName => $sectionQuestions)
                            @php $tabId = 'tab-desk-' . Str::slug($sectionName ?: 'general'); @endphp
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}"
                                role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-vertical-center">
                                        <thead>
                                            <tr class="bg-light">
                                                <th style="width: 50px;" class="text-center">No</th>
                                                <th>Pertanyaan</th>
                                                <th style="width: 350px;">Jawaban Vendor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sectionQuestions as $q)
                                                @php
                                                    $val = $payload[$q->id] ?? null;
                                                    $isDoc =
                                                        $q->answer_type ===
                                                        \App\Models\VendorAuditQuestionTemplate::TYPE_DOCUMENT;
                                                @endphp
                                                <tr>
                                                    <td class="text-center font-weight-bold">{{ $loop->iteration }}
                                                    </td>
                                                    <td>
                                                        <div class="font-weight-bold text-dark mb-1"
                                                            style="font-size:1.05rem;">{!! nl2br(e($q->question)) !!}</div>
                                                    </td>
                                                    <td>
                                                        @if ($isDoc)
                                                            @if ($val)
                                                                @foreach ((array) $val as $docPath)
                                                                    <x-preview-doc-button :url="Storage::url($docPath)"
                                                                        label="Lihat" />
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted font-italic">Tidak ada
                                                                    dokumen.</span>
                                                            @endif
                                                        @else
                                                            <div class="font-weight-bolder text-dark"
                                                                style="font-size:1.05rem;">
                                                                {{ is_array($val) ? implode(', ', $val) : $val ?? '—' }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
