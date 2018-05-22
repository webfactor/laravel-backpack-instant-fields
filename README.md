# laravel-backpack-instant-fields

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![StyleCI][ico-style-ci]][link-style-ci]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This is a package for [Laravel Backpack](https://laravel-backpack.readme.io/docs) and provides CRUD field types which allow to create a related CRUD entity on-the-fly while adding/editing another.

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

use Webfactor\Laravel\Backpack\InstantFields\Instantfields;

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
    Route::match(['get', 'post'],'entity/ajax/{create?}', 'EntityCrudController@handleAjaxRequest');
});
 ```

The trait/route will handle three situations for you:

- search on triggered entity
- retrieve the HTML for the modal
- store entity from modal

### Available Fields

There are two field types available in this package which allow you an instant creation of related models (1-n and n-m). They are modified versions of the equivalent field types that already exist in Laravel Backpack:

- [select2_from_ajax](https://laravel-backpack.readme.io/docs/crud-fields#section-select2_from_ajax)
- [select2_from_ajax_multiple](https://laravel-backpack.readme.io/docs/crud-fields#section-select2_from_ajax_multiple)

To use instant creation capability of these field types you have to add the `on-the-fly` key and set a name for the entity.

```
'on_the_fly' => [
    'entity' => 'entity' // e.g. user, contact, company, job etc...
]
```

If you use Laravel Backpack Crud >=3.4.11 you don't have to publish the provided fields, you can use them directly from the package by using the `view_namespace` key.

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
    'minimum_input_length' => 0,
    'on_the_fly'           => [
        'entity' => 'entity',
    ],
],
```

## Multiple instant fields

If you want to use more than one instant field in a CrudController you have to set the `$ajaxEntity` property by using the setter in the `setup()`-method of the EntityCrudController that is triggered by an "instant field". This has to be the same name as in the field definition:

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

## Customization

### Modal view

By default the modal is loaded automatically by using `entity` in `on_the_fly` of the field definition resulting in `backpack_url($field['on_the_fly']['entity']).'/ajax/create'` in the field blade.

You can overwrite this behavior by setting a `create_view` attribute:

```
'on_the_fly' => [
    'entity' => 'entity',
    'create_view => 'route/to/modal/html'
]
```

### Search logic

The "instant field" triggers the `ajaxIndex()` of the `EntityCrudController` where the field is defined and uses the fields `model` and `attribute` parameters to perform the search on the foreign model.  

By adding `search_logic` to the field defintion you can implement your own searching behavior:

```
'search_logic' => function($query, $searchTerm) {
    return $query->where('name', 'like', '%'.$searchTerm.'%')
                 ->whereActive()
                 ->whereSomethingElse();
},
```

Furthermore you can then use `attibute` to display enriched values in the dropdown by using an accessor on the model.

### Search data source

If needed you are free to use the `data_source` attribute from the original field blades which come with Laravel Backpack. This is the URL that is triggered by the select2 field for searching.

### Request validation

You can also use Request Validation! Just set the `$ajaxStoreRequest` property by using the provided setter method:

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
        
        // fields/columns definitions
    }
}
```

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
