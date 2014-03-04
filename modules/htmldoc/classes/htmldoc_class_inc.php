<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
} 
// end security check

/**
* htmldoc Object 
*
* This is merely a chisimba class encapsulating the htmldoc tool (http://htmldoc.org) so other modules
* can utilize it.  From the website: HTMLDOC converts Hyper-Text Markup Language ("HTML") input files 
* into indexed HTML, Adobe¨ PostScript¨, or Adobe Portable Document Format ("PDF") files.
*
* Notes for any author continuing development
* 	Here are some features to add:
* 	- verify executable exists, otherwise return an error
* 	- verify that executable can actually be run
*	- commands for setting parameters, see htmldoc documentation for details on params
* 	- consider specific commands for the more common parameters
*	- run command
*	- specify output (file, stream, to web)
*	- specify input (by string, file reference, url, etc)
*
* @author Ryan Whitney, ryan@greenlikeme.org 
*/
class htmldoc extends object 
{

	/**
	 * Variable htmldocPath String identifying the path to the htmldoc binary 
	 */
	 private $htmldocPath = ''; 

	/**
	 * Variable objSysConfig Object for accessing wikiwriter module configuration 
	 */
	public $objSysConfig = '';

	/**
	 * Constructor
 	 */ 
	public function init()
	{
		try
		{
			$this->objSysConfig = & $this->getObject('dbsysconfig', 'sysconfig');

			// First check that we can utilize htmldoc
			// Check configuration file
			if($this->objSysConfig->checkIfSet('HTMLDOC_PATH', 'htmldoc')){
				$this->htmldocPath = $this->objSysConfig->getValue('HTMLDOC_PATH', 'htmldoc');
			}
			// TODO: If that doesn't work, identify the os and check common paths
			// TODO: Add executables
			// TODO: This should be run once and these things checked or checked everytime?
			// TODO: Check that you can run the script
			// If no executables are found or we are unable to run the script, throw an exception
		}
		catch(customException $e)
		{
			echo customException::cleanUp();
			die();
		}
	}


	/**
    * Renders the pdf from the given html source 
    * 
    * @access public
    * @param string $path path to the htmldoc binary
    * @param boolean $asBook Flag on whether to render the PDF with table of contents
    * @param boolean $asWebPage Flag on whether to render the PDF as a webpage or not.
    * @param string $destination path to save the rendered binary to
    * @return mixed the pdf content
    *
    * If destination is given, user has to ensure that any existing files are removed.
    */ 
	public function render($path, $asBook=TRUE, $asWebPage=FALSE, $destination='')
	{
		$asBook = ($asBook) ? ' --book' : '';
		$asWebPage = ($asWebPage) ? ' --webpage' : '';
        
        if ($destination != '') {
            $destination = ' -f '.$destination;
        }
        
        //TODO: High risk for failure, should wrap in try/catch
        $shellCommand = $this->htmldocPath . 'htmldoc '.$asBook.' '.$asWebPage.' -t pdf14 ' . $path.' '.$destination;
        
        //return $shellCommand;
		return shell_exec($shellCommand);

	}

}
?>