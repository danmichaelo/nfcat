<?php

class UsersController extends BaseController {

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{

		return View::make('users.create');
	}

	public function optionsStore()
	{
		$headers = array(
			'Access-Control-Allow-Origin'  => '*',
			'Access-Control-Allow-Methods' => 'OPTIONS, POST',
			'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type'
		);
		return Response::make('', 200, $headers);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postStore()
	{

		// Check if User exists, create if not
		$ltid = strtolower(Input::get('ltid'));
		if (!preg_match('/^[0-9a-z]{10}$/', $ltid)) {
			return Response::json(array('error' => 'Registration-invalid-ltid-format'));
		}

		$user = User::where('ltid','=',$ltid)->first();

		$new_user = false;
		if (!$user) {
			$new_user = true;
			$user = new User();
			$user->ltid = $ltid;
			$e = $user->save();
			if (!empty($e)) {
				return Response::json(array('error' => $e));
			}
		}

		$e = $user->addActivationCode();
		if (!empty($e)) {
			if ($e == 'Registration-code-exhausted') {
				return Response::json(array('error' => $e));
			} else {

				$user->syncLoans();

				return Response::json(array('notice' => $e, 'user' => array(
					'firstname' => $user->firstname,
					'lastname' => $user->lastname,
					'phone' => $user->phone,
				)));
			}
		}

		$user->syncLoans();

		$r = Response::json(array('new_user' => $new_user, 'user' => array(
			'firstname' => $user->firstname,
			'lastname' => $user->lastname,
			'phone' => $user->phone,
		)));
		$r->headers->set('Access-Control-Allow-Origin', '*');
		return $r;

	}

	/*
	 * Returns the loans for a user identified by ltid and a confirmed device key.
	 */
	public function postLoans()
	{
		$ltid = Input::get('ltid');
		$key = Input::get('key');
		$forceSync = (Input::get('forceSync') == 'true');

		$user = User::where('ltid','=',$ltid)->first();
		if (!$user) {
			return Response::json(array('error' => 'invalid_user'));
		}

		if (!$user->validateKey($key)) {
			return array('error' => 'invalid_key');
		}

		Log::info('Get loans for ' . $user->ltid . ', forceSync = ' . ($forceSync ? 'true' : 'false'));

		if (is_null($user->synced_at)) {
			$user->syncLoans();
		} else {
			$now = new DateTime();
			$synced_at = $user->synced_at;
			// if more than 1 hour has passed since the last sync date, we re-sync
			$synced_at->add(new DateInterval('PT1H'));
			if ($now > $synced_at || $forceSync) {
				$user->syncLoans();
			}
		}

		$user = User::with('loans')->where('ltid','=',$ltid)->first();
		$loans = array();
		foreach ($user->loans as $loan) {
			$loans[] = array(
				'id' => $loan->id,
				'item_id' => $loan->item_id,
				'title' => $loan->title,
				'created_at' => $loan->created_at ? $loan->created_at->format('Y-m-d') : null,
				'due_at' => $loan->due_at ? $loan->due_at->format('Y-m-d') : null,
			);
		}
		$response = array(
			'synced_at' => $user->synced_at ? $user->synced_at->format('Y-m-d H:i:s') : null,
			'loans' => $loans
		);
		return Response::json($response);  // ~ 82 bytes per loan
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEdit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function putUpdate($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}