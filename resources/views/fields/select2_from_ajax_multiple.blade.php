<!-- select2 from ajax multiple -->
@php
    $connected_entity = new $field['model'];
    $connected_entity_key_name = $connected_entity->getKeyName();
    $old_value = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : false ));
@endphp

<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    @include('crud::inc.field_translatable_icon')
    <select
        name="{{ $field['name'] }}[]"
        style="width: 100%"
        id="select2_ajax_multiple_{{ $field['name'] }}"
        @include('crud::inc.field_attributes', ['default_class' =>  'form-control'])
        multiple>

        @if ($old_value)
            @foreach ($old_value as $item)
                @if (!is_object($item))
                    @php
                        $item = $connected_entity->find($item);
                    @endphp
                @endif
                <option value="{{ $item->getKey() }}" selected>
                    {{ $item->{$field['attribute']} }}
                </option>
            @endforeach
        @endif
    </select>

    @if (isset($field['on_the_fly']))
        <button href="{{ $field['on_the_fly']['create_view'] }}"
                type="button"
                class="btn btn-primary"
                data-toggle="modal"
                data-target="#{{ $field['on_the_fly']['entity'] ?? 'ajax_entity' }}_modal">
            {{ trans('backpack::crud.add') }}
        </button>
        <div class="modal fade"
             id="{{ $field['on_the_fly']['entity'] ?? 'ajax_entity' }}_modal"
             tabindex="-1"
             role="dialog"
             aria-labelledby="{{ $field['on_the_fly']['entity'] ?? 'ajax_entity' }}_modal"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content"></div>
            </div>
        </div>
    @endif

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->checkIfFieldIsFirstOfItsType($field, $fields))

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
    <!-- include select2 css-->
    <link href="{{ asset('vendor/adminlte/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
    <!-- include select2 js-->
    <script src="{{ asset('vendor/adminlte/bower_components/select2/dist/js/select2.min.js') }}"></script>
    @endpush

@endif

<!-- include field specific select2 js-->
@push('crud_fields_scripts')
<script>
    jQuery(document).ready(function($) {
        // trigger select2 for each untriggered select2 box
        $("#select2_ajax_multiple_{{ $field['name'] }}").each(function (i, obj) {
            if (!$(obj).hasClass("select2-hidden-accessible"))
            {
                $(obj).select2({
                    theme: 'bootstrap',
                    multiple: true,
                    placeholder: "{{ $field['placeholder'] }}",
                    minimumInputLength: "{{ $field['minimum_input_length'] }}",
                    ajax: {
                        url: "{{ $field['data_source'] }}",
                        dataType: 'json',
                        quietMillis: 250,
                        data: function (params) {
                            return {
                                q: params.term, // search term
                                searchkey: "{{ $field['attribute'] }}", // search key in database
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;

                            return {
                                results: $.map(data.data, function (item) {
                                    return {
                                        text: item["{{$field['attribute']}}"],
                                        id: item["{{ $connected_entity_key_name }}"]
                                    }
                                }),
                                more: data.current_page < data.last_page
                            };
                        },
                        cache: true
                    },
                });
            }
        });
    });
</script>
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
