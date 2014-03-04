<?php 

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
} 
// end security check

/**
* Class wikiwriter - the controller class for the wikiwriter module
* TODO: Flesh out description
* 
* @author Ryan Whitney, ryan@greenlikeme.org 
* @package wikiwriter
*/
class wikiwriter extends controller 
{

	/**
	* Variable objChisimbaCfg Object for accessing chisimba configuration
	*/
	public $objChisimbaCfg = '';

	/**
	* Variable objSysConfig Object for accessing wikiwriter module configuration 
	*/
	public $objSysConfig = '';

	// Personal debugger - TODO: REMOVE BEFORE COMMITTING FOR PRODUCTION!
	public function dbg($sErr){
		$handle = fopen('error_log/my_debug.log', 'a');
		fwrite($handle, "[" . strftime("%b %d %Y %H:%M:%S") ."]/controller.php :: " . $sErr . "\n");
		fclose($handle);
	}

    /**
     * Method to initialise the wiki object
     * 
     * @access public 
     */
    public function init()
    {
		try{
			// Load Objects
			//$this->loadClass('wwDocument', 'wikiwriter');
			//$this->loadClass('htmldoc', 'htmldoc');
			$this->objSysConfig = & $this->getObject('dbsysconfig', 'sysconfig');
		}
		catch(customException $e) {
			//oops, something not there - bail out
			echo customException::cleanUp();
			//we don't want to even attempt anything else right now.
			die();
		}
    } 
    

    /**
     * Method to handle actions from templates
     * 
     * @access public
     * @param string $action Action to be performed
     * @return mixed Name of template to be viewed or function to call
     */
    public function dispatch($action)
    {
		try{
			switch($action){
				default: 
					return 'default_tpl.php';
					break;
				case "publish":
					$urls = $this->getParam('URLList');
					$this->dbg('######## Begin Publishing ############################');
					$this->dbg('URLLIST = ' . $urls);
					$format = 'pdf'; // Hard coding for now, eventually will be taken from a getParam
					return $this->publish($urls, $format);
				break;

			}
		}
		catch(customException $e) {
			//oops, something not there - bail out
			echo customException::cleanUp();
			//we don't want to even attempt anything else right now.
			die();
		}
    }
    
    /**
     * Grabs the URLs passed in, parse the wiki content and return a Document in the chosen format
     * 
     * @access private 
     * @param string $action Action to be performed
     * @return mixed Name of template to be viewed or function to call
     **************************************************/
	private function publish($urllist, $format)
	{
		$this->dbg('publishing');
		//create a wwDocument first
		$wwDoc = & $this->newObject('wwDocument', 'wikiwriter');
		//$wwDoc = new wwDocument();
		$this->dbg('created wwDocument');

		// Grab all the URLs and parse into an array
		$urls = explode(',', $urllist);
		$this->dbg('Grab all URLs');


		// For each url load into the wwDocument
		foreach($urls as $u)
		{
			$wwDoc->importPage($u);
		}
		$this->dbg('just imported pages');

		// Now build the page and get the file location
		$location = $wwDoc->buildDocument();
		$this->dbg('just built the document');


		//Default format is pdf
		switch($format){
			case 'odt':
				// A nice idea but this really doesn't work, just opens up OpenOffice Web Writer
				// Still, worth some more investigation.
				header("Content-type: application/vnd.oasis.opendocument.text");
				header("Content-Disposition: attachment; filename='test.odt'");
				header("Content-Description: PHP Generated Data");
				echo $page;
			break;
			default:
				// Old version uilizing dompdf library
				// Get PDF rendering of the content
				//$pdfwriter = new DomPDFWrapper();
				//$pdfwriter->generatePDF($page); 
				$this->dbg('about to generate ' . $location);
				$hd = $this->newObject('htmldoc', 'htmldoc');
				$this->dbg('created htmldoc wrapper');
				$output = $hd->render($location);
				$this->dbg('generated');
				//$output = shell_exec($this->objSysConfig->getValue('HTMLDOC_PATH', 'wikiwriter') . 'htmldoc --book -t pdf14 ' . $location);

				header("Content-type: application/pdf");
				header("Content-Disposition: attachment; filename=testing.pdf");
				header("Content-Description: PHP Generated Data");
				echo $output;
			break;
		}

		//TODO: Command to destroy the file created, eventually maybe an option to save teh generated file on teh system

	}
} 
?>
