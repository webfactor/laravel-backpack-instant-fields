@extends('webfactor::modal.layout', ['action' => $action ])

@section('header')
    <h3 class="box-title">{{ trans('backpack::crud.' . $action) }} {{ $crud->entity_name }}</h3>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
        @include('crud::inc.grouped_errors')

        <!-- load the view from the application if it exists, otherwise load the one in the package -->
            @if(view()->exists('vendor.backpack.crud.form_content'))
                @include('vendor.backpack.crud.form_content', ['fields' => $fields, 'action' => $action])
            @else
                @include('crud::form_content', ['fields' => $fields, 'action' => $action])
            @endif
        </div>
    </div>
@endsection

@section('footer')
    @include('webfactor::modal.inc.' . $action . '_form_save_buttons')
@endsection

@push('crud_fields_scripts')
    <script>
        var action = '{{$action}}';
        var modalId = "#{{$entity}}_" + action;
        $(modalId).submit(function (e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.

            $.ajax({
                type:  action === 'create' ? 'PUT' : 'PATCH',
                url: "/{{ ltrim($crud->route . '/ajax', '/') }}",
                data: $(modalId).serialize(), // serializes the form's elements.
                success: function (data) {
                    new PNotify({
                        type: "success",
                        title: "{{ trans('backpack::base.success') }}",
                        text: "{{ trans('backpack::crud.update_success') }}"
                    });

                    $(modalId + "_modal" ).modal('hide');

                    // provide auto-fill

                    if ($("#select2_ajax_{{ $request->input('field_name') }}").length) {
                        searchfield = $("#select2_ajax_{{ $request->input('field_name') }}")
                    } else {
                        searchfield = $("#select2_ajax_multiple_{{ $request->input('field_name') }}")
                    }

                    searchfield.val(null).trigger('change');
                    searchfield.select2('open');

                    // Get the search box within the dropdown or the selection
                    // Dropdown = single, Selection = multiple
                    var search = searchfield.data('select2').dropdown.$search || searchfield.data('select2').selection.$search;
                    // This is undocumented and may change in the future
                    var searchText = '';
                    try {
                        var autofill_attributes = JSON.parse(@json($request->input('autofill_attributes')));
                        searchText = autofill_attributes.map(function (attr) {
                            let input = $(modalId + " [name='" + attr + "']").serializeArray()[0];
                            return input ? input['value'] : '';
                        }).join(' ');
                    } catch (e) {
                        let input = $(modalId + " [name='{{ $request->input('autofill_attributes') }}']").serializeArray()[0];
                        if (input) {
                            searchText = input['value'];
                        }
                    }

                    search.val(searchText);
                    search.trigger('input');
                    setTimeout(function () {
                        $('.select2-results__option').trigger("mouseup");
                    }, 200);
                },
                error: function (data) {
                    new PNotify({
                        type: "error",
                        title: "{{ trans('backpack::base.error') }}",
                        text: "{{ trans('backpack::base.error') }}: " + data.responseJSON
                    });
                }
            });

        });
    </script>

@endpush
