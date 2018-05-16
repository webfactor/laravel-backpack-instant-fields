# laravel-backpack-instant-fields

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![StyleCI][ico-style-ci]][link-style-ci]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This is a package for [Laravel Backpack](https://laravel-backpack.readme.io/docs) and provides CRUD field types which allow to create a related CRUD entity on-the-fly while adding/editing another.

## Install

### Via Composer

``` bash
composer require webfactor/laravel-backpack-instant-fields
```

## Usage

### EntityCrudController

In the EntityCrudController that is supposed to PROVIDE instant creation (not in the CrudController where you want to USE instant fields!) you have to embed the `CanBeCreatedOnTheFly` trait from this package.

```php
<?php

use Webfactor\Laravel\Backpack\InstantFields\CanBeCreatedOnTheFly;

class EntityCrudController extends CrudController
{
    use CanBeCreatedOnTheFly;

    //
}
```

This trait provides all needed route entry points methods and ajax response methods.

### Routes

in your routes file you have to add three additional routes for you `CRUD::resource`. For clarity we recommend to use the `with()` helper:

 ```php
 <?php
 
CRUD::resource('entity', 'EntityCrudController')->with(function () {
    Route::get('entity/ajax/create', 'EntityCrudController@ajaxCreate');
    Route::get('entity/ajax', 'EntityCrudController@ajaxIndex');
    Route::post('entity/ajax', 'EntityCrudController@ajaxStore');
});
 ```

### Available Fields

There are two field types available in this package which allow you an instant creation of related models (1-n and n-m). They are modified versions of the equivalent field types that already exist in Laravel Backpack:

- [select2_from_ajax](https://laravel-backpack.readme.io/docs/crud-fields#section-select2_from_ajax)
- [select2_from_ajax_multiple](https://laravel-backpack.readme.io/docs/crud-fields#section-select2_from_ajax_multiple)

To use instant creation capability of these field types you have to add the `on-the-fly` key

```
'on_the_fly' => [
    'create_view' => backpack_url('entity/ajax/create'),
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
    'data_source'          => backpack_url('entity/ajax'),
    'placeholder'          => 'Choose',
    'minimum_input_length' => 0,
    'on_the_fly'           => [
        'create_view' => backpack_url('entity/ajax/create'),
    ],
],
```

## Multiple instant fields

If you want to use more than one instant field in a CrudController you have to define separate names for each so that JQuery is able to trigger the modals in the right way.

### EntityCrudController

In the EntityCrudController that provides instant creation you have to set the `$ajaxEntity` property by using the setter in the `setup()`-method:

```php
<?php

use Webfactor\Laravel\Backpack\InstantFields\CanBeCreatedOnTheFly;

class EntityCrudController extends CrudController
{
    use CanBeCreatedOnTheFly;

    public function setup()
    {
        // other Backpack options
        $this->setAjaxEntity('name_of_entity');
        
        // fields/columns definitions
    }
}
```

### Field definition

In the field definition you have to add `entity` to the `on-the-fly` key and give it the exact same name as in the EntityCrudController above.

```
'on_the_fly' => [
    'create_view' => backpack_url('entity/ajax/create'),
    'entity'      => 'name_of_entity',
]
```

## Customization

### Search behavior

If you need a different search behavior just overwrite the `ajaxIndex()` method in your `EntityCrudController` and write your own search logic.

### Request validation

You can also use Request Validation! Just copy the `ajaxStore()` method to your `EntityCrudController` and replace `Request` by your desired request (usually just `StoreRequest`).

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
