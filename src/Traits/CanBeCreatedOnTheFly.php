<?php

namespace Webfactor\Laravel\Backpack\InstantFields;

use Illuminate\Http\Request;

trait CanBeCreatedOnTheFly
{
    /**
     * Returns the name of the on-the-fly entity. If more than one CRUD field provide on-the-fly you
     * have to overwrite this property in your EntityCrudController and use the SAME NAME for the
     * field definition ['on_the_fly']['entity'], otherwise the modal won't work as intended.
     *
     * @return string
     */
    public function getAjaxEntity()
    {
        return $this->ajaxEntity ?? 'ajax_entity';
    }

    /**
     * Provides the search algorithm for the select2 field. Overwrite it in
     * the EntityCrudController if you need some special functionalities
     *
     * @return mixed
     */
    public function ajaxIndex(Request $request)
    {
        $search_term = $request->input('q');
        $search_key = $request->input('searchkey');
        $page = $request->input('page');

        if ($search_term) {
            $results = $this->crud->query->where($search_key, 'LIKE', '%' . $search_term . '%')->paginate(10);
        } else {
            $results = $this->crud->query->paginate(10);
        }

        return $results;
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
     * please copy this method in your EntityCrudController and replace Request by your StoreRequest.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxStore(Request $request)
    {
        if (!$this->crud->hasAccess('create')) {
            return $this->ajaxRespondNoPermission();
        }

        if (parent::storeCrud($request)) {
            return $this->ajaxRespondCreated();
        }

        return $this->ajaxRespondError();
    }

    /**
     * Responses 403 No Permission
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function ajaxRespondNoPermission()
    {
        return response()->json(['errors' => 'No permission'], 403);
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
        return response()->json(['errors' => 'Could not save'], 422);
    }
}
