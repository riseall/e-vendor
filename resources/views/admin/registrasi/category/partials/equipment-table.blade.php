@php
    $items = $items ?? [];
    $isReadOnly = $isReadOnly ?? false;
    $title = $title ?? __('inspection_equipment_and_machinery_list');
    $tbodyId = $tbodyId ?? 'equipmentBody';
    $templateId = $templateId ?? 'tpl_equipment';
    $revisionNotes = $revisionNotes ?? [];
@endphp

<div class="row mt-8">
    <div class="col-12">
        <div class="question-wrapper">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <label class="question-label font-weight-bolder">{{ $title }}</label>
                @if (!$isReadOnly)
                    <button type="button" class="btn btn-sm btn-light-primary font-weight-bold btn-add-repeater"
                        data-target-tbody="#{{ $tbodyId }}" data-template="#{{ $templateId }}">
                        <i class="flaticon2-plus icon-sm"></i> {{ __('add_equipment') }}
                    </button>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th width="50px" class="text-center">{{ __('no_short') }}</th>
                            <th>{{ __('equipment_type') }}</th>
                            <th width="100px">{{ __('qty') }}</th>
                            <th>{{ __('capacity_output') }}</th>
                            <th>{{ __('brand_type') }}</th>
                            <th width="120px">{{ __('year') }}</th>
                            @if (!$isReadOnly)
                                <th width="50px"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="{{ $tbodyId }}">
                        @foreach ($items as $index => $item)
                            <tr>
                                <td class="text-center align-middle row-number">{{ $loop->iteration }}</td>
                                <td><input type="text" class="form-control form-control-sm"
                                        name="{{ $name }}[{{ $index }}][jenis]"
                                        value="{{ $item['jenis'] ?? '' }}" required
                                        {{ $isReadOnly ? 'readonly' : '' }}></td>
                                <td><input type="number" class="form-control form-control-sm"
                                        name="{{ $name }}[{{ $index }}][jml]"
                                        value="{{ $item['jml'] ?? '' }}" required
                                        {{ $isReadOnly ? 'readonly' : '' }}></td>
                                <td><input type="text" class="form-control form-control-sm"
                                        name="{{ $name }}[{{ $index }}][kapasitas]"
                                        value="{{ $item['kapasitas'] ?? '' }}"
                                        {{ $isReadOnly ? 'readonly' : '' }}></td>
                                <td><input type="text" class="form-control form-control-sm"
                                        name="{{ $name }}[{{ $index }}][merk]"
                                        value="{{ $item['merk'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>
                                </td>
                                <td><input type="number" class="form-control form-control-sm"
                                        name="{{ $name }}[{{ $index }}][tahun]"
                                        value="{{ $item['tahun'] ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}
                                        placeholder="YYYY">
                                </td>
                                @if (!$isReadOnly)
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-revision-note :name="$name" :notes="$revisionNotes" />
        </div>
    </div>
</div>

@if (!$isReadOnly)
    <script type="text/template" id="{{ $templateId }}">
        <tr>
            <td class="text-center align-middle row-number"></td>
            <td><input type="text" class="form-control form-control-sm" name="{{ $name }}[__INDEX__][jenis]" required></td>
            <td><input type="number" class="form-control form-control-sm" name="{{ $name }}[__INDEX__][jml]" required></td>
            <td><input type="text" class="form-control form-control-sm" name="{{ $name }}[__INDEX__][kapasitas]"></td>
            <td><input type="text" class="form-control form-control-sm" name="{{ $name }}[__INDEX__][merk]"></td>
            <td><input type="number" class="form-control form-control-sm" name="{{ $name }}[__INDEX__][tahun]" placeholder="YYYY"></td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-icon btn-xs btn-light-danger btn-remove-repeater"><i class="fas fa-trash-alt"></i></button>
            </td>
        </tr>
    </script>
@endif
