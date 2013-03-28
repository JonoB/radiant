# Radiant
Self validating models for Laravel's Eloquent Orm

**Note:** This package is heavily influenced by the Aware bundle for Laravel 3.
https://github.com/crabideau5691/Radiant

## Installation

### Composer

Add `"jonob/radiant": "dev-master"` to the `require` section of your `composer.json`:

```composer
"require": {
	"jonob/radiant": "dev-master"
},

```

Now run `composer update`.

### Laravel

Add the following code to the `aliases` section of the `app/config/app.php` file

```php
'Radiant' => 'Jonob\Radiant\Radiant',
```

so that it looks something like the following:

```php
'aliases' => array(
	...
	'Radiant'       => 'Jonob\Radiant\Radiant',
	...
),
```

## Guide

* [Basic](#basic)
* [Validation](#validation)
* [Retrieving Errors](#errors)
* [Messages](#messages)
* [Save callbacks](#save)

<a name="basic"></a>
## Basic

Radiant extends the Eloquent model.

To create a new Radiant model, instead of extending the Eloquent class, simply extend the Radiant class: 

`class User extends Radiant {}`

<a name="validation"></a>
## Validation

Radiant models use Laravel's built-in Validation. Defining validation rules for a model is simple:

```php
class User extends Radiant {

  /**
   * Radiant validation rules
   */
  protected $rules = array(
    'name' => 'required',
    'email' => 'required|email'
  );
}
```

Radiant models validate themselves automatically when `Radiant->save()` is called.

```php
$user = new User();
$user->name = 'John';
$user->email = 'john@doe.com';
$user->save(); // returns false if model is invalid
```

**note:** You also can validate a model at any time using the `Radiant->valid()` method.

<a name="errors"></a>
## Retrieving Errors

When a Radiant model fails validation, an Illuminate\Messages object is attached to the Radiant object.

Retrieve all errors with `$user->getErrors()`.

<a name="messages"></a>
## Validation Messages

You can also set custom error messages in the model if you wish.

```php
protected $messages = array(
	'user.required' => 'Please complete the User field.',
);
```	

<a name="save"></a>
## Callbacks

Note the callbacks have been completely removed from Radiant. This is because Laravel's Events are far 
more flexible and powerful. I highly recommend that you hook into the saving, saved, creating, created,
updating, updated, deleting and deleted events as needed.

In fact, Radiant now uses the saving event to run the validator prior to the model being saved.
