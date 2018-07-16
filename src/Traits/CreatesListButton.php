<?php

namespace Webfactor\Laravel\Backpack\InstantFields;

trait CreatesListButton
{
    public function addInstantCreateButtonToList(
        string $foreignAjaxEntity,
        string $content,
        string $entity = 'entity_id',
        string $class = 'btn btn-xs btn-default'
    ) {
        $this->crud->instantCreateButton = [
            'name'    => $foreignAjaxEntity,
            'entity'  => $entity,
            'class'   => $class,
            'content' => $content,
        ];

        $this->crud->addButtonFromView('line', $foreignAjaxEntity, 'webfactor::buttons.create');
        $this->crud->addButtonFromView('bottom', $foreignAjaxEntity, 'webfactor::buttons.fake');
    }
}
