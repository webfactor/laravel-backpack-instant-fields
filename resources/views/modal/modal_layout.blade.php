@stack('crud_fields_styles')
{!! Form::open(['id' => 'create_'.$entity, 'files'=>$crud->hasUploadFields('create')]) !!}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    @yield('header')
</div>
<div class="modal-body" id="modal-body">
    @yield('content')
</div>

<div class="modal-footer">
    @yield('footer')
</div>
{!! Form::close() !!}
@stack('crud_fields_scripts')