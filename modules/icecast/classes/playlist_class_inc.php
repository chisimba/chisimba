<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
class playlist extends object
{
	/**
	* @var array List of *.ogg files to play
	*/
	 public $playlist;
   public function init()
	{
        // Create an instance of the language object
        $this->objLanguage = &$this->getObject('language', 'language');
	}
	public function get()
	{
        //Get the current context
        $objContext =& $this->getObject('dbcontext','context');
        $contextCode = $objContext->getContextCode();
        //Get the base path for the files
        $objConfig =& $this->getObject('config', 'config');
        $dir = 
			$objConfig->getValue('KEWL_CONTENT_BASEPATH')
          	."content/$contextCode/media/";
		// Recursively search for all *.ogg files in $dir
		$objFilemanager	=& $this->getObject('filemanager','filemanager');
		$this->playlist = $objFilemanager->globr($dir, '*.ogg');
		// Write playlist to *.m3u file
		$filename = "{$dir}playlist.m3u";
		if ($fp = @fopen($filename, 'w')) {
			if (!empty($this->playlist)) {
			    foreach ($this->playlist as $path) {
				    @fwrite($fp, $path."\n");
				}
			}
			@fclose($fp);
		}
		// Send file through socket to icecast server
		if (!$fp = @fsockopen ("127.0.0.1", 7, $errno, $errstr, 30)) {
		    die ("$errstr ($errno)<br>\n");
		} else {
			$lines = @file($filename);
			if (!empty($lines)) {
			    foreach ($lines as $line) {
				    @fputs ($fp, "$line\n");
				}
			}
		    @fclose ($fp);
		}
	}
}
?>
