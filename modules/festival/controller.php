<?php
/* ----------- controller class extends controller for festival module------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Festival Text to Speech synthesizer engine wrapper class
 * @author Paul Scott
 * @copyright GNU GPL (UWC 2005)
 * @package nextgen
 */
class festival extends controller
{

    /**
    * @public string $action The action parameter from the querystring 
    */
    public $action;
    /**
     * @public string $string The string that is passed to the tts engine
     */
    public $string;

    /**
    * Standard constructor method 
    */
    function init()
    {
        //Retrieve the action parameter from the querystring
        $this->action = $this->getParam('action', Null);
        //Create an instance of the User object
        $this->objUser = $this->getObject("user", "security");
        //Create an instance of the language object
        $this->objLanguage = $this->getObject("language", "language");
        //create an instance of the TTS engine class
        $this->objTTS = $this->newObject("ttsengine","festival");
        $this->objConfig = $this->getObject("altconfig","config");
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }

   /**
    * Standard dispatch method 
    */
    function dispatch()
    {   	  		
        switch ($this->action) 
        {
            
        	case null:            
            case "demo":
            	//Create the utterances folder if it doesn't exist 
            	if(file_exists($this->objConfig->getcontentBasePath(). '/utterances/'))
            	{
           			return "main_tpl.php";
           		}
           		else
           		{
           			mkdir('/var/www/chisimba_framework/app/usrfiles/utterances');
           			return "main_tpl.php";
           		}        			           		           			
            break;
            case 'convert':
            	//Create a file that recieves the name from the textinput
            	$file = $this->getParam('title');
            	//Copy filename into the utterances folder
            	$oggFile = $this->objConfig->getcontentBasePath(). "/utterances/{$file}";
            	//Create the output ogg file
            	$filename = "{$oggFile}.txt";
            	//Set up the output file
            	$outputfile = basename($filename,'.txt');
           		$outputfile = $this->objConfig->getcontentBasePath(). '/utterances/'. $outputfile . '.wav';
           		//Set the comment for the ttsengine recieved from the text area
           		$comment = $this->getParam('comment');
           		//Use the text2Wav function to convert the comment to speech
					$linkfile = $this->objTTS->text2Wav($comment,$filename,$outputfile);
					//Convert from wav to ogg
            	shell_exec ("ffmpeg -i $outputfile -ar 44100 -ab 128 {$oggFile}.ogg");
            	//Delete the wav file
            	unlink($outputfile);
            	//Send the file to the player
            	$this->setVar('oggFile', $this->objConfig->getSiteRoot()."usrfiles/utterances/$file.ogg");
            	//$this->setVar('linkFile', $oggFile. '.ogg');
            	//Return the template
            	return "status_tpl.php";
            	break;
            	case 'download':
            		//Create a file that recieves the link
            		$file = $this->getParam('file');
            		//Send the file to the template
            		$this->setVar('oggFile', $file);
            		//Return the template
            		return "download_tpl.php";
            	break;
        }//end the switch
    }//end the dispatch method
}//end class