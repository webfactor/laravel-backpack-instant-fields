<?php

namespace Webfactor\Laravel\Backpack\InstantFields;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

trait HandlesAjaxRequest
{
    /**
     * Handles the incoming ajax requests by default
     * @param Request $request
     * @param null $create
     * @return mixed
     *
     */
    public function handleAjaxRequest(Request $request, $create = null)
    {
        if ($create) {
            return $this->ajaxCreate();
        }

        if (strtolower($request->method()) == 'post') {
            return $this->ajaxStore($request);
        }

        return $this->ajaxIndex($request);
    }

    /**
     * Provides the search algorithm for the select2 field. Overwrite it in
     * the EntityCrudController if you need some special functionalities
     *
     * @return mixed
     */
    public function ajaxIndex(Request $request)
    {
        $searchTerm = $request->input('q');
        $page = $request->input('page');

        $field = $this->crud->getFields(null)[$request->input('field')];

        if (isset($field['search_logic']) && is_callable($field['search_logic'])) {
            return $field['search_logic']($field['model']::query(), $searchTerm)->paginate(10);
        }

        return $field['model']::where($field['attribute'], 'LIKE', '%' . $searchTerm . '%')->paginate(10);
    }

    /**
     * Returns the HTML that is used for displaying the on-the-fly modal of the entity
     * @return string
     */
    public function ajaxCreate()
    {
        $this->crud->hasAccessOrFail('create');

        return \View::make('webfactor::modal.create')
            ->with('action', 'create')
            ->with('entity', $this->getAjaxEntity())
            ->with('crud', $this->crud)
            ->with('saveAction', $this->getSaveAction())
            ->with('fields', $this->crud->getCreateFields())
            ->with('title', trans('backpack::crud.add') . ' ' . $this->crud->entity_name)
            ->render();
    }

    /**
     * Checks permission and tries to store on-the-fly entity. If you want to enable request validation,
     * please set your StoreRequest class by using setAjaxStoreRequest() in your EntityCrudController
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxStore(Request $request)
    {
        if (!$this->crud->hasAccess('create')) {
            return $this->ajaxRespondNoPermission();
        }

        if ($storeRequest = $this->getAjaxStoreRequest()) {
            if ($errors = $this->ajaxValidationFails($request, $storeRequest->rules())) {
                return response()->json($this->ajaxFormatMessage($errors), 422);
            }
        }

        if (parent::storeCrud($request)) {
            return $this->ajaxRespondCreated();
        }

        return $this->ajaxRespondError();
    }

    /**
     * Validates the request and returns an error bag if it fails
     *
     * @return mixed
     */
    public function ajaxValidationFails(Request $request, array $rules)
    {
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return false;
    }

    /**
     * Responses 403 No Permission
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function ajaxRespondNoPermission()
    {
        return response()->json($this->ajaxFormatMessage(trans('backpack::base.unauthorized')), 403);
    }

    /**
     * Responses 201 Created
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function ajaxRespondCreated()
    {
        return response()->json([], 201);
    }

    /**
     * Responses 422 Error
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function ajaxRespondError()
    {
        return response()->json($this->ajaxFormatMessage(trans('backpack::base.error_saving')), 422);
    }

    /**
     * Formats the message for the notification
     *
     * @return string
     */
    private function ajaxFormatMessage($message)
    {
        if ($message instanceof MessageBag) {
            $validationErrors = '<ul>';

            foreach ($message->all() as $validationError) {
                $validationErrors .= '<li>' . $validationError . '</li>';
            }

            $validationErrors .= '</ul>';

            return $validationErrors;
        }

        return $message;
    }
}
