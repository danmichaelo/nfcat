<?php

class LoansController extends \BaseController {

	public function optionsRenew()
	{
		$headers = array(
			'Access-Control-Allow-Origin'  => '*',
			'Access-Control-Allow-Methods' => 'OPTIONS, POST',
			'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type'
		);
		return Response::make('', 200, $headers);
	}

	public function postRenew()
	{

		$ltid = Input::get('ltid');
		$key = Input::get('key');
		$dokid = Input::get('dokid');

		$user = User::where('ltid','=',$ltid)->first();
		if (!$user) {
			return Response::json(array('error' => 'no_such_user'));
		}
		if (!$user->validateKey($key)) {
			return array('error' => 'invalid_key');
		}
		$loan = Loan::with('user')->where('item_id','=',$dokid)->first();
		if (!$loan) {
			return array('error' => 'no_such_loan');
		}
		if ($loan->user->ltid != $ltid) {
			return array('error' => 'no_such_loan');
		}

		$error = $loan->renew();
		if ($error !== '') {
			$response = array('success' => false, 'error' => $error);
		} else {
			$response = array('success' => true, 'dueDate' => $loan->due_at->format('Y-m-d'));
		}
		return Response::json($response);  // ~ 82 bytes per loan

	}

	public function optionsCheckout()
	{
		$headers = array(
			'Access-Control-Allow-Origin'  => '*',
			'Access-Control-Allow-Methods' => 'OPTIONS, POST',
			'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type'
		);
		return Response::make('', 200, $headers);
	}

	public function postCheckout()
	{

		$ltid = Input::get('ltid');
		$key = Input::get('key');
		$dokid = Input::get('dokid');

		$user = User::where('ltid','=',$ltid)->first();
		if (!$user) {
			Log::warning('Failed to checkout ' . $dokid . ' to user ' . $ltid . ': user does not exist. (#100)');
			return Response::json(array('error' => 'Book-checkout-auth-failed'));
		}
		if (!$user->validateKey($key)) {
			Log::warning('Failed to checkout ' . $dokid . ' to user ' . $ltid . ': invalid key given. (#101)');
			return array('error' => 'Book-checkout-auth-failed');
		}
		$loan = Loan::with('user')->where('item_id','=',$dokid)->first();
		if ($loan) {
			Log::warning('Failed to checkout ' . $dokid . ' to user ' . $ltid . ': document already on loan. (#102)');
			return array('error' => 'Book-checkout-loan-exists');
		}

		$loan = new Loan;
		$loan->user_id = $user->id;
		$loan->item_id = $dokid;
		$success = $loan->save();

		if ($success) {
			$response = array(
				'success' => true,
				'loan' => array(
					'dueDate' => $loan->due_at->format('Y-m-d'),
					'dokid' => $loan->item_id,
					//'title' => $loan->title
				)
			);
		} else {
			Log::warning('Failed to checkout ' . $dokid . ' to user ' . $ltid . ': ' . $loan->error . '. (#103)');
			$response = array(
				'success' => false,
				'error' => $loan->error
			);
		}
		return Response::json($response);

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return View::make('loans.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return View::make('loans.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        return View::make('loans.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return View::make('loans.edit');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
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
