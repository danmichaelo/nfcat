<?php

class ActivationCode extends Eloquent {

	protected $hidden = array('activation_code', 'edittoken');

	public function user()
	{
		return $this->belongsTo('User');
	}

	protected function make_code() {
		$digits = 4;
		$this->activation_code = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
	}

	/**
	 * Returnes number of attempts left
	 *
	 * @return bool
	 */
	public function attempts_left() {
		return (3 - $this->attempts);
	}

	/**
	 * Returnes true if code has expired
	 *
	 * @return bool
	 */
	public function expired() {
		$t1hour = new DateTime('-30 minutes');
		/*if ($this->confirmed_at) {
			return true;
		}*/
		return ($this->created_at < $t1hour);
	}

	/**
	 * Test if a code is correct.
	 *
	 * @return bool
	 */
	public function attempt($activation_code) {
		if ($this->expired()) {
			return false;
		}
		if ($this->attempts_left() <= 0) {
			return false;
		}
		$this->attempts++;
		$this->save();
		if ($this->activation_code == $activation_code) {
			$this->confirmed_at = new DateTime;
			$this->save();
			return true;
		}
		return false;
	}

	/**
	 * Save the model to the database.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function save(array $options = array())
	{
		if (is_null($this->activation_code)) {
			$this->make_code();
		}
		parent::save($options);
		return true;
	}



}
