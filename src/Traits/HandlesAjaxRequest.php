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
            return $this->ajaxCreate($request);
        }

        if ($mode == 'edit') {
            return $this->ajaxEdit($request);
        }

        if ($mode == 'delete') {
            return $this->ajaxDelete($request);
        }

        if (strtolower($request->method()) == 'put') {
            return $this->ajaxStore($request);
        }

        if (strtolower($request->method()) == 'patch') {
            return $this->ajaxUpdate($request);
        }

        if (strtolower($request->method()) == 'delete') {
            return $this->ajaxDestroy($request->input('id'));
        }

        return $this->ajaxIndex($request);
    }

    /**
     * Provides the search algorithm for the select2 field. Overwrite it in
     * the EntityCrudController if you need some special functionalities
     *
     * @param Request $request
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
     * @param Request $request
     * @return string
     */
    public function ajaxCreate(Request $request)
    {
        $this->crud->hasAccessOrFail('create');

        return \View::make($this->getModalView($request, 'create', 'webfactor::modal.create'))
            ->with('action', 'create')
            ->with('entity', $this->getAjaxEntity())
            ->with('crud', $this->crud)
            ->with('fields', $this->crud->getCreateFields())
            ->with('title', trans('backpack::crud.add') . ' ' . $this->crud->entity_name)
            ->with('request', $request)
            ->render();
    }

    /**
     * Returns the HTML that is used for displaying the on-the-fly modal of the editing an entity
     * @param Request $request
     * @return string
     */
    public function ajaxEdit(Request $request)
    {
        $this->crud->hasAccessOrFail('update');

        return \View::make($this->getModalView($request, 'edit', 'webfactor::modal.edit'))
            ->with('action', 'edit')
            ->with('id', $request->input('id'))
            ->with('entity', $this->getAjaxEntity())
            ->with('crud', $this->crud)
            ->with('fields', $this->crud->getUpdateFields($request->input('id')))
            ->with('title', trans('backpack::crud.add') . ' ' . $this->crud->entity_name)
            ->with('request', $request)
            ->render();
    }

    /**
     * Returns the HTML that is used for displaying the on-the-fly modal of the deleting an entity
     * @param Request $request
     * @return string
     */
    public function ajaxDelete(Request $request)
    {
        $this->crud->hasAccessOrFail('delete');

        return \View::make($this->getModalView($request, 'delete', 'webfactor::modal.delete'))
            ->with('action', 'delete')
            ->with('id', $request->input('id'))
            ->with('entry', $this->crud->model::find($request->input('id')))
            ->with('entity', $this->getAjaxEntity())
            ->with('crud', $this->crud)
            ->with('title', trans('backpack::crud.add') . ' ' . $this->crud->entity_name)
            ->with('request', $request)
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

        if (parent::store($request)) {
            return $this->ajaxRespondCreated();
        }

        return $this->ajaxRespondError();
    }

    /**
     * Checks permission and tries to update on-the-fly entity. If you want to enable request validation,
     * please set your StoreRequest class by using setAjaxStoreRequest() in your EntityCrudController
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxUpdate(Request $request)
    {
        if (!$this->crud->hasAccess('update')) {
            return $this->ajaxRespondNoPermission();
        }

        if ($updateRequest = $this->getAjaxUpdateRequest()) {
            if ($errors = $this->ajaxValidationFails($request, $updateRequest->rules())) {
                return response()->json($this->ajaxFormatMessage($errors), 422);
            }
        }

        if (parent::update($request)) {
            return $this->ajaxRespondUpdated();
        }

        return $this->ajaxRespondError();
    }

    /**
     * Checks permission and tries to delete on-the-fly entity.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxDestroy(int $id)
    {
        if (!$this->crud->hasAccess('delete')) {
            return $this->ajaxRespondNoPermission();
        }

        try {
            $this->crud->delete($id);
        } catch (\Exception $exception) {
            return response()->json($this->ajaxFormatMessage($exception), 422);
        }

        return $this->ajaxRespondDeleted();
    }

    /**
     * Validates the request and returns an error bag if it fails
     *
     * @param Request $request
     * @param array $rules
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
     * Responses 204 No Content
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function ajaxRespondUpdated()
    {
        return response()->json([], 204);
    }

    /**
     * Responses 204 No Content
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function ajaxRespondDeleted()
    {
        return response()->json([], 204);
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
     * @param $message
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

        if ($message instanceof \Exception) {
            return $message->getMessage();
        }

        return $message;
    }

    /**
     * @param Request $request
     * @param string $mode
     * @param string $fallbackView
     * @return array|string|null
     */
    private function getModalView(Request $request, string $mode, string $fallbackView)
    {
        if (\View::exists($request->input($mode . '_modal_view'))) {
            return $request->input($mode . '_modal_view');
        }

        return $fallbackView;
    }
}
