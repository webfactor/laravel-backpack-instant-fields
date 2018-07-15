<a
    href="{{ backpack_url($crud->instantCreateButton['name'] . '/ajax/create?' . $crud->instantCreateButton['entity'] . '=' . $entry->id) }}"
    type="button"
    class="{{ $crud->instantCreateButton['class'] }}"
    data-toggle="modal"
    data-target="#{{ $crud->instantCreateButton['name'] }}_modal">
    {!! $crud->instantCreateButton['content'] !!}
</a>
<div class="modal fade"
     id="{{ $crud->instantCreateButton['name'] }}_modal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="{{ $crud->instantCreateButton['name'] }}_modal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"></div>
    </div>
</div>
