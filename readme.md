# Radiant
Self validating models for Laravel's Eloquent Orm

**Note:** This package is heavily influenced by the Radiant bundle for Laravel 3.
https://github.com/crabideau5691/Radiant

## Installation

### Composer

Add `"jonob/radiant": ">=1.0.*"` to the `require` section of your `composer.json`:

```composer
"require": {
	"jonob/radiant": ">=1.0.*"
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
* [Delete callbacks](#delete)

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

**note:** You also can validate a model at an time using the `Radiant->valid()` method.

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
## Save Callbacks

Radiant provides a convenient way to create callbacks on the `save()` method in your models. You 
can create a `beforeSave()` and `afterSave()` method in each of your models.

**Note:** The `beforeSave()` method should return `true` if you want the `save()` method to run. If
the `beforeSave()` method return `false`, then the `save()` method will be intercepted.

```php
class User extends Radiant {

  public function beforeSave()
  {
    // Change the email to lowercase
    $this->email = strtolower($this->email);

    return true;
  }

}
```

You can also run afterSave() methods - this can include any action that you want to perform after
the save action has successfully completed.

<a name="delete"></a>
## Delete Callbacks

Radiant also provides a way to create callbacks on the `delete()` method in your models.

**Note:** The `beforeDelete()` method should return `true` if you want the `delete()` method to run. If
the `beforeDelete()` method return `false`, then the `delete()` method will be intercepted.

```php
class User extends Radiant {

  public function beforeDelete($user_id)
  {
    // We don't want to delete active users
	$user = self::find($user_id);
    if ( ! $user or $user->active)
	{
		// set a string in the session
		Session::flash('error', 'Unable to delete that user');
		return false;
	}

    return true;
  }

}
```


