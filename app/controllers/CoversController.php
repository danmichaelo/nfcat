<?php

class CoversController extends \BaseController {

	/*
	|--------------------------------------------------------------------------
	| Cover Controller
	|--------------------------------------------------------------------------
	|
	*/

	public function getBibsysCover($isbn, $path)
	{
		//$tosiste = substr($isbn,-2);
		//$img = "http://innhold.bibsys.no/bilde/forside/images/mini/".$tosiste[1]."/".$tosiste[0]."/".$isbn.".jpg";
		$url = 'http://innhold.bibsys.no/bilde/forside/?size=stor&id=' . $isbn . '.jpg';
		return $this->cacheCover($url, $path);
	}

	public function getGoogleCover($isbn, $path)
	{
		//$tosiste = substr($isbn,-2);
		//$img = "http://innhold.bibsys.no/bilde/forside/images/mini/".$tosiste[1]."/".$tosiste[0]."/".$isbn.".jpg";
		$url = 'https://www.googleapis.com/books/v1/volumes?country=NO&q=' . $isbn;

		$response = json_decode(file_get_contents($url));

		if (isset($response->items)) {
			foreach ($response->items as $item) {
				if (isset($item->volumeInfo) && isset($item->volumeInfo->imageLinks)) {
					if (isset($item->volumeInfo->imageLinks->thumbnail)) {
						return $this->cacheCover($item->volumeInfo->imageLinks->thumbnail, $path);
					} else if (isset($item->volumeInfo->imageLinks->smallThumbnail)) {
						return $this->cacheCover($item->volumeInfo->imageLinks->smallThumbnail, $path);
					}
				}
			}
		}
		return false;
	}

	public function getCover($dokid)
	{
		$dokid = preg_replace('/[^0-9a-zA-Z]/', '', $dokid);
		$base_url = '/covercache/' . sha1($dokid) . '.jpg';
		$path = public_path() . $base_url;

		$default_cover = '/img/blank-cover.jpg';

		if (file_exists($path)) {
			header('Location: ' . $base_url);

		} else {

			$sruResponse = json_decode(file_get_contents('http://services.biblionaut.net/sru_iteminfo.php?dokid=' . $dokid));

			if (!isset($sruResponse->isbn)) {
				copy(public_path($default_cover), $path);
			} else {
				$isbn = $sruResponse->isbn[0];

				if (!$this->getBibsysCover($isbn, $path)) {

					if (!$this->getGoogleCover($isbn, $path)) {

						copy(public_path($default_cover), $path);

					}
				}
			}
			header('Location: ' . $base_url);
			exit;
		}

	}

	/**
	 * Returns the image mime type for a given file,
	 * or false if the file is not an image.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function getMimeType($path)
	{
		$a = getimagesize($path);
		$image_type = $a[2];

		if(in_array($image_type, array(IMAGETYPE_GIF,
									   IMAGETYPE_JPEG,
									   IMAGETYPE_PNG,
									   IMAGETYPE_BMP,
									   IMAGETYPE_TIFF_II,
									   IMAGETYPE_TIFF_MM)))
		{
			return image_type_to_mime_type($image_type);
		}
		return false;
	}

	/**
	 * Store cover image from url in cache
	 *
	 * @param  string  $url
	 * @param  string  $path
	 * @return boolean
	 */
	public function cacheCover($url, $path)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'UBO Scriptotek Dalek/0.1 (+http://biblionaut.net/bibsys/)');
		//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.65 Safari/537.36');
		curl_setopt($ch, CURLOPT_HEADER, 0); // no headers in the output
		curl_setopt($ch, CURLOPT_REFERER, 'http://ask.bibsys.no');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		curl_close($ch);
		file_put_contents($path, $data);

		$mime = $this->getMimeType($path);
		if ($mime === false) {

			// if it's not an image, we just delete it
			// (might be a 404 page for instance)
			unlink($path);
			return false;

		} else if ($mime !== 'image/jpeg') {

			rename("$path", "$path.1");
			$image = new Imagick("$path.1");
			$image->setImageFormat('jpg');
			$image->writeImage("$path");
			unlink("$path.1");

		}

		return true;
	}

}