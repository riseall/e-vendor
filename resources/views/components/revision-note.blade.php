@props([
    'name',
    'notes' => [],
])

@if (!empty($notes[$name]))
    <div class="revision-note-message text-danger mt-1 font-size-xs font-weight-bold"
        data-revision-field="{{ $name }}">
        <i class="fas fa-exclamation-circle text-danger mr-1" style="font-size: 10px;"></i>
        Revisi: {{ $notes[$name] }}
    </div>
@endif
