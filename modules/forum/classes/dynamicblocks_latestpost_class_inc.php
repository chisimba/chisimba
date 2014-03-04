<?php
  // security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Forum dynamic blocks to view topic table
* This class renders forum view dynamic block
* @author Brent van Rensburg
* @copyright (c) 2004 University of the Western Cape
* @package forum
* @version 1
*/
/**
* This class renders forum view dynamic block
*/
class dynamicblocks_latestpost extends object
 {

	/**
	* Constructor method to define the table(default)
	*/
	function init()
	{
		$this->loadClass('label','htmlelements');
        $this->loadClass('link', 'htmlelements');
        
        $this->objLanguage =& $this->getObject('language', 'language'); 
        $this->objPost =& $this->getObject('dbpost');
        
        $this->objIcon = $this->getObject('geticon', 'htmlelements');
        $this->objDateTime =& $this->getObject('dateandtime', 'utilities');
        $this->trimstrObj =& $this->getObject('trimstr', 'strings');
    }
    
    
     /**
     * Method to render a forum
     * @param string $id Record Id of the forum
     * @return string Results
     */
    function renderLatestPost($id)
    {	
		$noPost = $this->objLanguage->languageText('mod_forum_nopostsyet', 'forum');
        $todayAt = $this->objLanguage->languageText('mod_forum_todayat', 'forum');

        $post = $this->objPost->getLastPost($id);

        if ($post == FALSE) {
            $postDetails = '<em>'.$noPost.'</em>';
            $cssClass= NULL;
        } else {
            $cssClass = 'smallText';
            $postLink = new link($this->uri(array( 'module'=> 'forum', 'action' => 'viewtopic', 'id' => $post['topic_id'], 'post'=>$post['post_id'])));
            $postLink->link = stripslashes($post['post_title']);
            $postDetails = '<strong>'.$postLink->show().'</strong>';
            $postDetails .= '<br />'.$this->trimstrObj->strTrim(stripslashes(str_replace("\r\n", ' ', strip_tags($post['post_text']))), 80);
			/*
            if ($post['firstName'] != '') {
                $user = 'By: '.$post['firstName'].' '.$post['surname'].' - ';
            } else {
                $user = '';
            }
			*/
            if ($this->formatDate($post['datelastupdated']) == date('j F Y')) {
                $datefield = $todayAt.' '.$this->formatTime($post['datelastupdated']);
            } else {
                $datefield = $this->formatDate($post['datelastupdated']).' - '.$this->formatTime($post['datelastupdated']);
            }
			
            $postDetails .= '<br /><strong>'.$datefield.'</strong>';
        }
        return $postDetails;
    }
    
    /**
    * Method to format a date.
    */
    function formatDate($date)
    {

        if (isset($date)) {
        $date = getdate(strtotime($date));

        return ($date['mday'].' '.$date['month'].' '. $date['year']);
        }
    }

    /**
    * Method to format the time.
    */
    function formatTime($time)
    {
        $time = getdate(strtotime($time));

        if ($time['minutes'] < 10) {
            $zeroes = '0';
        } else {
            $zeroes = NULL;
        }

        return ($time['hours'].':'.$zeroes.$time['minutes']);
    }

    /**
    * Method to display a link to the forum
    */
    function getLink()
    {
        $lnForum = $this->objLanguage->languageText('mod_forum_name', 'forum');
        $url = $this->uri('', 'forum');
        $this->objIcon->setModuleIcon('forum');
        $objLink = new link($url);
        $objLink->link = $this->objIcon->show();
        $lnStr = '<p>'.$objLink->show();
        $objLink = new link($url);
        $objLink->link = $lnForum;
        $lnStr .= '&nbsp;'.$objLink->show().'</p>';
        return $lnStr;
    }

    /**
    * Display function
    */
    function show()
    {
        $str = $this->showLastPost();
        $str .= $this->getLink();
        return $str;
    }
 }
?>