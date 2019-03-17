@extends('webfactor::modal.create_layout')

@section('header')
    <h3 class="box-title">{{ trans('backpack::crud.add_a_new') }} {{ $crud->entity_name }}</h3>
@endsection

@php
    $requestData = [];

    foreach ($request->input() as $key => $item) {
        $requestData[] = [
            'name' => $key,
            'value' => $item,
        ];
    }
@endphp

@section('content')
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
        @include('crud::inc.grouped_errors')

        <!-- load the view from the application if it exists, otherwise load the one in the package -->
            @if(view()->exists('vendor.backpack.crud.form_content'))
                @include('vendor.backpack.crud.form_content', ['fields' => $fields])
            @else
                @include('crud::form_content', ['fields' => $fields])
            @endif
        </div>
    </div>
@endsection

@section('footer')
    @include('webfactor::modal.inc.create_form_save_buttons')
@endsection

@push('crud_fields_scripts')
    <script>
        $("#create_{{ $entity }}").submit(function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            var requestData = <?php echo json_encode($requestData); ?>;

            $.ajax({
                type: "PUT",
                    url: "/{{ ltrim($crud->route . '/ajax', '/') }}",
                        data
                :
                    $("#create_{{ $entity }}").serialize() + '&' + $.param(requestData), // serializes the form's elements.
                        success
                :

                    function (data) {
                        new PNotify({
                            type: "success",
                            title: "{{ trans('backpack::base.success') }}",
                            text: "{{ trans('backpack::crud.insert_success') }}"
                        });

                        $("#{{ $entity }}_create_modal").modal('hide');

                        // provide auto-fill

                        if ($("#select2_ajax_{{ $request->input('field_name') }}").length) {
                        searchfield = $("#select2_ajax_{{ $request->input('field_name') }}")
                    } else {
                        searchfield = $("#select2_ajax_multiple_{{ $request->input('field_name') }}")
                    }

                    searchfield.select2('open');

                    // Get the search box within the dropdown or the selection
                    // Dropdown = single, Selection = multiple
                    var search = searchfield.data('select2').dropdown.$search || searchfield.data('select2').selection.$search;
                    // This is undocumented and may change in the future
                    var userInput = $("#create_{{ $entity }} [name='{{ $request->input('attribute') }}']").serializeArray();

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
        });
    </script>

@endpush
