# Changelog

All notable changes to `laravel-backpack-instant-fields` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## NEXT - YYYY-MM-DD

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

## 2.2.0 - 2019-03-17

### Added
- modal_view parameters to use own modal view
- `serialize` parameter to define which current field values should be passed to the foreign CrudController

## 2.1.0 - 2019-02-10

### Added
- delete button!
- edit/delete buttons will be disabled if no entry is selected

## 2.0.0 - 2019-02-08

### Added
- new layout: inline!
- edit button!
- support for dependencies
- use all form values in custom search logic
- adjust pagination (default: 10)
- autofill select on creating an entity

If your are upgrading from version 1.x please just update your routes to use "any" method (see readme)


## 1.4.0 - 2018-11-11

### Added
- allow overriding button views

## 1.3.0 - 2018-07-16

### Added
- `addInstantCreateButtonToList()`-method

### Changed
- some refactoring

## 1.2.0 - 2018-05-24

### Changed
- uses translation files for notifications
- formated validation errors in notifications

## 1.1.0 - 2018-05-23

### Added
- now request validation can be used without overwriting `ajaxStore()` - just use the `setAjaxStoreRequest()` setter.

## 1.0.0/1.0.5 - 2018-05-21

### Added
- initial Version
