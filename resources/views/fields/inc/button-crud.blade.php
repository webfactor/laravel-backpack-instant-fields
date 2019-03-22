<span class="input-group-btn">
    <button
        href="#"
        type="button"
        class="btn btn-warning {{ isset($field['value']) ?: 'disabled'}}"
        style="border-radius: 0px"
        data-id="{{ $field['value'] ?? '' }}"
        data-url="{{ $field['on_the_fly']['crud_url'] ?? backpack_url($field['on_the_fly']['entity']) }}"
        data-target="#{{ $field['on_the_fly']['entity'] ?? 'ajax_entity' }}_edit_crud"
        onclick="location.href=($(this).data('url') + '/' + $(this).data('id') + '/edit')"
        >
    <i class="fa fa-pencil"></i>
    </button>
</span>

