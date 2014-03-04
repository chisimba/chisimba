<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
} 
// end security check

/**
* WikiWriterDocument Object 
*
* The WikiWriterDocument takes in wiki pages and creates
* a master document including all the major elements of the pages 
* (stylesheets, images and actual content) creating a final html
* document to be parsed by whatever selected tool.
*
* @author Ryan Whitney, ryan@greenlikeme.org 
*/
class wwDocument extends object 
{

	// Personal debugger - TODO: REMOVE BEFORE COMMITTING FOR PRODUCTION!
	public function dbg($sErr){
		$handle = fopen('error_log/my_debug.log', 'a');
		fwrite($handle, "[" . strftime("%b %d %Y %H:%M:%S") ."]/classes/wwDocument :: " . $sErr . "\n");
		fclose($handle);
	}

	/**
	 * @var array $stylesheets Reference to stylesheets stored on the system
	 */
	private $stylesheets = array();

	/**
	 * @var array $images Reference to images stored on the system
	 */
	private $images = array();

	/**
	 * @var array $pages Parsed wiki pages containing only the content
	 */
	private $pages = array();

	/**
	* Variable objChisimbaCfg Object for accessing chisimba configuration
	*/
	public $objChisimbaCfg = '';

	/**
	 * Variable string dirPath Directory where all the files will be stored
	 */
	public $dirPath;

	/**
	 * Constructor
	 * Creates the basic structure for the Document object  (html, head, body)
 	 */ 
	public function init()
	{
		try
		{
			//Load classes
			$this->loadClass('wikiParser', 'wikiwriter');

			//Instantiate needed objects
			$this->objChisimbaCfg = $this->newObject('altconfig', 'config'); 

			// create a directory for the relative document files 
			$this->dirPath = 'usrfiles/wikiwriter/' . time() . rand(1,999) . '/'; 
			if(!mkdir($this->dirPath))
				throw new customException('Unable to create directory (' . $this->dirPath . ').  Please contact admin to check file permissions');
		}
		catch(customException $e)
		{
			echo customException::cleanUp();
			die();
		}
	}

	/**
	 * Takes in the url, saves the images and stylesheets, then parses the html 
	 * for the actual wiki content and saves that.
	 * @access public
	 * @params string $url URL for a selected wiki page
	 * @returns void
	 */
	public function importPage($url)
	{
		$this->dbg('page url = ' . $url);

		// Fetch html content and load into a DOM object for processing
		$objDOM = new DOMDocument();
		$objDOM->loadHTML($this->getFile($url));

		// Parse the wiki content 
		$objWP = new wikiParser();
		$objWikiPage = $objWP->parse($objDOM);

		//Find every image in the wiki content and add any new ones into the images array
		foreach($objWikiPage->getElementsByTagName('img') as $imgNode)
		{
			$imgURL = $this->getFullURL($imgNode->getAttribute('src'), $url);
			$this->dbg('img URL = ' . $imgURL . ' , from = ' . $imgNode->getAttribute('src'));

			// If img doesn't already exist, then we load it, set the new relative url and add to the images array 
			if(!array_search($imgURL, $this->images))
			{
				$imgNode->setAttribute('src', $this->getImage($imgURL));
				array_push($this->images, $imgURL);
			} else {
			// Since it already exists, we just replace the url with the relative one
				$imgNode->setAttribute('src', $this->getImageRelURL($imgURL));
			}
		}

		//push the wiki content onto the pages array now that we've replaced the img urls
		array_push($this->pages, $objWikiPage);	

		// Load all the stylesheets, here we reference from the actual full document as opposed to wiki content
		foreach($objDOM->getElementsByTagName('link') as $linkNode)
		{
			//first determine that its a stylesheet
			if($linkNode->getAttribute('rel') == 'stylesheet')
			{
				$ssURL = $this->getFullURL($linkNode->getAttribute('href'), $url);
				$this->dbg('ss URL = ' . $ssURL . ' , from = ' . $linkNode->getAttribute('href'));

				//If the link doesn't already exist, then we load it and save to the stylesheets array
				if(!array_search($ssURL, $this->stylesheets)){
					$this->getStyleSheet($ssURL);
					array_push($this->stylesheets, $ssURL);
				}
			}
		}
	}

	/**
	 * Builds the final page, saves it, and returns the path to the 
	 * final html page
	 * 
	 * @access public
	 * @returns $string filesystem path to html page 
	 */
	public function buildDocument()
	{
		//Create DomDocument 
		$dom = new DomDocument();

		$root = $dom->createElement('html');
		$root = $dom->appendChild($root);

		$head = $dom->createElement('head');
		$head = $root->appendChild($head);

		$body = $dom->createElement('body');
		$body = $root->appendChild($body);

		// add each stylesheet
		foreach($this->stylesheets as $css){
			$link = $dom->createElement('link');
			$link->setAttribute('rel', 'stylesheet');
			$link->setAttribute('type', 'text/css');
			$link->setAttribute('href', $this->getStyleSheetRelURL($css));
			$head->appendChild($link);
		}

		//add each page of content
		foreach($this->pages as $page){
			$content = $dom->importNode($page, true);
			$body->appendChild($content);
		}

		//write file
		// save to the file system
		$fHandle = fopen($this->dirPath . 'index.html', 'w');
		fwrite($fHandle, $dom->saveHTML());
		fclose($fHandle);
		
		// Return the html content
		return $this->dirPath . 'index.html';

	}	

	/**
	 * Retrieves the file from the specified url and returns the content
	 * 
	 * @access private
	 * @params string $url URL of the file to retrieve
	 * @return multi Returns the file format that was requested
	 */
	private function getFile($url)
	{
		$ch = curl_init($url);

		// set cURL options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // So cURL returns the file contents 

		//if URL isn't localhost or 127.0.0.1, setup a proxy for retrieval if setting exists
		if( !(preg_match('/http:\/\/localhost/', $url) || preg_match('/http:\/\/127.0.0.1/', $url)) 
			&& $this->objChisimbaCfg->getItem('KEWL_PROXY'))
		{				
			// Load the proxy settings from the proxy parser
			$pp = $this->newObject('proxyparser', 'utilities');
			$proxy = $pp->getProxy();

			if($proxy['proxy_protocol'])
				curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy['proxy_protocol']);

			if($proxy['proxy_user'] && $proxy['proxy_pass'])
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy['proxy_user'] . ":" . $proxy['proxy_pass']);

			if($proxy['proxy_port'])
				curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['proxy_port']);

			if($proxy['proxy_host'])
				curl_setopt($ch, CURLOPT_PROXY, $proxy['proxy_host']);
					
			/*
			curl_setopt_array($ch, array(CURLOPT_PROXY => $this->objChisimbaCfg->getItem('KEWL_PROXY'),
								CURLOPT_PROXYUSERPWD => 'mwatson:schrodinger',
								CURLOPT_PROXYPORT => 80	)	
							); 
			*/
		}

		// Grab content and close resource
		// TODO: Throw an error if unable to retrieve the file
		$content = curl_exec($ch);
		curl_close($ch);

		return $content;
	}

	/**
	 * Downloads and saves the file from the url given 
	 *
	 * @access private
	 * @params string $url URL of the image to retrieve 
	 * @returns string Relative url for the image
	 */
	private function getImage($url)
	{
		try{	
			// Figure out the relative url for the image and filename
			preg_match('/([a-zA-Z0-9_.-]+\/)+/', $url, $matches);
			$path = $matches[0];
			$this->dbg('print path matches for images = ' . var_export($matches, 1));
			preg_match('/([a-zA-Z0-9_-]+\.)+(jpg|jpeg|gif|bmp|png)/', $url, $matches);
			$filename = $matches[0];

			// make the directory
			// But first we must change all periods to underscores, so htmldoc doesn't think directories are urls
			$path = str_replace('.', '_', $path);
			if(!$this->makeFullPath($this->dirPath . $path))
				throw new customException('Unable to create directory (' . $this->dirPath . $path . ').  Please contact admin to check file permissions');
		
			// get the image
			$img = $this->getFile($url);

			// save to the file system
			$fHandle = fopen($this->dirPath . $path . $filename, 'wb');
			fwrite($fHandle, $img);
			fclose($fHandle);

			// return the relative url
			return $path . $filename;
	 	}
		catch(customException $e)
		{
			echo customException::cleanUp();
			die();
		}

	 }

	/**
	 * Downloads and saves the stylesheet from the url given 
	 *
	 * @access private
	 * @params string $url URL of the stylesheet to download 
	 */
	private function getStyleSheet($url)
	{
		try
		{	
			// TODO: user getStyleSheetRelURL, just drop the filename on the end
			// Figure out the relative url for the stylesheet and filename
			preg_match('/([a-zA-Z0-9_.-]+\/)+/', $url, $matches);
			$path = $matches[0];
			preg_match('/\w+\.css/', $url, $matches);
			$filename = $matches[0];

			// make the directory
			// But first we must change all periods to underscores, so htmldoc doesn't think directories are urls
			$path = str_replace('.', '_', $path);
			if(!$this->makeFullPath($this->dirPath . $path))
				throw new customException('Unable to create directory (' . $this->dirPath . $path . ').  Please contact admin to check file permissions');
		
			// get the stylesheet 
			$css = $this->getFile($url);

			// save to the file system
			$fHandle = fopen($this->dirPath . $path . $filename, 'w');
			fwrite($fHandle, $css);
			fclose($fHandle);
	 	}
		catch(customException $e)
		{
			echo customException::cleanUp();
			die();
		}
	}

	/**
	 * Takes in an URL for an image and returns the relative path we use for generating the page
	 * 
	 * @access private
	 * @params string $url URL of the image  
	 * @returns string relative path for the file
	 */
	private function getImageRelURL($url)
	{
		// Figure out the relative url for the image and filename
		preg_match('/([a-zA-Z0-9_.-]+\/)+/', $url, $matches);
		$path = $matches[0];
		$path = str_replace('.', '_', $path);  // Set all periods to underscores
		preg_match('/([a-zA-Z0-9_-]+\.)+(jpg|jpeg|gif|bmp|png)/', $url, $matches);
		$filename = $matches[0];

		return $path . $filename;
	}

	/**
	 * Takes in an URL for a stylesheet and returns the relative path we use for generating the page
	 * 
	 * @access private
	 * @params string $url URL of the stylesheet
	 * @returns string relative path for the file
	 */
	private function getStyleSheetRelURL($url)
	{
		// Figure out the relative url for the stylesheet and filename
		preg_match('/([a-zA-Z0-9_.-]+\/)+/', $url, $matches);
		$path = $matches[0];
		$path = str_replace('.', '_', $path); // Set all periods to underscores
		preg_match('/\w+\.css/', $url, $matches);
		$filename = $matches[0];

		return $path . $filename;
	}

	/**
	 * Because some urls referenced in html documents aren't full urls,
	 * but rather directory locations, this function takes the original url
	 * and the url used to pull the src document and returns a fully valid URL (http://...)
	 * 
	 * @access private
	 * @params string $url URL to be verified or turned into a fully valid URL
	 * @params string $srcURL URL of the source document
	 * @returns string A fully valid URL
	 */ 
	private function getFullURL($url, $srcURL)
	{
		// if the url starts with http://, then return it, its already fully valid
		if(preg_match('/http:\/\//', $url))
		{
			return $url;
		} 
		// If $url starts with a / then we just need to get the domain name and not the subdirectories
		else if(stripos($url, '/') == 0)
		{
			preg_match('/http:\/{2}([a-zA-Z0-9_.-])+/', $srcURL, $matches);
			foreach($matches as $m){
				$this->dbg('matches = ' . $m);
			}
			return $matches[0] . $url;
		} else{
			//TODO: Strip any leading slash marks if they exist, so we don't get doubles or triple slashes
			// else grab the base url from $srcURL and prepend it to the $url and return
			//TODO: Consider wrapping this in a try/catch or some other way to confirm that the matches returns correctly
			preg_match('/http:\/{2}([a-zA-Z0-9_.-]+\/)+/', $srcURL, $matches);
			foreach($matches as $m){
				$this->dbg('matches = ' . $m);
			}
			return $matches[0] . $url;
		}
	}
	
	/**
	 * Takes the directory path given and creates all the necessary directories
	 * @access private
	 * @params string $path path to create
	 * @returns boolean 1 for success, 0 otherwise
	 */

	private function makeFullPath($path)
	{
		$this->dbg('path = ' . $path);
		//Break the path apart
		$dirs = explode('/', $path);

		//Slowly rebuild while creating directories that don't exist
		$slowPath = ""; 
		foreach($dirs as $dir)
		{
			$slowPath .= $dir;
			if(!file_exists($slowPath))
			{
				if(!mkdir($slowPath))
					return 0;
			}
			$slowPath .= "/";
			$this->dbg('slowpath = ' . $slowPath);
		}

		//Success
		return 1;
	}
}
?>
