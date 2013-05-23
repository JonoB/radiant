<?php namespace Jonob\Radiant;

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
	 * The https request method
	 *
	 * @var string $method
	 */
	private $httpMethod;


	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);

		$this->httpMethod = \Request::getMethod();
		$this->createEventListener();
	}

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
			return true;
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
	 * Set the validation rules
	 */
	public function setRules($rules = array())
	{
		$this->rules = $rules;
	}

	/**
	 * Create an event listener on the save event if the http method is post
	 *
	 * The event listener will listen for the saving event and validate the model
	 */
	private function createEventListener()
	{
		if ($this->httpMethod == 'POST')
		{
			$host = $this;
			\Event::listen('eloquent.saving: '.get_called_class(), function() use ($host)
			{
				return $host->valid();
			});
		}
	}
}