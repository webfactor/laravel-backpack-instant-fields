# laravel-backpack-instant-fields

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![StyleCI][ico-style-ci]][link-style-ci]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This is a package for [Laravel Backpack](https://laravel-backpack.readme.io/docs) and provides CRUD field types which allow to create/edit/delete a related CRUD entity on-the-fly while adding/editing another.

![Screenshot](https://webfactor.de/files/modal_1.png)

![Screenshot](https://webfactor.de/files/modal_2.png)

## Install

### Via Composer

``` bash
composer require webfactor/laravel-backpack-instant-fields
```

## Usage

### EntityCrudController

For simplicity add the `InstantFields` trait from this package to all EntityCrudControllers which are supposed to provide "instant fields" or are triggered by "instant fields".

```php
<?php

use Webfactor\Laravel\Backpack\InstantFields\InstantFields;

class EntityCrudController extends CrudController
{
    use InstantFields;

    //
}
```

This trait provides all needed route entry points methods and ajax response methods.

### Routes

in your routes file you have to add one additional route in your `CRUD::resource` for each Entity that uses the packages trait. For clarity we recommend to use the `with()` helper:

 ```php
 <?php
 
CRUD::resource('entity', 'EntityCrudController')->with(function () {
    Route::any('entity/ajax/{mode?}', 'EntityCrudController@handleAjaxRequest');
});
 ```

The trait/route will handle the following requests for you:

- search on triggered entity
- retrieve the HTML for the create/edit/delete modal
- store/update/destroy foreign entity

### Available Fields

There are two field types available in this package which allow you an instant creation of related models (1-n and n-m). They are modified versions of the equivalent field types that already exist in Laravel Backpack:

- [select2_from_ajax](https://laravel-backpack.readme.io/docs/crud-fields#section-select2_from_ajax)
- [select2_from_ajax_multiple](https://laravel-backpack.readme.io/docs/crud-fields#section-select2_from_ajax_multiple)

> **Attention:**
>
> Edit and delete buttons are only available for  `select2_from_ajax`  
> Please consider your database constraints when using the delete button!

### Field Definition

Please set the `$ajaxEntity` property by using the setter in the `setup()`-method of the (foreign) EntityCrudController that is **triggered** by an "instant field":
       
```php
<?php

use Webfactor\Laravel\Backpack\InstantFields\InstantFields;

class EntityCrudController extends CrudController
{
   use InstantFields;

   public function setup()
   {
       // other Backpack options
       
       $this->setAjaxEntity('entity');
       
       // fields/columns definitions
   }
}
```
       
In the field definition of the `EntityCrudController` where your instant field is setup you will have to set the above name in the `on_the_fly`-Array.    

> Note: If you use Laravel Backpack Crud >=3.4.11 you don't have to publish the provided fields, you can use them directly from the package by using the `view_namespace` key.

Example:

```
[
    'name'                 => 'entity_id',
    'type'                 => 'select2_from_ajax',
    'label'                => 'Entity',
    'view_namespace'       => 'webfactor::fields',
    'model'                => Entity::class,
    'entity'               => 'entity',
    'attribute'            => 'name',
    'placeholder'          => 'Choose',
    'pagination'           => 20, // optional, default: 10
    'minimum_input_length' => 0,
    'on_the_fly'           => [
        'entity'        => 'entity', // e. g. user, contact, company etc...
        
        // optional:
        
        'create'        => false
        'edit'          => false
        'delete'        => false
        'create_modal'  => 'path to custom create modal'
        'edit_modal'    => 'path to custom edit modal'
        'attribute'     => '...' // see auto-fill below in readme
    ],
    'dependencies'         => ['field1', 'field2'...], // optional, resets this field when changing the given ones
],
```

Instant Fields will try to auto-fill the select2 input after creating a new entry. It will assume that an input field exists with the name `name` and will use its value for the triggered ajax search. If you want to use another field for this, just add `attribute` to the `on_the_fly`-array containing the field name you want to use.

> Sometimes you may need a simple Button/Link to the "real" foreign entity without a modal: Just add `'crud' => true` in that case

## List view

With this package your are also able to add a create button for the foreign CRUD entity in your list view of Backpack! Just add the following line in your `EntityCrudController`:

```php
<?php

    $this->addInstantCreateButtonToList(
        $entity, // foreign entity 
        $content, // content of the button
        $entity_id, // the name of the ID of the current entity will be forwarded by this  
        $class, // optional, default: 'btn btn-xs btn-default', the css class of the button
        $position, // optional, default: beginning, the position of the button in the line 
        $button_view // optional, you can override the used button blade by your own
    );
        
        // Example:
    
    $this->addInstantCreateButtonToList(
        'order', 
        '<i class="fa fa-cart-plus"></i>', 
        'task_id', 
        'btn btn-sm btn-info', 
        'end'
    );
```

## Customization

### Modal view

#### By route

By default the modals are loaded automatically by using `entity` in `on_the_fly` of the field definition, e.g. resulting in `backpack_url($field['on_the_fly']['entity']).'/ajax/create'` for the create modal.

You can overwrite this behavior for all modals by setting an attribute:

```
'on_the_fly' => [
    'entity' => 'entity',
    'create_modal'  => 'route/to/modal/html',
    'edit_modal'    => 'route/to/modal/html',
    'delete_modal'  => 'route/to/modal/html',
]
```

> Please be aware that by using this attributes you will be completely responsible for the content of the modal! The defined request has to provide valid HTML which is then filled in `<div class="modal-content"></div>`

#### By view

Instead of defining a route you can also use a custom view by:

```
'on_the_fly' => [
    'entity' => 'entity',
    'create_modal_view' => 'view.to.create.modal',
    'edit_modal_view'   => 'view.to.edit.modal',
    'delete_modal_view' => 'view.to.delete.modal',
]
```

### Search logic

The "instant field" triggers the `ajaxIndex()` of the `EntityCrudController` where the field is defined and uses the fields `model` and `attribute` parameters to perform the search on the foreign model.  

By adding `search_logic` to the field defintion you can implement your own searching behavior:

```
'search_logic' => function($query, $searchTerm, $form) { // Collection $form is optional
    return $query->where('name', 'like', '%'.$searchTerm.'%')
                 ->whereActive()
                 ->whereSomethingElse();
},
```

`Collecion $form` is an optional parameter and provides all current values of your CRUD form. You can use it to manipulate your search depending on actual inputs and in combination with `dependencies` (see [Backpack Documentation](https://backpackforlaravel.com/docs/3.5/crud-fields#select2_from_ajax))

Furthermore you can then use `attibute` to display enriched values in the dropdown by using an accessor on the model.

### Search data source

If needed you are free to use the `data_source` and `method` attributes from the original field blades which come with Laravel Backpack. This is the URL that is triggered by the select2 field for searching.

### Request validation

You can also use Request Validation! Just set the `$ajaxStoreRequest` and/or `$ajaxUpdateRequest` property by using the provided setter method:

```php
<?php

use Webfactor\Laravel\Backpack\InstantFields\InstantFields;

class EntityCrudController extends CrudController
{
    use InstantFields;

    public function setup()
    {
        // other Backpack options
        
        $this->setAjaxEntity('entity');
        $this->setAjaxStoreRequest(\RequestNamespace\StoreRequest::class);
        $this->setAjaxUpdateRequest(\RequestNamespace\UpdateRequest::class);
        
        // fields/columns definitions
    }
}
```
### Auto-fill after store/update

Instant Fields will try to auto-fill the select2 input after creating a new entry. It will assume that an input field exists with the name `name` and will use its value for the triggered ajax search. If you want to use another field for this, just add `attribute` to the `on_the_fly`-array containing the field name you want to use:

```
'on_the_fly' => [
    'entity' => 'entity',
    'attribute' => 'company'
]
```

You can also pass an array of `attributes` instead of a single attribute:
```
'on_the_fly' => [
    'entity' => 'entity',
    'attributes' => [ 
        'company',
        'contact_name',
        'address',
    ]
]
```
In order for this to work properly on multiple columns, you should implement a custom search logic for the field.

Search logic example:
```
'search_logic' => function ($query, $searchTerm) {
    return $query->where(function ($q) use ($searchTerm) {
        collect(explode(' ', $searchTerm))->each(function ($searchTermPart) use ($q) {
            $q->where(function ($sq) use ($searchTermPart) {
                $sq->where('company', 'LIKE', '%' . $searchTermPart . '%')
                   ->orWhere('contact_name', 'LIKE', '%' . $searchTermPart . '%')
                   ->orWhereRaw('LOWER(JSON_EXTRACT(address, "$.postcode")) LIKE \'"' . strtolower($searchTermPart) . '%\'');
            });
        });
    })->orderBy('name');
```


### Passing current field values to foreign `EntityCrudController`

Sometimes you will need current values to be used for the creation of an foreign entity. You may define `serialize` with the IDs of the fields you need in the store request:

```
'on_the_fly' => [
    'serialize' => ['type_id', 'name'],
]
```

So the current values of `type_id` and `name` will be available in the `$request` of the `ajaxStore` method of your foreign `EntityCrudController` (you will have to overwrite it).

### Fields

Publish the fields in your project and modify functionality

```
php artisan vendor:publish --tag=instantfields
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email thomas.swonke@webfactor.de instead of using the issue tracker.

## Credits

- [Thomas Swonke][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/webfactor/laravel-backpack-instant-fields.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-style-ci]: https://styleci.io/repos/133576169/shield
[ico-travis]: https://img.shields.io/travis/webfactor/laravel-backpack-instant-fields/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/webfactor/laravel-backpack-instant-fields.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/webfactor/laravel-backpack-instant-fields.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/webfactor/laravel-backpack-instant-fields.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/webfactor/laravel-backpack-instant-fields
[link-style-ci]: https://styleci.io/repos/133576169
[link-travis]: https://travis-ci.org/webfactor/laravel-backpack-instant-fields
[link-scrutinizer]: https://scrutinizer-ci.com/g/webfactor/laravel-backpack-instant-fields/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/webfactor/laravel-backpack-instant-fields
[link-downloads]: https://packagist.org/packages/webfactor/laravel-backpack-instant-fields
[link-author]: https://github.com/tswonke
[link-contributors]: ../../contributors
