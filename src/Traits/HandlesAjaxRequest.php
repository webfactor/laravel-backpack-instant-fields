<?php

namespace Webfactor\Laravel\Backpack\InstantFields;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

trait HandlesAjaxRequest
{
    /**
     * Handles the incoming ajax requests by default
     * @param Request $request
     * @param null $mode
     * @return mixed
     *
     */
    public function handleAjaxRequest(Request $request, $mode = null)
    {
        if ($mode == 'create') {
            return $this->ajaxCreate();
        }

        /*if ($mode == 'edit') {
            return $this->ajaxEdit();
        }*/

        if (strtolower($request->method()) == 'put') {
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
        $field = $this->crud->getFields(null)[$request->input('field')];

        $form = collect($request->input('form'));
        $searchTerm = $request->input('q');
        $page = $request->input('page');
        $pagination = $field['pagination'] ?? 10;

        if (isset($field['search_logic']) && is_callable($field['search_logic'])) {
            return $field['search_logic']($field['model']::query(), $searchTerm, $form)->paginate($pagination);
        }

        return $field['model']::where($field['attribute'], 'LIKE', '%' . $searchTerm . '%')->paginate($pagination);
    }

    /**
     * Returns the HTML that is used for displaying the on-the-fly modal of the adding an entity
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
            ->with('field_name', request()->input('field_name'))
            ->with('attribute', request()->input('attribute'))
            ->render();
    }

    /**
     * Returns the HTML that is used for displaying the on-the-fly modal of the editing an entity
     * @return string
     */
    public function ajaxEdit()
    {
        $this->crud->hasAccessOrFail('edit');

        //
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