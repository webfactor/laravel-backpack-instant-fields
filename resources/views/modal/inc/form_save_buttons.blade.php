<div id="saveActions" class="form-group">

    <input type="hidden" name="save_action" value="{{ $saveAction['active']['value'] }}">

    <button type="submit" class="btn btn-success">
        <span class="fa fa-save" role="presentation" aria-hidden="true"></span> &nbsp;
        <span data-value="{{ $saveAction['active']['value'] }}">{{ $saveAction['active']['label'] }}</span>
    </button>

    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#{{ $entity }}_modal">
        <span class="fa fa-ban"></span> {{ trans('backpack::crud.cancel') }}
    </button>
</div>
