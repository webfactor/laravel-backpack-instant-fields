<?php

namespace Webfactor\Laravel\Backpack\InstantFields;

trait InstantFields
{
    private $ajaxEntity = 'ajax_entity';

    use ValidatesInput, HandlesAjaxRequest, CreatesListButton;

    /**
     * Returns the name of the on-the-fly entity. If more than one CRUD field provide on-the-fly you
     * have to overwrite this property in your EntityCrudController and use the SAME NAME for the
     * field definition ['on_the_fly']['entity'], otherwise the modal won't work as intended.
     *
     * @return string
     */
    public function getAjaxEntity()
    {
        return $this->ajaxEntity;
    }

    /**
     * Sets $ajaxEntity property to use if more than one on-the-fly field is used
     *
     * @return void
     */
    public function setAjaxEntity(string $entity)
    {
        $this->ajaxEntity = $entity;
    }
}
