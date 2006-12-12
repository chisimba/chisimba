<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Audio file converter class
 * WMA 2 Ogg
 * MP3 2 Ogg
 *
 * @access public
 * @author Paul Scott
 * @copyright Paul Scott
 * @link http://directory.fsf.org/all/mp32ogg.html
 * @link http://www.mplayerhq.hu/design7/news.html
 * @link http://directory.fsf.org/OggEnc.html
 */

class audioconvert extends object
{
	/**
	 * Constructor method, checks to see if necessary binaries exist
	 *
	 * @access public
	 * @param void
	 * @return exception on error
	 */
	public function init()
	{
		try {
			$this->objLanguage = $this->getObject("language", "language");
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}

		if (!@file_exists('/usr/bin/mp32ogg'))
		{
			throw new customException($this->objLanguage->languageText("mod_utilities_nomp32ogg", "utilities") ." http://directory.fsf.org/all/mp32ogg.html");
		}
		if (!@file_exists('/usr/bin/mplayer'))
		{
			throw new Exception($this->objLanguage->languageText("mod_utilities_nomplayer","utilities") . " http://www.mplayerhq.hu/design7/news.html");
		}
		if (!@file_exists('/usr/bin/oggenc'))
		{
			throw new Exception($this->objLanguage->languageText("mod_utilities_nooggenc","utilities") . " http://directory.fsf.org/OggEnc.html");
		}
	}

	/**
	 * Method to encode an MP3 file to Ogg Vorbis format
	 *
	 * @param string $file
	 * @param bool string $delete
	 * @return void
	 */
	public function mp32OggFile($file, $delete = FALSE)
	{
		if(file_exists($file))
		{
			$filename = basename($file);
			$path = str_replace($filename, "",$file);
			$res = @system("/usr/bin/mp32ogg $file $path");
			if($delete == TRUE)
			{
				unlink($file);
			}
			return $res;
		}
		else {
			throw new customException($this->objLanguage->languageText("mod_utilities_file", "utilties") . $file . $this->objLanguage->languageText("mod_utilities_noconvert", "utilities"));
		}

	}

	/**
	 * Method to convert a WMA (Windows Media File) to Ogg Vorbis Format
	 *
	 * @param string $file
	 * @return void
	 */
	public function wma2Ogg($file)
	{
		if(file_exists($file))
		{
			$filename = basename($file);
			$path = str_replace($filename, "",$file);
			chdir($path);
			@system("mplayer $file -ao pcm:file=$file.wav");
			@system("oggenc \"$file.wav\" ");
			unlink($file.".wav");
		}
		else {
			throw new customException($this->objLanguage->languageText("mod_utilities_file", "utilties") . $file . $this->objLanguage->languageText("mod_utilities_noconvert", "utilities"));
		}
	}
}
?>