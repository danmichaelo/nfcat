<?php

class Loan extends Eloquent {
	protected $guarded = array();
	public static $rules = array();
	public $error;

	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * returns a list of fields that should be converted to instances
	 * of Carbon, which provides an assortment of helpful methods,
	 * and extends the native PHP DateTime class
	 */
	public function getDates()
	{
		return array('created_at', 'updated_at', 'due_at', 'deleted_at');
	}

	public function renew()
	{
		$ncip = App::make('ncip.client');
		$response = $ncip->renewItem($this->user->ltid, $this->item_id);
		if (!$response->success) {
			Log::info('Loan could not be renewed: ' . $this->item_id . ' for ' . $this->user->ltid );
			return $response->error;
		}

		Log::info('Loan renewed for ' . $this->item_id . ', due date changed from ' . $this->due_at->format('Y-m-d') . ' to ' . $response->dateDue->format('Y-m-d') );

		$this->due_at = $response->dateDue;
		$this->save();
		return '';
	}

	/**
	 * Save the model to the database.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function save(array $options = array())
	{
		$this->error = '';
		if (!$this->exists && array_get($options, 'ncip', true) === true) {

			// Checkout in NCIP service
			$user = User::find($this->user_id);

			$ncip = App::make('ncip.client');
			$response = $ncip->checkoutItem($user->ltid, $this->item_id);

			if ((!$response->success && $response->error == 'Empty response') || ($response->success)) {

				if ($response->dateDue) {
					Log::info('Lånte ut ' . $this->item_id . ' til ' . $user->ltid . ', forfallsdato ' . $response->dateDue->format('Y-m-d') );
					//$this->title = $response->bibliographic['title'];
					$this->due_at = $response->dateDue;
				} else {
					Log::info('Lånte ut ' . $this->item_id . ' til ' . $user->ltid . ', fikk tom respons');
					$response2 = $ncip->lookupItem($this->item_id);
					if ($response2->dateRecalled) {
						Log::info('Hentet forfallsdato for ' . $this->item_id . ': ' . $response2->dateRecalled->format('Y-m-d') );
						$this->due_at = $response2->dateRecalled;
						//$this->title = $response2->bibliographic['title'];
					}
				}
			} else {
				Log::info('Dokumentet ' . $this->item_id . ' kunne ikke lånes ut i BIBSYS: ' . $response->error);
				$this->error = 'Dokumentet kunne ikke lånes ut i BIBSYS: ' . $response->error;
				return false;
			}

		}

		parent::save($options);
		return true;
	}

}
