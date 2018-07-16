<a
    href="{{ backpack_url($crud->instantCreateButton['name'] . '/ajax/create?' . $crud->instantCreateButton['entity'] . '=' . $entry->id) }}"
    type="button"
    class="{{ $crud->instantCreateButton['class'] }}"
    data-toggle="modal"
    data-target="#{{ $crud->instantCreateButton['name'] }}_modal">
    {!! $crud->instantCreateButton['content'] !!}
</a>
