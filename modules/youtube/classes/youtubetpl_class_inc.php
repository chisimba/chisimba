<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check
/**
*
* Class for manipulating YouTube API generated XML
*
* @author Derek Keats
* @category Chisimba
* @package youtube
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class youtubetpl extends object 
{
    /**
    * 
    * @var $objLanguage String object property for holding the 
    * language object
    * @access private
    * 
    */
    private $objLanguage;
    /**
    * 
    * @var integer $cols the number of columns of thumbnails  
    * 
    * @access private
    * 
    */
    private $cols;
    /**
    * 
    * @var string $ytIdentifier The value of the identifier for the XML  
    * 
    * @access private
    * 
    */
    private $ytIdentifier;
    
    /**
    *
    * Standard init method  
    *
    */
    public function init()
    {
        //Get the language object 
        $this->objLanguage = $this->getObject('language', 'language');
        //Load the link builder and image classes since we use them 
        //  in more than one funciton
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('image', 'htmlelements');
        $this->cols=3;
        $this->hitsPerPage=24;
        $this->ytIdentifier = "digitalfreedom";
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
    * A method to show the requested videos as thumbnails
    * with next and previous links. The videos are formatted
    * into a table.
    * 
    * @param string object $apiXml The XML object returned by the 
    * Youtube API call
    * 
    */
    public function showVideos(& $apiXml, $ytMethod)
    {
        $isInvalid = $this->catchError($apiXml);
        if (!$isInvalid) {
            return $isInvalid;
        } else {
            //loop through each video in the list and display it
            $i = 0;
            $count = 0;
            $this->loadClass('htmltable', 'htmlelements');
            $outerTable = new htmltable();
            //The table width is the width of the thumbnails (130) times the number of columns
            $vidTableWidth = 130 * $this->cols;
            //The outer table width is the width of the thumbnails + 400 for the display
            $outerTable->width=$vidTableWidth + 400;
            //Start the table for the thumbnails
            $table = new htmltable();
            $table->startRow();
            //Set the table width depending on the number of columns
            $table->width = $vidTableWidth;
            $str="";
            //Get the total number of videos
            $total = $apiXml->video_list->total;
            //Get the number of hits per page
            $perPage = $this->getParam('hitsperpage', $this->hitsPerPage);
            $objLink = new link();
            $objImage = new image();
            //Get the videoid, defaulting to none
            $videoId = $this->getParam('videoid', 'none');
            //Get the video player so we can play the first or chosen video
            $this->objYtFilter = $this->getObject('parse4youtube', 'filters');
            if ($videoId == 'none') {
                $displayVideoUrl = $apiXml->video_list->video[0]->url;
                $videoId = $this->objYtFilter->getVideoCode($displayVideoUrl);
            }
            $vidPlayer = $this->getVideoPlayer($videoId) . "<br />";
            //Add the tabbed boxes
            $tab1 = $this->objLanguage->languageText("mod_youtube_chifilter", "youtube")
              . "<br />" . $this->getTextBox($this->getFilterLink($this->getYahooUrl($videoId)));
            $objDetails = $this->getObject('videodetails','youtube');
            $objDetails->set('videoId', $videoId);
            $tab2 = $objDetails->showDetails();
            
            $objAuthor = $this->getObject('authordetails','youtube');
            $objAuthor->set('author', $objDetails->author);
            $tab3 = $objAuthor->showDetails();
            $multiTab  = $this->newObject('tabcontent','htmlelements');
            $multiTab->width ='400px';
            $multiTab->addTab($this->objLanguage->languageText("mod_youtube_filtercode",'youtube'), $tab1, FALSE, '360px');
            $multiTab->addTab($this->objLanguage->languageText("mod_youtube_filterdesc",'youtube'), $tab2, FALSE, '360px');
            $multiTab->addTab($this->objLanguage->languageText("mod_youtube_author",'youtube'), $tab3, FALSE, '360px');
            $vidPlayer .=  $multiTab->show();
            //Use the tooltips for displaying description
            $tooltipHelp = $this->getObject('tooltip','htmlelements');
            //Loop and insert the thumbnails with links 
            foreach($apiXml->video_list->video as $video) {
                $title = $video->title;
                //Keep the title short to not break the layout but use tooltip
                $tooltipHelp->setCaption(htmlentities(substr($title, 0, 15)) . '...');
                $title = htmlentities($title);
                $tooltipHelp->setText($title);
                //Add the link to a filter
                //$title .= $this->getFilterLink($video->url);
                //The thumbnaiil image for the current video
                $objImage->src = $video->thumbnail_url;
                //Set up the link URL
                $videoUrl = $video->url;
                $videoId = $this->objYtFilter->getVideoCode($videoUrl);
                $ytIdentifier = $this->getParam('ytidentifier', $this->ytIdentifier);
                $action=$this->getParam('ytaction', 'view');
                $objLink->href = $this->getViewUrl($ytMethod, $ytIdentifier, $action, $videoId, $perpage);
//echo $objLink->href . "<br />";
                //$objLink->href = htmlentities($video->url);
                //Make the link the image
                $objLink->link = $objImage->show();
//echo $objLink->show() . "<br /><br />";
                //Add the linked image to a table cell
                $tipHelp = $tooltipHelp->show();
                //$table->addCell($objLink->show() . "<br />" . $tipHelp, "130");
                $table->addCell($objLink->show() . "<br />");
                //Using two counters, one to keep track of rows, and one to track the totals
                $i++;
                $count++;
                $flag=FALSE;
                // only cols videos per row (default value=3)
                if ($i == $this->cols) {
                    $i = 0;
                    $table->endRow();
                    //echo $count . "----" . $perPage . "<br />";
                    if ($count != $perPage) {
                        $table->startRow();
                    }
                    $flag=TRUE;
                }
            }
            //If we have not closed the row then close it
            if ($flag==FALSE) {
                $table->endRow();
            }
            $str = $table->show();
            //Clean up the table memory
            unset($table);
            //Count the number of pages        
            if ($total > $perPage) {
                $pages = round($total/$perPage, 0);
            } else {
                $pages = 1;
            }
            $page = $this->getParam('ytpage', 1);
            $nextPage = $this->getNextPageLink($page, $pages);
            $prevPage = $this->getPrevPageLink($page, $pages);
            $arRep=array(
              'PAGE'=>$page,
              'PAGES'=>$pages);
            $pageOf = $this->objLanguage->code2Txt("mod_youtube_pageofpages", "youtube", $arRep);
            $navTable = new htmltable();
            $navTable->width = $vidTableWidth;
            $navTable->startRow();
            $navTable->addCell($prevPage);
            $navTable->addCell($pageOf);
            $navTable->addCell($nextPage, NULL, "top", "right");
            $navTable->endRow();
            $navBar = $navTable->show();
            $str = $navBar . $str . $navBar;
            $outerTable->startRow();
            $outerTable->addCell($str);
            $outerTable->addCell($vidPlayer);
            $outerTable->endRow();
            $outerTable->cellspacing=3;
            return $outerTable->show();            
        }
        

    }  
    
    /**
    * 
    * Method to create the view URL to use with the thumbnails to make a link
    * to the video
    * 
    * @param string $ytMethod The method to call (e.g. by_tag, by_user)
    * @param string $ytIdentifier The value of the item to return (e.g. digitalfreedom, derekkeats)
    * @param string $action The action for the youtube module to perform
    * @param string $videoId The id of the video to play
    * @param string $perpage The number of hits per page
    * @return The URL for viewing the video under the thumbnail
    *  
    */
    private function getViewUrl(&$ytMethod, &$ytIdentifier, &$action, &$videoId, &$perpage)
    {
         $arUri = $this->extractQueryString();
         $arUri['ytmethod'] = $ytMethod;
         $arUri['ytidentifier'] = $ytIdentifier;
         $arUri['ytpage'] = $this->getParam('ytpage', 1);
         $arUri['ytaction'] = $action;
         $arUri['videoid'] = $videoId;
         $arUri['hitsperpage'] = $perpage;
         $curModule = $this->getParam('module', 'youtube');
         return $this->uri($arUri, $curModule);
    }
    
    /**
    * 
    * Method to get and format the Next page link
    * 
    * @param integer $page The page we are on currently
    * @param integer $pages The number of pages
    * @return string The formatted next page link 
    * 
    */
    private function getNextPageLink(&$page, &$pages)
    {
        
        $objIcon = $this->newObject('geticon', 'htmlelements');
        if ($page < $pages) {
            $nextPage = $page+1;
            $ytMethod = $this->getParam('ytmethod', 'by_tag');
            $ytIdentifier = $this->getParam('ytidentifier',$this->ytIdentifier);
            $action=$this->getParam('ytaction', 'view');
            $arUri = $this->extractQueryString();
            $arUri['ytmethod'] = $ytMethod;
            $arUri['ytidentifier'] = $ytIdentifier;
            $arUri['ytpage'] = $nextPage;
            $arUri['ytaction'] = $action;
            //Use the module we are in so any module can have a page of vids
            $curModule = $this->getParam('module', 'youtube');
            $objLink = new link();
            $objLink->href = $this->uri($arUri, $curModule);
            $objIcon->setIcon('next');
            $objIcon->title = $this->objLanguage->languageText("mod_youtube_nextpage", "youtube");
            $objLink->link = $objIcon->show();
            return $objLink->show();
        } else {
            $objIcon->setIcon('next_grey');
            $objIcon->title = $this->objLanguage->languageText("mod_youtube_onlastpage", "youtube");
            return $objIcon->show();
        }
    }
    
    /**
    * 
    * Method to get and format the Previous page link
    * 
    * @param integer $page The page number
    * @param integer $pages The number of pages
    * @return string The formatted next page link 
    * 
    */
    private function getPrevPageLink(&$page, &$pages)
    {
        $prevPage = $page-1;
        $objIcon = $this->newObject('geticon', 'htmlelements');
        if ($page > 1) {
            $prevPage = $page-1;
            $ytMethod = $this->getParam('ytmethod', 'by_tag');
            $ytIdentifier = $this->getParam('ytidentifier', $this->ytIdentifier);
            $action=$this->getParam('ytaction', 'view');
            $arUri = $this->extractQueryString();
            $arUri['ytidentifier'] = $ytIdentifier;
            $arUri['ytpage'] = $prevPage;
            $arUri['ytmethod'] = $ytMethod;
            $arUri['ytaction'] = $action;
            $objLink = new link();
            $curModule = $this->getParam('module', 'youtube');
            $objLink->href = $this->uri($arUri, $curModule);
            $objIcon->setIcon('prev');
            $objIcon->title = $this->objLanguage->languageText("mod_youtube_prevpage", "youtube");
            $objLink->link = $objIcon->show();
            return $objLink->show();
        } else {
            $objIcon->setIcon('prev_grey');
            $objIcon->title = $this->objLanguage->languageText("mod_youtube_onfirstpage", "youtube");
            return $objIcon->show();
        }
    }
    
    /**
     * 
     * A method to extract the querystring into an array of keys and values
     * to use to rebuild the navigation so that the navigation stays within
     * the current module and page. It excludes ones that are built up in the
     * view class (this class).
     * 
     * @return string array An array of keys and values from the querystring
     * 
     */
    function extractQueryString()
    {
        //Extract the querystring into a string
        $qs = $_SERVER['QUERY_STRING'];
        //Create an array of excluded parameters
        $arExcluded = array('ytpage', 'hitsperpage', 'ytidentifier', 'ytcols', 'ytaction', 'module', 'videoid');
        //Extract the querystring into its own array
        $qsArray = explode("&", $qs);
        $arRet = array();
        foreach($qsArray as $entry) {
            $tmpAr = explode("=", $entry);
            $key = $tmpAr[0];
            $value = $tmpAr[1];
            if (!in_array($key, $arExcluded)) {
                $arRet[$key] = $value;
            }
        }
        return $arRet;
    }
    
    /**
    * 
    * Method to return a Filter plugin link for 
    * Chisimba from a Youtube video URL
    * 
    * @param string $url A valid Youtube video URL
    *  
    */
    private function getFilterLink($url)
    {
        return '&#91;YOUTUBE&#93;' . $url . '&#91;/YOUTUBE&#93;';
    }
    
    /**
    * 
    * A method to return a search by tag searchbox
    * @return string A form containing the search box and a submit button
    * 
    */
    public function getTagSearchBox()
    {
        $this->loadClass('form','htmlelements');
        $objForm = new form('vidtag');
        $objForm->setAction($this->uri(array('ytmethod'=>'by_tag'),'youtube'));
        $this->loadClass('textinput','htmlelements');
        $identifier = new textinput('ytidentifier');
        $objForm->setDisplayType(1);
        $objForm->addToForm($this->objLanguage->languageText("mod_youtube_dispbytag", "youtube") . "<br />");
        $objForm->addToForm($identifier);
        $this->loadClass('button','htmlelements');
        $btn = $this->objLanguage->languageText("mod_youtube_showvideos", "youtube");
        $objButton=new button('bytagbtn');
        $objButton->setToSubmit();
        $objButton->setValue($btn);
        $objForm->addToForm($objButton->show());
        return $objForm->show();
    }
    
    /**
    * 
    * A method to return a search by playlist searchbox
    * @return string A form containing the search box and a submit button
    * 
    */
    public function getPlSearchBox()
    {
        $this->loadClass('form','htmlelements');
        $objForm = new form('vidpl');
        $objForm->setAction($this->uri(array('ytmethod'=>'by_playlist'),'youtube'));
        $this->loadClass('textinput','htmlelements');
        $identifier = new textinput('ytidentifier');
        $objForm->setDisplayType(1);
        $objForm->addToForm($this->objLanguage->languageText("mod_youtube_dispbypl", "youtube") . "<br />");
        $objForm->addToForm($identifier);
        $this->loadClass('button','htmlelements');
        $btn = $this->objLanguage->languageText("mod_youtube_showvideos", "youtube");
        $objButton=new button('byplbtn');
        $objButton->setToSubmit();
        $objButton->setValue($btn);
        $objForm->addToForm($objButton->show());
        return $objForm->show();
    }

    /**
    * 
    * A method to return a search by user searchbox
    * @return string A form containing the search box and a submit button
    * 
    */    
    public function getUserSearchBox()
    {
        $this->loadClass('form','htmlelements');
        $objForm = new form('viduser');
        $objForm->setAction($this->uri(array('ytmethod'=>'by_user'),'youtube'));
        $this->loadClass('textinput','htmlelements');
        $identifier = new textinput('ytidentifier');
        $objForm->setDisplayType(1);
        $objForm->addToForm($this->objLanguage->languageText("mod_youtube_dispbyuser", "youtube") . "<br />");
        $objForm->addToForm($identifier);
        $this->loadClass('button','htmlelements');
        $btn = $this->objLanguage->languageText("mod_youtube_showvideos", "youtube");
        $objButton=new button('byusrbtn');
        $objButton->setToSubmit();
        $objButton->setValue($btn);
        $objForm->addToForm($objButton->show());
        return $objForm->show();
    }
    
   /**
   * 
   * A method to show on the left panel the method that is
   * currently being called (by tag, by user, etc.)
   * 
   * @return string The text formatted for display in the left panel
   * 
   */
   public function showMethod()
   {
        //Get the method to use and default to by_tag
        $ytMethod = $this->getParam('ytmethod', 'by_tag');
        //Get the tag or user or other identifier and default to digitalfreedom
        $ytIdentifier = $this->getParam('ytidentifier', 'digitalfreedom');
        switch ($ytMethod) {
                case "by_tag":
                    $ret = $this->objLanguage->languageText("mod_youtube_bytag", "youtube")
                      . ": " . $ytIdentifier;
                    break;
                case "by_user":
                    $ret = $this->objLanguage->languageText("mod_youtube_byuser", "youtube")
                      . ": " . $ytIdentifier;
                    break;
                case "by_playlist":
                    $ret = $this->objLanguage->languageText("mod_youtube_bypl", "youtube")
                      . ": " . $ytIdentifier;
                    break;
                default:
                    $ret = $this->objLanguage->languageText("mod_youtube_unknownmethod", "youtube")
                      . ": " . $ytMethod;
                    break;
        }
       return $ret;
    } 
    
    /**
    * 
    * Method to use the parse4youtube filter class to return
    * a Youtube video player to play the video identified by
    * $videoId
    * 
    * @param sting $videoId The videoid of the youtube video to play
    * 
    */
    public function getVideoPlayer($videoId)
    {
        return $this->objYtFilter->getVideoObject($videoId);
    }
    
    /**
    * 
    * Method to convert a videoId into a full Yahoo video URL. It
    * is mainly used to generate the Chsisimba filter link from the
    * videoId of the currently playing video.
    * 
    * @param string $videoId The id of the video
    * @return string The formatted URL
    * 
    */
    public function getYahooUrl(&$videoId)
    {
        return "http://www.youtube.com/watch?v=" . $videoId;
    }
    
    /**
    *
    * A method to return a simple text box, with content, as used for the
    * Chisimba Youtube filter link
    * 
    * @param string $contents The text to put into the box
    * @return string The formatted text box with content
    *  
    */
    public function getTextBox($contents)
    {   
        /*$this->loadClass('textinput','htmlelements');
        $boxywoxy = new textinput('ytbox', $contents, NULL, 70);*/
        return "<div style='border: 1px solid grey; background: white; padding:5px; width: 390px; height: 60px; overflow: auto;' id='faketextbox' name='faketextbox'>$contents</div>";
    }
    

    
    //WORKING HERE
    /**
    * 
    * If it works, it returns <ut_response status="ok"> so we just need 
    * to check that to see if there was an error. A full error looks like
    *   <ut_response status="fail">
    *     <error>
    *       <code>8</code>
    *           <description>Bad or unknown dev_id specified.</description>
    *     </error>
    *    </ut_response> 
    * 
    */
    public function catchError(& $apiXml) 
    {
        //return TRUE;
        $success = $apiXml['status'];
        if ($success == 'ok') {
            return TRUE;                   
        } else {
            $erCode = $apiXml->ut_response->error->code;
            $erDesc = $apiXml->ut_response->error->description;
            return $erDesc . "($erCode)";
        }
    }
}
?>