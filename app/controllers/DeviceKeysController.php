<?php

class DeviceKeysController extends \BaseController {

	# http://www.dailymail.co.uk/sciencetech/article-2383200/One-PINs-correctly-guessed-time-Research-reveals-20-commonly-used-numbers.html
	protected $blocked_pins = array(
		'0000', '1111', '2222', '3333', '4444', '5555', '6666', '7777', '8888', '9999',
		'1234', '1212', '1004', '2000', '6969'
		);

	public function optionsStorePin()
	{
		$headers = array(
			'Access-Control-Allow-Origin'  => '*',
			'Access-Control-Allow-Methods' => 'OPTIONS, POST',
			'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type'
		);
		return Response::make('', 200, $headers);
	}

	/**
	 * Stores a PIN associated with a device key.
	 *
	 * @return Response
	 */
	public function postStorePin()
	{
		// Check if User exists, create if not
		$key = Input::get('key');
		$pin = Input::get('pin');
		$ltid = Input::get('ltid');

		if (preg_match('[^0-9]', $pin)) {
			return Response::json(array('error' => 'Registration-pin-invalid'));
		}
		if (strlen($pin) != 4) {
			return Response::json(array('error' => 'Registration-pin-invalid'));
		}
		if (in_array($pin, $this->blocked_pins)) {
			return Response::json(array('error' => 'Registration-pin-too-simple'));
		}

		$key = DeviceKey::where('key','=',$key)->first();
		if (!$key) {
			return Response::json(array('error' => 'unknown-error')); // Skal normalt aldri inntreffe
		}

		if ($key->user->ltid != $ltid) {
			return Response::json(array('error' => 'unknown-error')); // Skal normalt aldri inntreffe
		}

		if (!is_null($key->pin)) {
			return Response::json(array('error' => 'Registration-pin-already-set'));
		}

		$key->pin = $pin;

		return Response::json(array('success' => 'great-success'));

	}

}