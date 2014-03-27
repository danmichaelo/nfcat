<?php

use Illuminate\Auth\UserInterface,
	Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');


	public function activation_codes()
	{
		return $this->hasMany('ActivationCode');
	}

	public function device_keys()
	{
		return $this->hasMany('DeviceKey');
	}

	public function loans()
	{
		return $this->hasMany('Loan');
	}

	/**
	 * returns a list of fields that should be converted to instances
	 * of Carbon, which provides an assortment of helpful methods,
	 * and extends the native PHP DateTime class
	 */
	public function getDates()
	{
		return array('created_at', 'updated_at', 'synced_at');
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	/**
	 * Save the model to the database.
	 *
	 * @param  array  $options
	 * @return string
	 */
	public function save(array $options = array())
	{
		$ncip = App::make('ncip.client');
		$response = $ncip->lookupUser($this->ltid);
		if (!$response->exists) {
			return 'Registration-ltid-unregistered';
		}
		if ($response->exists) {
			$this['lastname'] = $response->lastName;
			$this['firstname'] = $response->firstName;
			$this['email'] = $response->email;
			$this['phone'] = $response->phone;
			$this['phone'] = str_replace(' ', '', $this['phone']);  # 902 07 510 -> 90207510
			if (strlen($this['phone']) === 0) {
				return 'Registration-no-phone';
			} else if (strlen($this['phone']) === 8) {
				$this['phone'] = '+47' . $this['phone'];
			} else if (strlen($this['phone']) === 10) {
				$this['phone'] = '+' . $this['phone'];
			} else {
				return 'Registration-unknown-phone-format';
			}
		}

		parent::save($options);
		return '';
	}

	/**
	 * Create a new activation code and send to user.
	 *
	 * @return string
	 */
	public function addActivationCode() {

		$last_code = $this->activation_codes()->orderBy('created_at', 'desc')->first();
		if ($last_code) {
			$t1hour = new DateTime('-5 minutes');
			if ($last_code->created_at > $t1hour) {
				if ($last_code->attempts_left() <= 0) {
					return 'Registration-code-exhausted';
				}
				return 'Registration-code-request-too-rapid';
			}
		}

		$ac = new ActivationCode();
		$ac->user_id = $this->id;
		$ac->save();

		$sms = new NexmoMessage(Config::get('nexmo.account_key'), Config::get('nexmo.account_secret'));
		$sms->sendText($this->phone,
			'BokSkanner', // maks 11 tegn
			'Engangskoden din er: ' . $ac->activation_code);

		return '';

	}

	/**
	 * Create a new device key
	 *
	 * @return string
	 */
	public function addDeviceKey() {

		$key = new DeviceKey();
		$key->user_id = $this->id;
		$key->save();

		return $key->key;

	}

	/**
	 * Validates the device key for the current user
	 */
	public function validateKey($key)
	{
		// TODO: Hash it  #security
		// if (!$this->device_keys()->where('key','=',Hash::make($key))->first()) {
		return $this->device_keys()->where('key','=',$key)->first() ? true : false;
	}

	/**
	 * Syncs the user's loans towards an NCIP service.
	 * Returns true if something changed, false otherwise
	 */
	public function syncLoans()
	{

		Log::info('Sync loans for: ' . $this->ltid);
		Log::info('URL: ' . var_export(Config::get('ncip::url', 'NO'), true) );

		$ncip = App::make('ncip.client');
		$response = $ncip->lookupUser($this->ltid);
		if (!$response->exists) {
			return false;  // TODO: Delete all loans and throw some error
		}

		$changed = false;

		// (1) Insert items from the NCIP response that are not in the DB:
		foreach ($response->loanedItems as $item) {
			$loan = Loan::where('user_id',$this->id)->where('item_id',$item['id'])->first();
			if ($loan) {
				// Compare todo dates, update and return true if changed
				// Log::info( $loan->due_at->getTimestamp() . ' <> ' . $item['dateDue']->getTimestamp() );

				if ($loan->due_at->getTimestamp() != $item['dateDue']->getTimestamp()) {
					Log::info('Got new due date for ' . $item['id'] . ': ' . $loan->due_at->format('Y-m-d H:i:s') . ' -> ' . $item['dateDue']->format('Y-m-d H:i:s') );
					$loan->due_at = $item['dateDue'];
					$loan->save();
					$changed = true;
				}
			} else {
				$loan = new Loan(array(
					'user_id' => $this->id,
					'item_id' => $item['id'],
					'due_at' => $item['dateDue'],
					'title' => $item['title'],
				));
				$loan->save(array('ncip' => false));
				Log::info('Added loan: ' . $item['id'] . ' for user ' . $this->ltid);
				$changed = true;
			}
		}

		// (2) Remove items from DB that are not in the NCIP response:
		foreach (Loan::where('user_id',$this->id)->get() as $dbItem) {
			$inNcipResponse = false;
			foreach ($response->loanedItems as $ncipItem) {
				if ($ncipItem['id'] == $dbItem->item_id) {
					$inNcipResponse = true;
					break;
				}
			}
			if (!$inNcipResponse) {
				$dbItem->delete();
				Log::info('Deleted loan: ' . $dbItem->item_id . ' for user ' . $this->ltid);
				$changed = true;
			}
		}

		$this->synced_at = new DateTime;
		//$this->save();

		return $changed;
	}


}
