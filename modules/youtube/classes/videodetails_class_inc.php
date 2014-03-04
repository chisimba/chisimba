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
* to get and format the video details.
*
* @author Derek Keats
* @category Chisimba
* @package youtube
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class videodetails extends object 
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
        $this->videoId=NULL;
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
        if ($this->videoId==NULL) {
            return " ";
        } else {
            $callCode = $this->objYouTube->getVideoDetailsByVideoId($this->videoId);
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
        $this->author = htmlentities($apiXml->video_details->author);
        $this->title = htmlentities($apiXml->video_details->title);
        $this->description = htmlentities($apiXml->video_details->description);
        $this->tags =  $apiXml->video_details->tags;
        $this->avgRating = $apiXml->video_details->rating_avg;
        $this->ratingCount = $apiXml->video_details->rating_count;
        $this->updated = $apiXml->video_details->update_time;
        $this->comments = $apiXml->video_details->comment_count;
        $this->uploaded = $apiXml->video_details->upload_time;
        $this->lengthSecs = $apiXml->video_details->length_seconds;
        $views =  $apiXml->video_details->view_count;
        
        $this->objLanguage = $this->getObject('language', 'language');
        
        $table = new htmltable();
        $table->cellspacing = 4;
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_youtube_author', 'youtube'));
        $table->addCell($this->author);
        $table->endRow();
        
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_youtube_title', 'youtube'));
        $table->addCell($this->title);
        $table->endRow();

        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_youtube_description', 'youtube'));
        $table->addCell($this->description);
        $table->endRow();
        
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_youtube_tags', 'youtube'));
        $table->addCell($this->tags);
        $table->endRow();
        return $table->show();
    }
    
    /**
    *
    * Method to convert space delimited tags into an array
    * @param string $tags A string of space delimited tags
    * 
    * @return string array An array of tags
    * @access public 
    * 
    */
    public function tagsToArray(&$tags)
    {
        return explode(" ", $tags);
    }
}
?>