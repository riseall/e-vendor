<div class="card mt-5">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title font-weight-bolder">Riwayat Permohonan</h3>
    </div>
    <div class="card-body pt-2">
        <ul class="nav nav-tabs nav-tabs-line mb-5" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#activity-history">Perubahan Status</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#file-history">Versi Dokumen</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="activity-history">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Aksi</th>
                                <th>Status</th>
                                <th>User</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activityHistory as $activity)
                                <tr>
                                    <td>{{ optional($activity->created_at)->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $activity->action)) }}</td>
                                    <td>
                                        {{ $activity->status_before ?: '-' }}
                                        @if ($activity->status_before !== $activity->status_after)
                                            &rarr; {{ $activity->status_after }}
                                        @endif
                                    </td>
                                    <td>{{ optional($activity->user)->name ?: 'Sistem' }}</td>
                                    <td>{{ $activity->ip_address ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted">Belum ada histori status.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="file-history">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Nama File</th>
                                <th>Versi</th>
                                <th>Waktu</th>
                                <th>Pengunggah</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($fileHistory as $file)
                                <tr>
                                    <td>{{ $file['field'] }}</td>
                                    <td>{{ $file['name'] }}</td>
                                    <td>
                                        <span
                                            class="label {{ $file['is_current'] ? 'label-light-success' : 'label-light' }} label-inline">
                                            {{ $file['is_current'] ? 'Aktif' : 'Lama' }}
                                        </span>
                                    </td>
                                    <td>{{ optional($file['uploaded_at'])->format('d/m/Y H:i') }}</td>
                                    <td>{{ $file['uploaded_by'] ?: '-' }}</td>
                                    <td>
                                        <x-preview-doc-button url="{{ $file['url'] }}" title="{{ $file['name'] }}"
                                            compact />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted">Belum ada histori versi dokumen.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
