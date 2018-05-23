@extends('webfactor::modal.modal_layout')

@section('header')
    <h3 class="box-title">{{ trans('backpack::crud.add_a_new') }} {{ $crud->entity_name }}</h3>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
        @include('crud::inc.grouped_errors')

        <!-- load the view from the application if it exists, otherwise load the one in the package -->
            @if(view()->exists('vendor.backpack.crud.form_content'))
                @include('vendor.backpack.crud.form_content', ['fields' => $crud->getFields('create')])
            @else
                @include('crud::form_content', ['fields' => $crud->getFields('create')])
            @endif
        </div>
    </div>
@endsection

@section('footer')
    @include('webfactor::modal.inc.form_save_buttons')
@endsection

@push('crud_fields_scripts')
    <script>
        $("#create_{{ $entity }}").submit(function (e) {

            $.ajax({
                type: "POST",
                url: "/{{ $crud->route . '/ajax' }}",
                data: $("#create_{{ $entity }}").serialize(), // serializes the form's elements.
                success: function (data) {
                    new PNotify({
                        type: "success",
                        title: "{{ trans('backpack::base.success') }}",
                        text: "{{ trans('backpack::crud.insert_success') }}"
                    });

                    $("#{{ $entity }}_modal").modal('toggle');
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
