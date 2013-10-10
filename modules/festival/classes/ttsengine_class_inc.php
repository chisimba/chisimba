<?php
/**
 * Festival Wrapper class for Kewl.NextGen
 * @author Paul Scott
 * @package nextgen
 * @version 0.9
 */
class ttsengine
{
	/**
	 * Standard init function, we don't need to initialise anything here
	 */
	function init()
	{
		
	}
	
	/**
	 * Method to wrap API calls to the festival tts engine
	 * This function will create an utterance and output it to a wav file
	 * @author Paul Scott
	 * @param $string
	 * @return waveform
	 */
	function text2Wav($string, $filename, $outputfile)
	{
		if (!$handle = fopen($filename, "w")) {
			return false;
		}
		// Write $string to our opened file.
		if (fwrite($handle, $string) === FALSE) {
			return false;
		}

		fclose($handle);

		//initialise and execute the fest engine
		$cmd = "text2wave $filename -o $outputfile";
		exec($cmd);
		unlink($filename);
		return $outputfile;	
	}
	
	/**
	 * Method to create a WAV file from a text input.
	 * @author Paul Scott
	 * @param $string
	 * @return wav file
	 */
	function text2Speech($string)
	{
		if (!$handle = fopen($filename, "w")) {
			return false;
		}
		// Write $string to our opened file.
		if (fwrite($handle, $string) === FALSE) {
			return false;
		}

		fclose($handle);

		//initialise and execute the fest engine
		$cmd = "festival --tts $string";
		exec($cmd);
		return true;
	}
}//end class
?>