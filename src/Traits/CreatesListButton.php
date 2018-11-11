<?php

namespace Webfactor\Laravel\Backpack\InstantFields;

trait CreatesListButton
{
    public function addInstantCreateButtonToList(
        string $foreignAjaxEntity,
        string $content,
        string $entity = 'entity_id',
        string $class = 'btn btn-xs btn-default',
        string $position = 'beginning',
        string $createButtonView = 'webfactor::buttons.create',
        string $fakeButtonView = 'webfactor::buttons.fake'
    ) {
        if (!isset($this->crud->instantCreateButtons)) {
            $this->crud->instantCreateButtons = [];
        }

        $this->crud->instantCreateButtons[$foreignAjaxEntity] = [
            'name'    => $foreignAjaxEntity,
            'entity'  => $entity,
            'class'   => $class,
            'content' => $content,
        ];

        $this->crud->addButton('line', $foreignAjaxEntity, 'view', $createButtonView, $position);
        $this->crud->addButton('bottom', $foreignAjaxEntity, 'view', $fakeButtonView);
    }
}
