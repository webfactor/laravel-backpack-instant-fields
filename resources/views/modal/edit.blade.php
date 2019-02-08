@extends('webfactor::modal.edit_layout')

@section('header')
    <h3 class="box-title">{{ trans('backpack::crud.edit') }} {{ $crud->entity_name }}</h3>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
        @include('crud::inc.grouped_errors')

        <!-- load the view from the application if it exists, otherwise load the one in the package -->
            @if(view()->exists('vendor.backpack.crud.form_content'))
                @include('vendor.backpack.crud.form_content', ['fields' => $fields, 'action' => 'edit'])
            @else
                @include('crud::form_content', ['fields' => $fields, 'action' => 'edit'])
            @endif
        </div>
    </div>
@endsection

@section('footer')
    @include('webfactor::modal.inc.edit_form_save_buttons')
@endsection

@push('crud_fields_scripts')
    <script>
        $("#edit_{{ $entity }}").submit(function (e) {

            $.ajax({
                type: "PATCH",
                url: "/{{ ltrim($crud->route . '/ajax', '/') }}",
                data: $("#edit_{{ $entity }}").serialize(), // serializes the form's elements.
                success: function (data) {
                    new PNotify({
                        type: "success",
                        title: "{{ trans('backpack::base.success') }}",
                        text: "{{ trans('backpack::crud.update_success') }}"
                    });

                    $("#{{ $entity }}_edit_modal").modal('hide');

                    // provide auto-fill

                    if ($("#select2_ajax_{{ $field_name }}").length) {
                        searchfield = $("#select2_ajax_{{ $field_name }}")
                    } else {
                        searchfield = $("#select2_ajax_multiple_{{ $field_name }}")
                    }

                    searchfield.val(null).trigger('change');
                    searchfield.select2('open');

                    // Get the search box within the dropdown or the selection
                    // Dropdown = single, Selection = multiple
                    var search = searchfield.data('select2').dropdown.$search || searchfield.data('select2').selection.$search;
                    // This is undocumented and may change in the future
                    var userInput = $("#edit_{{ $entity }} [name='{{ $attribute }}']").serializeArray();

                    search.val(userInput[0]['value']);
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

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
    </script>

@endpush
