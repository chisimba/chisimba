<?php
/**
* @package discussion
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* The discussion block class displays the last post
* @author Megan Watson
*/

class block_discussion extends object
{
    /**
    * Constructor
    */
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('mod_discussion_lastpostindefault', 'discussion');
        $this->objPost =& $this->getObject('dbpost');
        $this->objDiscussion =& $this->getObject('dbdiscussion');
        $this->trimstrObj =& $this->getObject('trimstr', 'strings');

        $this->contextObject =& $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->contextObject->getContextCode();

        // If not in context, set code to be 'root'
        if ($this->contextCode == ''){
            $this->contextCode = 'root';
        }

        $this->objIcon =& $this->newObject('geticon', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
    }

    /**
    * Method to show the last post in the default discussion
    */
    function showLastPost()
    {
        $noPost = $this->objLanguage->languageText('mod_discussion_nopostsyet', 'discussion');
        $todayAt = $this->objLanguage->languageText('mod_discussion_todayat', 'discussion');

        $discussionId = $this->objDiscussion->getDefaultDiscussion($this->contextCode);
        $post = $this->objPost->getLastPost($discussionId['id']);

        if ($post == FALSE) {
            $postDetails = '<em>'.$noPost.'</em>';
            $cssClass= NULL;
        } else {
            $cssClass = 'smallText';
            $postLink = new link($this->uri(array( 'module'=> 'discussion', 'action' => 'viewtopic', 'id' => $post['topic_id'], 'post'=>$post['post_id'])));
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
    * Method to display a link to the discussion
    */
    function getLink()
    {
        $lnDiscussion = $this->objLanguage->languageText('mod_discussion_name', 'discussion');
        $url = $this->uri('', 'discussion');
        $this->objIcon->setModuleIcon('discussion');
        $objLink = new link($url);
        $objLink->link = $this->objIcon->show();
        $lnStr = '<p>'.$objLink->show();
        $objLink = new link($url);
        $objLink->link = $lnDiscussion;
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