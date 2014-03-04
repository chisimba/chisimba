<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check
/**
*
* Class for helping in manipulating YouTube API generated XML. Its main
* purpose is to call back to the api class from inside the template class
* to get and format the author details for a given video.
*
* @author Derek Keats
* @category Chisimba
* @package youtube
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class authordetails extends object 
{

    
    /**
    *
    * Standard init method  
    *
    */
    public function init()
    {
        //Instantiate the youtubeapi class
        $this->objYouTube = $this->getObject('youtubeapi', 'youtube');
        $this->author=NULL;
    }
    
    /**
    *
    * Method to access properties in this class
    * 
    * @param string $item The property whose value is being requested
    * @return string The value of the propery being looked up
    * @access public
    *
    */
    public function get($item) {
        return $this->$item;
    }
    
    /**
    *
    * Method to set properties in this class
    * 
    * @param string $key The property whose value is being set
    * @param string $value The value of the property being set
    * @return boolean TRUE It always returns true
    * @access public
    *
    */
    public function set($key, $value)
    {
        $this->$key = $value;
        return TRUE;
    }
    
    /**
    * 
    * Method to show the details for a video once the videoId
    * has been set as a class property
    * 
    * @access public
    * @return string A formatted string containing a table with the details
    *  of the video corresponding to videoId
    * 
    */
    public function showDetails()
    {
        //Get the video data
        if ($this->author==NULL) {
            return " ";
        } else {
            $callCode = $this->objYouTube->getVideoDetailsByAuthor($this->author);
            $apiXml = $this->objYouTube->show($callCode);
            return $this->getFormatted($apiXml);
        }
    }
    
    /**
    * 
    * A method to format the video details into a table for display
    * @param $apiXml The XML returned by the Youtube API
    * @return The details formatted into a table for display
    * @access private
    * 
    */
    private function getFormatted(&$apiXml)
    {
        $this->firstname = htmlentities($apiXml->user_profile->first_name);
        $this->lastname = htmlentities($apiXml->user_profile->last_name);
        $this->about = htmlentities($apiXml->user_profile->about_me);
        $this->age = htmlentities($apiXml->user_profile->age);
        $this->videosUploaded = htmlentities($apiXml->user_profile->video_upload_count);
        $hpage = htmlentities($apiXml->user_profile->homepage);
        $this->homepage = "<a href=\"" .
          $hpage . "\">" . $hpage . "</a>";
        $this->online = htmlentities($apiXml->user_profile->currently_on);
        $this->objLanguage = $this->getObject('language', 'language');
        $table = new htmltable();
        $table->cellspacing = 4;
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_youtube_author', 'youtube'));
        $table->addCell($this->firstname . " " . $this->lastname);
        $table->endRow();
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_youtube_aboutauthor', 'youtube'));
        $table->addCell($this->about);
        $table->endRow();
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_youtube_authage', 'youtube'));
        $table->addCell($this->age);
        $table->endRow();
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_youtube_authhomepage', 'youtube'));
        $table->addCell($this->homepage);
        $table->endRow();
        return $table->show();
    }

}
?>