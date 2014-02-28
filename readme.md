# Rulez - Validation for Laravel

This package is not ready for production!

Rulez provides easy way for setting up input validation rules and validation service.

## Installation

Require keevitaja/rulez with composer

`composer require keevitaja/rulez:dev-master`

Add service provider and alias to `app/config/app.php`

```php
'providers' => array(

    'Keevitaja\Rulez\RulezServiceProvider',

),
```

```php
'aliases' => array(

    'Rulez'           => 'Keevitaja\Rulez\RulezFacade'

),
```

## Usage

Validation rules can be set up in various places, like routes.php, but probably best way would be to create `app/rules.php` file and require it in the `app/start/global.php`.

```php
require app_path().'/rules.php';
```

### Setting the input validation rules

Rules can be set sepparately for creation and update. Base rules will apply for both. In the example below, `users` is the name for the rule set, which can be used later in the controller. 

```php
Rulez::register('users', function($rulez)
{
    $rulez->addBase([
        'first_name' => 'required|min:2',
        'last_name' => 'required|min:2',
        'password' => 'required|min:6'
    ]);

    $rulez->addCreate([
        'email' => 'required|unique:users|email'
    ]);

    $rulez->addUpdate([
        'email' => 'required|unique:users,email,%s|email'
    ]);
});
```

If you do not need sepparate rules for create and update, then just use base rules.

### Validating the input

`Rulez::validateBase($name, $input)` 

Validates base rules.

`Rulez::validateCreate($name, $input)` 

Merges create and base rules and validates.

`Rulez::validateUpdate($name, $input, $exclude = false)`

Merges update and base rules, sets the row id for case there's a unique column and validates. 

`'users'` is the name used with the rule registration in `app/rules.php`.

See the example below:

```php
$input = Input::all();

if (Rulez::validateUpdate('users', $input, $id))
{
    // all ok, lets do the update and redirect
}

// something does not validate, send user back with errors and input

return Redirect::back()->withErrors(Rulez::validationErrors())->withInput();
```

## If you like this

please follow me [@keevitaja](https://twitter.com/keevitaja)