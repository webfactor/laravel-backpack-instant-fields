<?php

namespace Webfactor\Laravel\Backpack\InstantFields;

use Illuminate\Foundation\Http\FormRequest;

trait ValidatesInput
{
    /**
     * Returns instance of FormRequest for request validation if $ajaxStoreRequest is set
     *
     * @return FormRequest|null
     */
    public function getAjaxStoreRequest()
    {
        return isset($this->storeAjaxRequest) ? new $this->storeAjaxRequest : null;
    }

    /**
     * Sets $ajaxStoreRequest property to use for request validation
     *
     * @return void
     */
    public function setAjaxStoreRequest(string $storeAjaxRequest)
    {
        $this->storeAjaxRequest = $storeAjaxRequest;
    }

    /**
     * Returns instance of FormRequest for request validation if $ajaxUpdateRequest is set
     *
     * @return FormRequest|null
     */
    public function getAjaxUpdateRequest()
    {
        return isset($this->updateAjaxRequest) ? new $this->updateAjaxRequest : null;
    }

    /**
     * Sets $ajaxUpdateRequest property to use for request validation
     *
     * @return void
     */
    public function setAjaxUpdateRequest(string $updateAjaxRequest)
    {
        $this->updateAjaxRequest = $updateAjaxRequest;
    }
}
