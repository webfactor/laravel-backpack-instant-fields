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
}
