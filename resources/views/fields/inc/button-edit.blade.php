@php($attribute = $field['on_the_fly']['attribute'] ?? json_encode($field['on_the_fly']['attributes']) ?? 'name')
<span class="input-group-btn">
    <button
        href="#"
        type="button"
        class="btn btn-warning {{ isset($field['value']) ?: 'disabled'}}"
        style="border-radius: 0px"
        data-toggle="modal"
        data-id="{{ $field['value'] ?? '' }}"
        data-target="#{{ $field['on_the_fly']['entity'] ?? 'ajax_entity' }}_edit_modal"
        data-load-url="{{ $field['on_the_fly']['edit_modal'] ?? backpack_url($field['on_the_fly']['entity']).'/ajax/edit?field_name='.$field['name'].'&edit_modal_view='.($field['on_the_fly']['edit_modal_view'] ?? 'webfactor::modal.edit').'&attribute='. $attribute }}">
    <i class="fa fa-edit"></i>
    </button>
</span>
<div class="modal fade"
     id="{{ $field['on_the_fly']['entity'] ?? 'ajax_entity' }}_edit_modal"
     role="dialog"
     aria-labelledby="{{ $field['on_the_fly']['entity'] ?? 'ajax_entity' }}_edit_modal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"></div>
    </div>
</div>

