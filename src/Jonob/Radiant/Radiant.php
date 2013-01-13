<?php namespace Jonob\Radiant;

use \Illuminate\Support\Facades;

class Radiant extends \Illuminate\Database\Eloquent\Model
{
	/**
	 * Validation rules
	 *
	 * @var array $rules
	 */
	protected $rules = array();

	/**
	 * Validation messages
	 *
	 * @var array $messages
	 */
	protected $messages = array();

	/**
	 * Validation errors
	 *
	 * @var \support\MessageBag
	 */
	protected $errors;

	/**
	 * Validate the Model
	 *
	 * @param array $rules 		Validation rules
	 * @param array $messages 	Custom error messages
	 * @return bool
	 */
	public function valid($rules = array(), $messages = array())
	{
		if ( ! empty($rules))
		{
			$this->rules = $rules;
		}

		if ( ! empty($messages))
		{
			$this->messages = $messages;
		}

		// if there are no rules, then assume everything is valid
		if (empty($this->rules))
		{
			return $true;
		}

		// run the validator
		$validator = \Illuminate\Support\Facades\Validator::make($this->attributes, $this->rules, $this->messages);
        $valid = $validator->passes();

		// if validation fails, then grab all the error messages from the validator
		if ( ! $valid)
		{
			$this->errors = $validator->messages();
		}

		return $valid;
	}

	/**
	 * Get all errors for the Model
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Called before a model is deleted
	 *
	 * @return bool
	 */
	public function beforeDelete()
	{
		return true;
	}

	/**
	 * Called after a model is deleted
	 *
	 * @return bool
	 */
	public function afterDelete()
	{
		return true;
	}

	/**
	 * Called before a model is saved
	 *
	 * @return bool
	 */
	public function beforeSave()
	{
		return true;
	}

	/**
	 * Called after a model is succesfully saved
	 *
	 * @return bool
	 */
	public function afterSave()
	{
		return true;
	}

	/**
	 * Delete the model from the database.
	 *
	 * @return void
	 */
	public function delete()
	{
		if ($this->exists)
		{
			// run beforeDelete
			if ( ! $this->beforeDelete())
			{
				return false;
			}

			$key = $this->getKeyName();

			$delete = $this->newQuery()->where($key, $this->getKey())->delete();

			// run after delete
			if ($delete)
			{
				$this->afterDelete();
			}

			return $delete;
		}
	}

	/**
	 * Save the model to the database
	 *
	 * @param array $rules
	 * @param array $messages
	 * @return bool
	 */
	public function save($rules = array(), $messages = array())
	{
		// validate
		$valid = $this->valid($rules, $messages);
		if ( ! $valid)
		{
			return false;
		}

		$keyName = $this->getKeyName();

		// First we need to create a fresh query instance and touch the creation and
		// update timestamp on the model which are maintained by us for developer
		// convenience. Then we will just continue saving the model instances.
		$query = $this->newQuery();

		if ($this->timestamps)
		{
			$this->updateTimestamps();
		}

		// run beforeSave
		if ( ! $this->beforeSave())
		{
			return false;
		}

		// If the model already exists in the database we can just update our record
		// that is already in this database using the current IDs in this "where"
		// clause to only update this model. Otherwise, we'll just insert them.
		if ($this->exists)
		{
			$query->where($keyName, '=', $this->getKey());

			$query->update($this->attributes);
		}

		// If the model is brand new, we'll insert it into our database and set the
		// ID attribute on the model to the value of the newly inserted row's ID
		// which is typically an auto-increment value managed by the database.
		else
		{
			if ($this->incrementing)
			{
				$this->$keyName = $query->insertGetId($this->attributes);
			}
			else
			{
				$query->insert($this->attributes);
			}
		}

		// run afterSave
		$this->afterSave();

		return $this->exists = true;
	}
}