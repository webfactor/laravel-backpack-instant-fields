@isset($crud->instantCreateButtons[$button->name])
    <a
        href="{{ backpack_url($crud->instantCreateButtons[$button->name]['name'] . '/ajax/create?' . $crud->instantCreateButtons[$button->name]['entity'] . '=' . $entry->id) }}"
        type="button"
        class="{{ $crud->instantCreateButtons[$button->name]['class'] }}"
        data-toggle="modal"
        data-target="#{{ $crud->instantCreateButtons[$button->name]['name'] }}_modal">
        {!! $crud->instantCreateButtons[$button->name]['content'] !!}
    </a>
@endisset
