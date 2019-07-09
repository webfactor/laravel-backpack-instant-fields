@stack('crud_fields_styles')
<form method="post" id="{{ $entity.'_'.$action }}" accept-charset="UTF-8">
    {{ csrf_field() }}
    <div class="modal-header">
        <button type="button"
                class="close"
                data-toggle="modal"
                data-target="#{{ $entity.'_'.$action }}_modal"
                aria-label="Close">
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
</form>
@stack('crud_fields_scripts')
