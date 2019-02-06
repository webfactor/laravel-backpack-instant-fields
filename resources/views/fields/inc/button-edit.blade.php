<span class="input-group-btn">
    <button
        href="{{ $field['on_the_fly']['edit_view'] ?? backpack_url($field['on_the_fly']['entity']).'/ajax/edit?field_name='.$field['name'].'&attribute='.($field['on_the_fly']['attribute'] ?? 'name') }}&id=1"
        type="button"
        class="btn btn-warning"
        style="border-radius: 0px"
        data-toggle="modal"
        data-target="#{{ $field['on_the_fly']['entity'] ?? 'ajax_entity' }}_edit_modal"
        onclick="">
    <i class="fa fa-pencil"></i>
    </button>
</span>
<div class="modal fade"
     id="{{ $field['on_the_fly']['entity'] ?? 'ajax_entity' }}_edit_modal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="{{ $field['on_the_fly']['entity'] ?? 'ajax_entity' }}_edit_modal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"></div>
    </div>
</div>

