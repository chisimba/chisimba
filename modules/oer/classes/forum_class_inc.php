<?php
/**
 * This class contains util methods for displaying full original product details
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @version    0.001
 * @package    oer

 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * @author     davidwaf davidwaf@gmail.com
 */

/**
 * @package oer
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * The forum block class displays the last post
 * 
 */
class forum extends object {

    /**
     * Constructor
     */
    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('mod_forum_lastpostindefault', 'forum');
        $this->objPost = & $this->getObject('dbpost','forum');
        $this->objForum = & $this->getObject('dbforum','forum');
        $this->trimstrObj = & $this->getObject('trimstr', 'strings');

        $this->contextObject = & $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->contextObject->getContextCode();

        // If not in context, set code to be 'root'
        if ($this->contextCode == '') {
            $this->contextCode = 'root';
        }

        $this->objIcon = & $this->newObject('geticon', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
    }

    /**
     * Method to show the last post in the default forum
     *
     * @param string $count
     * @return string
     */
    function showLastNPosts($count) {
        $postDetails="";
        $noPost = $this->objLanguage->languageText('mod_forum_nopostsyet', 'forum');
        $todayAt = $this->objLanguage->languageText('mod_forum_todayat', 'forum');

        $forumId = $this->objForum->getDefaultForum($this->contextCode);
        $posts = $this->objPost->getLastNPosts($forumId['id']);
        foreach ($posts as $post) {
            if ($post == FALSE) {
                $postDetails = '<em>' . $noPost . '</em>';
                $cssClass = NULL;
            } else {
                $cssClass = 'smallText';
                $postLink = new link($this->uri(array('action' => 'viewtopic', 'id' => $post['topic_id'], 'post' => $post['post_id']),"forum"));
                $postLink->link = stripslashes($post['post_title']);
                $postDetails.= '<strong>' . $postLink->show() . '</strong>';
                $postDetails .= '<br />' . $this->trimstrObj->strTrim(stripslashes(str_replace("\r\n", ' ', strip_tags($post['post_text']))), 80);

                if ($this->formatDate($post['datelastupdated']) == date('j F Y')) {
                    $datefield = $todayAt . ' ' . $this->formatTime($post['datelastupdated']);
                } else {
                    $datefield = $this->formatDate($post['datelastupdated']) . ' - ' . $this->formatTime($post['datelastupdated']);
                }

                $postDetails .= '<div id="forum_post"><strong>' . $datefield . '</strong></div>';
            }
        }
        return $postDetails;
    }

    /**
     * Method to format a date.
     *
     * @param string $date
     * @return string date
     */
    function formatDate($date) {

        if (isset($date)) {
            $date = getdate(strtotime($date));

            return ($date['mday'] . ' ' . $date['month'] . ' ' . $date['year']);
        }
    }

    /**
     * Method to format the time.
     *
     * @param string $time
     * @return string time
     */
    function formatTime($time) {
        $time = getdate(strtotime($time));

        if ($time['minutes'] < 10) {
            $zeroes = '0';
        } else {
            $zeroes = NULL;
        }

        return ($time['hours'] . ':' . $zeroes . $time['minutes']);
    }

    /**
     * Method to display a link to the forum
     */
    function getLink() {
        $lnForum = $this->objLanguage->languageText('mod_forum_name', 'forum');
        $url = $this->uri('', 'forum');
        $this->objIcon->setModuleIcon('forum');
        $objLink = new link($url);
        $objLink->link = $this->objIcon->show();
        $lnStr = '<p>' . $objLink->show();
        $objLink = new link($url);
        $objLink->link = $lnForum;
        $lnStr .= '&nbsp;' . $objLink->show() . '</p>';
        return $lnStr;
    }

    /**
     * Display function
     */
    function show() {
        $str = $this->showLastPost();
        $str .= $this->getLink();
        return $str;
    }

}

?>