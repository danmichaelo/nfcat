<?php

class DeviceKey extends Eloquent {

	protected $hidden = array('key');

	public function user()
	{
		return $this->belongsTo('User');
	}

	private function randomstring($length) {
		// Source: http://www.noobis.de/developer/141-php-random-string-erzeugen.html
		// $chars - String aller erlaubten Zahlen
		$chars = "!#abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
		// Funktionsstart
		srand((double)microtime()*1000000);
		$i = 0; // Counter auf null
		$pass = '';
		while ($i < $length) { // Schleife solange $i kleiner $length
			// Holen eines zufälligen Zeichens
			$num = rand() % strlen($chars);
			// Ausf&uuml;hren von substr zum wählen eines Zeichens
			$tmp = substr($chars, $num, 1);
			// Anhängen des Zeichens
			$pass = $pass . $tmp;
			// $i++ um den Counter um eins zu erhöhen
			$i++;
		}
		// Schleife wird beendet und
		// $pass (Zufallsstring) zurück gegeben
		return $pass;
	}

	public function make() {
		if (is_null($this->key)) {
			// TODO:
			//$this->key = Hash::make($this->randomstring(180));
			$this->key = $this->randomstring(180);
		}
	}

	/**
	 * Save the model to the database.
	 *
	 * @param  array  $options
	 * @return string
	 */
	public function save(array $options = array())
	{
		if (is_null($this->key)) {
			$this->make();
		}
		parent::save($options);
		return '';
	}

}
