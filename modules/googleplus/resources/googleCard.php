<?php
/**
*	A very quick and rough PHP class to scrape data from google+
*	Copyright (C) 2011  Mabujo
*	http://plusdevs.com
*	http://plusdevs.com/googlecard-googleplus-php-scraper/
*
*	This program is free software: you can redistribute it and/or modify
*	it under the terms of the GNU General Public License as published by
*	the Free Software Foundation, either version 3 of the License, or
*	(at your option) any later version.
*
*	This program is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*	GNU General Public License for more details.
*
*	You should have received a copy of the GNU General Public License
*	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class googleCard
{
	// The base g+ URL
	public $gplus_url = 'http://plus.google.com/';

	// set a plausible user agent
	public $user_agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:5.0) Gecko/20100101 Firefox/5.0';

	/* 
	* whether to cache the data or not
	* no cache = 0
	* cache = 1
	*/
	public $cache_data = '0';

	// how many hours to cache for
	public $cache_hours = '2';

	// cache file directory
	public $cache_dir = 'cache/';

	// cache file name
	public $cache_file = 'plus_card.txt';

	// constructor
	function __construct($id = '')
	{
		if (!empty($id) && is_numeric($id))
		{
			// build our google+ url
			$this->url = $this->gplus_url . $id;
		}
	}

	// main handler function, call it from your script
	public function googleCard()
	{
		// if we're using caching
		if ($this->cache_data > 0) 
		{
			$html = $this->ghettoCache();
			return $html;
		}
		// don't cache
		else
		{
			$html = $this->parseHtml();
			return $html;
		}
	}

	// parses through the returned html
	protected function parseHtml()
	{
		// load the page
		$this->getPage();

		// parse the html to look for the h4 'have X in circles' element
		preg_match('/<h4 class="a-c-ka-Sf">(.*?)<\/h4>/s', $this->html, $matches);
		$count = $matches[1];
		$circles = preg_replace('/[^0-9_]/', '', $count);
		if (empty($circles)) 
		{
			$circles = 0;
		}

		// parse the html for the user's name
		preg_match('/<span class="fn">(.*?)<\/span>/s', $this->html, $matches);
		$name = $matches[1];

		// parse the html for the img div
		preg_match('/<div class="a-Ba-V-z-N">(.*?)<\/div>/s', $this->html, $matches);
		$img_div = $matches[1];		

		// parse the img div for the image src
		preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $img_div, $matches);
		$img = 'http:' . $matches[1];

		// put the data in an array
		$return = array('count' => $circles, 'name' => $name, 'img' => $img, 'url' => $this->url);

		return $return;
	}

	// use curl to load the page
	protected function getPage()
	{
		// initiate curl with our url
		$this->curl = curl_init($this->url);

		// set curl options
		curl_setopt($this->curl, CURLOPT_HEADER, 0);
		curl_setopt($this->curl, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true); 

		// execute the call to google+
		$this->html = curl_exec($this->curl);

		curl_close($this->curl);
	}

	// caching
	protected function ghettoCache()
	{
		// our cache file
		$file = $this->cache_dir . $this->cache_file;
		$cache_time = ($this->cache_hours * 60) * 60;
		
		// if we have a cache file and it's within our expiry time
		if (file_exists($file) && (time() - $cache_time < filemtime($file))) 
		{
			//open cached file
			$handle = fopen($file, "r");

			//read it
			$data = fgets($handle);

			//close it
			fclose($handle);

			// json decode, put into array and return
			return get_object_vars(json_decode($data));
		}
		// we don't have a cache file
		// call google+ and cache
		else
		{
			// get and parse the data
			$html = $this->parseHtml();

			// json encode the data
			$json = json_encode($html);

			// open the file
			$handle = fopen($file, 'w');

			// write data to file
			fwrite($handle, $json);

			// close file
			fclose($handle);

			// return data
			return $html;
		}
	}
}
?>
