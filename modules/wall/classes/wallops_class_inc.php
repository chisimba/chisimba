<?php

/**
 *
 * A simple wall module operations object
 *
 * A simple wall module that makes use of OEMBED and that tries to look a bit
 * like Facebook's wall. This is the operations class. The module creates wall
 * posts (status messages) and comments (or replies) linked to each post or
 * status message
 *   WALL POST MESSAGE
 *       Reply to it
 *       Reply to it
 *   ANOTHER WALL POST MESSAGE
 *       Reply to it
 *
 *   ...etc...
 *
 * PHP version 5
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
 * @category  Chisimba
 * @package   wall
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbwall.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
        /**
         * The $GLOBALS is an array used to control access to certain constants.
         * Here it is used to check if the file is opening in engine, if not it
         * stops the file from running.
         *
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         *
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * A simple wall module operations object
 *
 * A simple wall module that makes use of OEMBED and that tries to look a bit
 * like Facebook's wall. This is the operations class.
 *
 * @author Derek Keats
 * @package wall
 *
 */
class wallops extends object {

    /**
     *
     * @var string Object $objDbwall String for the model object
     * @access public
     *
     */
    public $objDbwall;

    /**
     *
     * @var string Object $objUser String for the user object
     * @access public
     *
     */
    public $objUser;

    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;

    /**
     *
     * @var string Object $objDd String for the date-difference object (for human dates)
     * @access public
     *
     */
    public $objDd;

    /**
     *
     * @var integer $wallType The wall type (1,2,3)
     * @access public
     *
     */
    public $wallType = FALSE;

    /**
     *
     * @var string $loadingImage The gif animation for loading
     * @access public
     *
     */
    public $loadingImage = '<img src="skins/_common/icons/loading_bar.gif" alt=""Loading..." />';

    /**
     *
     * Intialiser for the wall database connector
     * @access public
     * @return VOID
     *
     */
    public function init() {
        // Set the jQuery version to the latest functional
        //$this->setVar('JQUERY_VERSION', '1.4.2');
        // Create an instance of the database class.
        $this->objDbwall = & $this->getObject('dbwall', 'wall');
        // Load jQuery Oembed.
        $oEmb = $this->getObject('jqoembed', 'oembed');
        $oEmb->loadOembedPlugin();
        // Load the functions specific to this module.
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('oembed.js'));
        // Load the functions specific to this module.
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('wall.js'));
        // Instantiate the user object.
        $this->objUser = $this->getObject('user', 'security');
        // Instantiate the language object.
        $this->objLanguage = $this->getObject('language', 'language');
        // Get an instance of the humanize date class.
        $this->objDd = $this->getObject('translatedatedifference', 'utilities');
        // Load all javascript values so they are available to the included script
        $this->loadScript();
    }

    /**
     *
     * Get an object wall (walltype=4, retrieve by identifier)
     *
     * @param string $keyName The key (should  always be identifier, but I made it generic)
     * @param string $keyValue The 'identifier' string for the object wall
     * @param integer $page The page number
     * @param integer $num The number of items per page
     * @return string The rendered wall
     * 
     * @WORKINGHERE
     *
     */
    public function showObjectWall($keyName, $keyValue, $page=0, $num=10) {
        $wallType = 4;
        $posts = $this->objDbwall->getMorePosts($wallType, $page, $keyName, $keyValue, $num);
        $numPosts = $this->objDbwall->countPosts($wallType, FALSE, $keyName, $keyValue);
        if ($numPosts <= 10) {
            return $this->addToWrapper(
                            $this->showPostBox($wallType, $keyName, $keyValue)
                            . $this->showPosts($posts, $numPosts, $wallType, $keyValue, $num, TRUE, $keyValue), $keyValue
            );
        } else {
            return $this->addToWrapper(
                            $this->showPostBox($wallType, $keyName, $keyValue)
                            . $this->showPosts($posts, $numPosts, $wallType, $keyValue, $num, FALSE), $keyValue
            );
        }
    }

    /**
     *
     * Render the wall posts for display
     *
     * @param string $wallType The wall type (0=site wall, 1=personal or user wall, 2=context wall)
     * @param integer $num The number of results to return, defaulting to 10
     * @return string The formatted wall posts
     *
     */
    public function showWall($wallType, $num=10) {
        $this->wallType = $wallType;
        $page = 0;
        switch ($wallType) {
            case "4":
                $keyName = 'identifier';
                $keyValue = $this->getParam($keyName, NULL);
                $posts = $this->objDbwall->getMorePosts($wallType, $page, $keyName, $keyValue, $num);
                break;
            case "3":
                $objContext = $this->getObject('dbcontext', 'context');
                if ($objContext->isInContext()) {
                    $currentContextcode = $objContext->getcontextcode();
                    $keyValue = $currentContextcode;
                    $keyName = 'identifier';
                    $posts = $this->objDbwall->getMorePosts($wallType, $page, $keyName, $keyValue, $num);
                } else {
                    // @TODO deal with this situation better
                    return NULL;
                }
                break;
            case "2":
                $objGuessUser = $this->getObject('bestguess', 'utilities');
                $ownerId = $objGuessUser->guessUserId();
                $keyValue = $ownerId;
                $keyName = 'ownerid';
                $posts = $this->objDbwall->getMorePosts($wallType, $page, $keyName, $keyValue, $num, $ownerId);
                break;
            case "1":
            default:
                $keyName = 'identifier';
                $keyValue = 'sitewall';
                $posts = $this->objDbwall->getMorePosts($wallType, 0, NULL, NULL, 10);
                break;
        }
        $numPosts = $this->objDbwall->countPosts($wallType, FALSE, $keyName, $keyValue);



//WORKING HERE






        if ($numPosts <= 10) {
            return $this->addToWrapper(
                            $this->showPostBox($wallType, $keyName, $keyValue)
                            . $this->showPosts($posts, $numPosts, $wallType, $keyValue, $num, TRUE, $keyValue), $keyValue
            );
        } else {
            return $this->addToWrapper(
                            $this->showPostBox($wallType, $keyName, $keyValue)
                            . $this->showPosts($posts, $numPosts, $wallType, $keyValue, $num, FALSE), $keyValue
            );
        }
    }

    /**
     * Adds output to the base wrapper DIV for the wall.
     *
     * @param string $str The string to wrap
     * @return string The wrapped string
     * @access private
     *
     */
    private function addToWrapper($str, $wallid) {
        return "\n\n<div class='wall_wrapper' id='wall_wrapper_{$wallid}'>\n" . $str . "\n</div>\n\n";
    }

    /**
     *
     * Adds output to the wall DIV for the wall.
     *
     * @param string $str The string to wrap
     * @return string The wrapped string
     * @access private
     *
     */
    private function addToWall($str, $wallid) {
        return "\n\n<div class='wall' id='wall_{$wallid}'>\n{$str}\n</div>\n\n";
    }

    /**
     *
     * Get the next 10 older posts.
     *
     * @return string The next 10 posts formatted for Ajax read
     * @access public
     *
     */
    public function nextPosts($wallid) {
        // Retrieve the wall type.
        $wallType = $this->getParam('walltype', FALSE);
        if ($wallType) {
            switch ($wallType) {
                case "2":
                case "personal":
                    $keyValue = $this->getParam('key', NULL);
                    $keyName = 'ownerid';
                    break;
                case "3";
                    $objContext = $this->getObject('dbcontext', 'context');
                    if ($objContext->isInContext()) {
                        $currentContextcode = $objContext->getcontextcode();
                        $keyValue = $currentContextcode;
                        $keyName = 'identifier';
                    } else {
                        $keyValue = NULL;
                        $keyname = NULL;
                    }
                    break;
                case "4";
                    $keyName = 'identifier';
                    $keyValue = $wallid;
                    break;
                case "1":
                case "sitewall":
                default:
                    $keyValue = '1';
                    $keyName = 'walltype';
                    break;
            }
            // Initialise the return string.
            $ret = "";
            // Retrieve the page number or set it to zero if none.
            $page = $this->getParam('page', 0);
            // Stick with pagesize of 10 for now.
            $pageSize = 10;
            // Count the number of records that correspond to our needs.
            $numPosts = $this->objDbwall->countPosts($wallType, FALSE, $keyName, $keyValue);
            // Calculate the number of pages.
            $totalPages = intval($numPosts / $pageSize) + 1;
            // set the next page to display.
            $pageDisp = $page + 1;
            // First record to show from the recordset
            $startRow = $page * $pageSize;
            // The point at which we display no more links
            $stopPoint = $totalPages;
            // We are on the last page
            if ($pageDisp == $stopPoint) {
                $hideMoreLink = TRUE;
            } else {
                $hideMoreLink = FALSE;
            }
            $rem = $totalPages - $pageDisp;
            //die($keyname . ' = ' . $keyValue);
            $posts = $this->objDbwall->getMorePosts($wallType, $page, $keyName, $keyValue);
            $ret .= $this->showPosts($posts, $numPosts, $wallType, $wallid, 10, $hideMoreLink);
            $ret ="<div class='wall_more_posts'>$ret</div>";
        } else {
            $ret = $this->objLanguage->languageText("mod_wall_nowalltype", "wall", "No wall type given");
        }
        return $ret;
    }

    /**
     *
     * Take an array of posts, and render the Chisimba wall.
     *
     * @param string array $posts An array of posts
     * @param integer $numPosts The number of posts that exist in the database
     * @param integer $wallType The wall type (1, 2, 3)
     * @param integer $num The number of posts to return
     * @param boolean $hideMorePosts TRUE|FALSE whether or not to hide the link to more posts
     * @param string $makePosterNameALink  whether or not to make name of the poster a link
     * @param string $testIfLoggedIn  test the login status of this user before displaying certain content
     * @return string The formatted wall posts and their comments
     *
     */
    public function showPosts($posts, $numPosts, $wallType, $wallid, $num=10, 
      $hideMorePosts=FALSE, $testIfLoggedIn=TRUE, $makePosterNameALink=TRUE) {
        // Initialize the comments string.
        $comments = NULL;
        $ret = NULL;
        // Build the more posts link
        if (!$hideMorePosts) {
            $numPostsTxt = "<a class='wall_posts_more' "
                    . "id='more_posts_{$wallid}' href='javascript:void(0);'>"
                    . $this->objLanguage->languageText("mod_wall_olderposts", "wall", "Older posts")
                    . "</a>";
        } else {
            $numPostsTxt = NULL;
        }
        $objCommentDb = $this->getObject('dbcomment', 'wall');
        // See if they are in myprofile else default link to wall
        $currentModule = $this->getParam('module', 'wall');
        // Get the current user Id
        $amLoggedIn = $this->objUser->isLoggedIn();
        if ($amLoggedIn) {
            $myUserId = $this->objUser->userId();
        } else {
            $myUserId = "NOT_LOGGED_IN";
        }
        foreach ($posts as $post) {
            // Set variables to NULL because they are used with .= later.
            $comments = NULL;
            $showMoreReplies = NULL;
            // Retrieve a small version of the user's avatar image.
            $img = $this->objUser->getSmallUserImage($post['posterid'], FALSE);
            // Convert the post date into human time.
            $when = $this->objDd->getDifference($post['datecreated']);
            $fullName = $post['firstname'] . " " . $post['surname'];
            $id = $post['id'];
            $replies = $post['replies'];
            // If there are fewer than 3 replies, show them all, otherwise get last 3.
            if ($replies > 0) {
                // Get the last three replies.
                $commentAr = $objCommentDb->getComments($id, 3);
                if ($replies > 3) {
                    // Let us know that there are more and build a link to get them via ajax.
                    $moreReplies = $replies - 3;
                    $reps = $this->objLanguage->languageText("mod_wall_replies", "wall", "more replies");
                    $showMoreReplies = $moreReplies . " " . $reps . ".";
                    $showMoreReplies = "<table class='tb_comments_more'><tr>"
                            . "<td align='right'><a class='wall_comments_more' "
                            . "id='mrep__$id' href='javascript:void(0);'>"
                            . $showMoreReplies . "</a></td></tr></table>";
                }
                // Render the comments into LI tags.
                $comments = $this->loadComments($commentAr, $currentModule, $makePosterNameALink);
                $comments = $this->createCommentBlock($comments, $id);
                // The replies notice which goes at the bottom of the wall post.
                $repliesNotice = $replies . ' '
                        . $this->objLanguage->languageText(
                                'mod_wall_replies', 'wall', 'replies');
            } else {
                $repliesNotice = $this->objLanguage->languageText(
                        'mod_wall_noreplies', 'wall', 'No replies');
            }

            if ($currentModule == 'myprofile') {
                $fnLink = $this->uri(array(
                    'username' => $post['username']
                        ), 'myprofile');
            } else {
                $fnLink = $this->uri(array(
                    'walltype' => 'personal',
                    'username' => $post['username']
                        ), 'wall');
            }
            if ($makePosterNameALink) {
                $fullName = '<a href="' . $fnLink . '">' . $fullName . '</a>';
            }
            $del = NULL;
            if ($this->objUser->isLoggedIn()) {
                if ($amLoggedIn) {
                    if ($myUserId == $post['posterid'] || $myUserId == $post['ownerid'] || $this->objUser->isAdmin()) {
                        $delLink = $this->uri(array(
                            'action' => 'delete',
                            'id' => $id
                                ), 'wall');
                        $delLink = "javascript:void(0);";
                        $delLink = str_replace('&amp;', '&', $delLink);
                        $del = '<a class="delpost" id="'
                                . $id . '" href="' . $delLink . '">[x]</a>';
                    } else {
                        $del = NULL;
                    }
                } else {
                    $del=NULL;
                }
            } else {
                $del = NULL;
            }
            // Render the content for display.
            $ret .= "<div class='wallpostrow' id='wpr__" . $id . "'>$del<div class='msg'>\n" . $img
                    . "<span class='wallposter'>" . $fullName
                    . "</span><br /><div class='wall_post_content'>"
                    . $post['wallpost'] . "</div></div><div class='wall_post_info'>" . $when
                    . "&nbsp;&nbsp;&nbsp;&nbsp;" . $repliesNotice . "&nbsp;&nbsp;&nbsp;&nbsp;";


            $ret .= $this->getCommentDisplayButton($id);

            $ret .= "</div>\n";

            if ($this->objUser->isLoggedIn()) {
                $ret .= $this->getReplyLink($id);
            }
            $ret .= $comments . " "
                    . $showMoreReplies . "</div>\n";
        }
        $ret = $this->addToWall($ret . $numPostsTxt, $wallid);
        return $ret;
    }

    /**
     *
     * Method to load the comments into LI tags and add additional
     * data for rendering to display.
     *
     * @param string Array $commentAr An array of comments retrieved from the database
     * @param string $currentModule The current module we are in
     * @param string $makePosterNameALink  whether or not to make name of the poster a link
     * @return string The rendered comments
     *
     */
    public function loadComments($commentAr, $currentModule='wall', $makePosterNameALink=TRUE) {
        $comments = NULL;
        foreach ($commentAr as $comment) {
            $commentWhen = $this->objDd->getDifference($comment['datecreated']);
            $commentFn = $comment['firstname'] . " " . $comment['surname'];
            if ($currentModule == 'myprofile') {
                $cfnLink = $this->uri(array(
                    'username' => $comment['username']
                        ), 'myprofile');
            } else {
                $cfnLink = $this->uri(array(
                    'walltype' => 'personal',
                    'username' => $comment['username']
                        ), 'wall');
            }
            $targetId = $comment['id'];
            $commentor = $comment['posterid'];
            $wallOwner = $comment['wallowner'];
            $wallType = $comment['walltype'];
            $del = $this->getDelCommentLink($targetId, $commentor, $wallOwner, $wallType);
            if ($makePosterNameALink) {
                $commentFn = '<a href="' . $cfnLink . '">' . $commentFn . '</a>';
            }
            $comments .= "<li id='cmt__" . $targetId . "'>" . $del . "<span class='wall_comment_author'>"
                    . $commentFn . "</span>&nbsp;&nbsp;<div class='wall_comment_content'>" . $comment['wallcomment']
                    . "</div><br /><div class='wall_comment_when'>"
                    . $commentWhen . "</div></li>";
        }
        return $comments;
    }

    /**
     *
     * Build the coment block
     *
     * @param string $comments The contents of the comment block
     * @return string The rendered block
     *
     */
    public function createCommentBlock($comments, $id) {
        // Tag it with the ID so we can write back to it from Javascript
        $blockTop = "\n\n<div class='wall_comments_top'></div>"
                . "<ol class='wall_replies' id='wct_" . $id . "'>";
        $blockBottom = "</ol><div class='wall_comments_bottom'></div>\n\n";
        return $blockTop . "\n" . $comments . "\n" . $blockBottom;
    }

    /**
     *
     * Render the input box for posting a wall post.
     *
     * @return string The input box and button
     *
     */
    private function showPostBox($wallType, $keyName, $keyValue, $ownerId=FALSE) {
        $wallid = $keyValue;
        if ($this->objUser->isLoggedIn()) {
            if ($ownerId) {
                $target = $this->uri(array(
                    'action' => 'save',
                    'ownerid' => $ownerId,
                    'walltype' => $wallType,
                    $keyName => $keyValue
                        ), 'wall');
            } else {
                $target = $this->uri(array(
                    'action' => 'save',
                    'walltype' => $wallType,
                    $keyName => $keyValue
                        ), 'wall');
            }
            $target = str_replace('&amp;', "&", $target);

            $onlyText = $this->objLanguage->languageText("mod_wall_onlytext", "wall", "No HTML, only links and text");
            $enterText = $this->objLanguage->languageText("mod_wall_entertext", "wall", "Enter your message here...");
            $shareText = $this->objLanguage->languageText("mod_wall_share", "wall", "Share");
            $ret = '<div id=\'updateBox\'>
            ' . $enterText . '
            <textarea class=\'wallpost\' id=\'wallpost_' . $wallid . '\'></textarea>
            <input type="hidden" id=\'target_' . $wallid . '\' value="' . $target . '" name="target_' . $wallid . '" />
            <button class=\'shareBtn\' id=\'' . $wallid . '\'>' . $shareText . '</button>
            <div class="wall_onlytext" id="wall_onlytext_' . $wallid . '">' . $onlyText . '</div>
            <div class=\'clear\'></div>
            </div>';
        } else {
            $ret = NULL;
        }
        return $ret;
    }

    /**
     *
     * Load the Ajax and other jQuery Javascript, including the OEMBED
     * translation. The script is rendered in the page header.
     *
     * @return VOID
     */
    private function loadScript() {
        if (!$this->wallType) {
            $objGuessWall = $this->getObject('wallguesser', 'wall');
            $wallType = $objGuessWall->guessWall();
            $this->wallType = $wallType;
        } else {
            $wallType = $this->wallType;
        }
        $objGuessUser = $this->getObject('bestguess', 'utilities');
        $ownerId = $objGuessUser->guessUserId();
        $target = $this->uri(array(
            'action' => 'save',
            'ownerid' => $ownerId,
            'walltype' => $wallType
                ), 'wall');
        $target = str_replace('&amp;', "&", $target);
        $myUserId = $this->objUser->userId();
        $me = $this->objUser->fullName();
        $youSaid = $this->objLanguage->languageText("mod_wall_yousaid", "wall", "You said");
        $secsAgo = $this->objLanguage->languageText("mod_wall_secsago", "wall", "a few seconds ago");
        $nothingApppendTo = $this->objLanguage->languageText("mod_wall_nothingappendto", "wall", "There is nothing to append to. Reload the page and try again.");


        //$un = $this->objUser->userName();
        //$currentModule = $this->getParam('module', 'wall');
        //if ($currentModule == 'myprofile') {
        //    $fnLink = $this->uri(array(
        //        'username' => $un
        //    ), 'myprofile');
        //} else {
        //    $fnLink = $this->uri(array(
        //        'walltype' => 'personal',
        //        'username' => $un
        //    ), 'wall');
        //}
        //$me = '<a href="' . $fnLink . '">' . $fn . '</a>';
        switch ($wallType) {
            case "2":
            case "personal":
                $keyValue = $ownerId;
                break;
            case "3";
                $objContext = $this->getObject('dbcontext', 'context');
                if ($objContext->isInContext()) {
                    $currentContextcode = $objContext->getcontextcode();
                    $keyValue = $currentContextcode;
                } else {
                    $keyValue = -99;
                }
                break;
            case "4":
                $keyValue = $this->getParam('identifier', -99);
                $target .= '&keyValue=' . $keyValue;
                break;
            case "1":
            case "sitewall":
            default:
                $keyValue = -99;
                break;
        }
        $page = $this->getParam('page', '1');
        $img = $this->objUser->getSmallUserImage();
        $myName = $me;
        $me = "\n\n<span class='wallposter'>" . $me . "</span>";
        $newScript = "<script type='text/javascript'>\n";
        $newScript .= "var page=" . $page . ";\n";
        $newScript .= "var wallType=" . $wallType . ";\n";
        $newScript .= "var keyValue='" . $keyValue . "';\n";
        $newScript .= "var me='" . $myName . "';\n";
        //$newScript .= "var target='" . $target ."';\n";
        $newScript .= "var youSaid='" . $youSaid . "';\n";
        $newScript .= "var secsAgo='" . $secsAgo . "';\n";
        $newScript .= "var nothingApppendTo='" . $nothingApppendTo . "';\n";
        $newScript .= "</script>\n\n";
        $this->appendArrayVar('headerParams', $newScript);
    }

    /**
     *
     * Return the comment box area or panel. There is a bit of trickery here with
     * the use of $id to generate the code to pass to the Ajax.
     *
     * @param string $id The parent id of the wall post to which the comment applies
     * @return string The formatted panel.
     *
     */
    public function getReplyLink($id) {
        $panel = '<div class=\'wall_panel\' id="c__' . $id . '">'
                . '<textarea id=\'ct_cb_' . $id . '\' style="width:390px;height:23px"></textarea><br />
            <input type="submit" value=" Comment " class="comment_submit" id="cb_' . $id . '"/>
            </div>';
        return $panel;
    }

    /**
     *
     * Render the comment link
     *
     * @param string $id The parent id of the wall post to which the comment applies
     * @return string  The formatted button
     * @access public
     *
     */
    public function getCommentDisplayButton($id) {
        if ($this->objUser->isLoggedIn()) {
            $button = '<a href="#" class="wall_comment_button" id="'
                    . $id . '">Comment</a>';
        } else {
            $button = NULL;
        }
        return $button;
    }

    /**
     *
     * Get the link for deleting a given comment
     *
     * @param string $id The id of the record
     * @param string $commentor The user id of the commentor
     * @param string $wallOwner The user id of the wall owner
     * @param integer $wallType The wall type (1,2,3)
     * @param string $identifier Typically the context code
     * @return string The formattted comment deletion link
     * @access public
     *
     */
    public function getDelCommentLink($id, $commentor, $wallOwner, $wallType, $identifier=NULL) {
        $userId = $this->objUser->userId();
        $delLink = $this->uri(array(
            'action' => 'deletecomment',
            'id' => $id
                ), 'wall');
        $delLink = "javascript:void(0);";
        $delLink = str_replace('&amp;', '&', $delLink);
        $delLink = '<a class="wall_delcomment" id="'
                . $id . '" href="'
                . $delLink . '">x</a>';

        switch ($wallType) {
            case '1':
                if ($userId == $commentor
                        || $this->objUser->isAdmin()) {
                    return $delLink;
                } else {
                    return NULL;
                }
            case '2':
                if ($userId == $commentor
                        || $userId == $wallOwner) {
                    return $delLink;
                } else {
                    return NULL;
                }
                break;
            case '3':
                if (
                        $userId == $commentor
                        || $this->objUser->isAdmin()
                        || $this->objUser->isContextAuthor()
                        || $this->objUser->isContextEditor()) {
                    return $delLink;
                } else {
                    return NULL;
                }
                break;
                case '4':
                if (
                        $userId == $commentor
                        || $this->objUser->isAdmin()
                        || $this->objUser->isContextAuthor()
                        || $this->objUser->isContextEditor()) {
                    return $delLink;
                } else {
                    return NULL;
                }
                break;
            default:
                return NULL;
                break;
        }
    }

    /**
     *
     *
     */

    /**
     *
     * A parameter value setter
     *
     * @param string $param The parameter to set
     * @param mixed $value The value of the parameter
     * @return boolean TRUE
     * @access public
     *
     */
    public function setValue($param, $value) {
        $this->param = $value;
        return true;
    }

}

?>