<?php

class ActivationCodesController extends \BaseController {

	public function optionsVerify()
	{
		$headers = array(
			'Access-Control-Allow-Origin'  => '*',
			'Access-Control-Allow-Methods' => 'OPTIONS, POST',
			'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type'
		);
		return Response::make('', 200, $headers);
	}

	/**
	 * Verify an activation code.
	 *
	 * @return Response
	 */
	public function postVerify()
	{
		// Check if User exists, create if not
		$ltid = Input::get('ltid');
		$activation_code = Input::get('activation_code');

		$user = User::where('ltid','=',$ltid)->first();
		if (!$user) {
			return Response::json(array('error' => 'unknown-user'));  // should not really happen
		}

		$last_code = $user->activation_codes()->orderBy('created_at', 'desc')->first();
		if (!$last_code) {
			return Response::json(array('error' => 'code-not-requested'));  // should not really happen
		}

		if ($last_code->attempts_left() <= 0) {
			return Response::json(array('error' => 'Registration-code-too-many-attempts'));
		}
		if ($last_code->expired()) {
			return Response::json(array('error' => 'Registration-code-expired'));
		}
		if ($last_code->attempt($activation_code)) {
			$key = $user->addDeviceKey();
			return Response::json(array('success' => 'great-success', 'key' => $key));
		} else {
			return Response::json(array('error' => 'Registration-code-invalid'));
		}


	}


}