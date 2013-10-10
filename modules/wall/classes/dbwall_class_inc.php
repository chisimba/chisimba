<?php
/**
 *
 * A simple wall module
 *
 * A simple wall module that makes use of OEMBED and that tries to look a bit like Facebook's wall
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
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Database accesss class for Chisimba for the module wall
*
* @author Derek Keats
* @package wall
*
*/
class dbwall extends dbtable
{
    /**
    *
    * @var string object The user object
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
    * Intialiser for the wall database connector
    * @access public
    *
    */
    public function init()
    {
        //Set the parent table here
        parent::init('tbl_wall_posts');
        $this->objUser = $this->getObject('user', 'security');
        // Instantiate the language object.
        $this->objLanguage = $this->getObject('language', 'language');

    }

    /**
     *
     * Default method to get the wall data as an array
     *
     * @param string $wallType The wall type (0=site wall, 1=personal or user wall, 2=context wall)
     * @param integer $num The number of results to return, defaulting to 10
     * @return string array An array of posts
     *
     */
    public function getWall($wallType, $num=10)
    {
        // The base SQL, uses joins to avoid going back and forth to the db
        $baseSql = 'SELECT tbl_wall_posts.*,
              tbl_users.userid,
              tbl_users.firstname,
              tbl_users.surname,
              tbl_users.username,
              (SELECT COUNT(tbl_wall_comments.parentid)
                   FROM tbl_wall_comments
                   WHERE tbl_wall_comments.parentid = tbl_wall_posts.id
              ) AS replies
              FROM tbl_wall_posts, tbl_users
              WHERE tbl_wall_posts.posterId = tbl_users.userid';

        $filter = $this->getFilter($wallType, $num) . " ORDER BY datecreated DESC LIMIT {$num}";
        //$filter = $this->getFilter($wallType, $num) . " ORDER BY datecreated DESC ";
        $sql = $baseSql . $filter;
        $sql = "SELECT * FROM tbl_wall_posts ";
        $posts = $this->getArray($baseSql . $filter);
        //$posts = $this->getArrayWithLimit($sql, 0, $num);
        //var_dump($posts);
        //die();
        return $posts;
    }

    /**
     *
     * Get more older posts for appending to the bottom of existing posts
     * by Ajax
     *
     * @param integer $wallType The wall type (1,2,3)
     * @param integer $page The starting page
     * @param string $keyName The name of the key (contextcode usually)
     * @param string $keyValue The value of the key (usually contextcode)
     * @param integer $num The number of records to return (pagesize)
     * @return string array An array of posts if any
     * @access public
     *
     */
    public function getMorePosts($wallType, $page, $keyName, $keyValue, $num=10)
    {
        // The base SQL, uses joins to avoid going back and forth to the db
        $baseSql = 'SELECT DISTINCT tbl_wall_posts.*,
              tbl_users.userid,
              tbl_users.firstname,
              tbl_users.surname,
              tbl_users.username,
              (SELECT COUNT(tbl_wall_comments.parentid)
                   FROM tbl_wall_comments
                   WHERE tbl_wall_comments.parentid = tbl_wall_posts.id
              ) AS replies
              FROM tbl_wall_posts, tbl_users
              WHERE tbl_wall_posts.posterId = tbl_users.userid ';
        $filter = " AND walltype = '$wallType' ";
        if ($keyName !== NULL && $keyValue !==NULL) {
            $filter .= " AND " . $keyName . " = '" . $keyValue . "'";
        }
        $startPoint = $page * $num;
        $tail =  " ORDER BY datecreated DESC LIMIT {$startPoint}, {$num} ";
        $sql = $baseSql . $filter . $tail;
       // die($sql);
        $posts = $this->getArray($sql);
        return $posts;
    }
    
    /**
     *
     * Count the number of posts per wall type, user or total
     *
     * @param string $wallType The wall type (0=site wall, 1=personal or user wall, 2=context wall)
     * @param boolean $total Whether or not to count total posts
     * @return integer The number of posts
     *
     */
    public function countPosts($wallType, $total=FALSE, $keyName=FALSE, $keyValue=FALSE)
    {
        $baseSql = 'SELECT COUNT(id) AS totalposts FROM tbl_wall_posts ';
        $filter = NULL;
        if ($total) {
            $sql = $baseSql;
        } else {
            $filter = $this->getCountFilter($wallType, $keyName, $keyValue);
        }
        $countAr = $this->getArray($baseSql . $filter);
        return $countAr[0]['totalposts'];
    }

    /**
     *
     * Get the filter to be used in counting posts
     *
     * @param integer $wallType The wall type to count for
     * @return string The filter
     *
     */
    public function getCountFilter($wallType, $keyName=FALSE, $keyValue=FALSE)
    {
        if ($keyName && $keyValue) {
            $filter = " WHERE walltype = '{$wallType}' AND {$keyName} = '{$keyValue}' ";
            return $filter;
        }
        // Create the filter based on walltype.
        if ($wallType == '3') {
            // Next check if they are in a context.
            $objContext = $this->getObject('dbcontext', 'context');
            if($objContext->isInContext()){
                $currentContextcode = $objContext->getcontextcode();
                $filter = " WHERE walltype = '{$wallType}' AND {$keyName} = '{$currentContextcode}' ";
            }
        } elseif ($wallType == '2') {
            $objGuessUser = $this->getObject('bestguess', 'utilities');
            $ownerId = $objGuessUser->guessUserId();
            $filter = " WHERE walltype = '$wallType' AND ownerid= '$ownerId' ";
        } else {
            $filter = " WHERE walltype = '$wallType' ";

        }
        return $filter;
    }

    /**
     *
     * Get the filter to be used in selecting posts
     *
     * @param integer $wallType The wall type to select for
     * @param integer $num The number of posts to return
     * @return string The filter
     *
     */
    public function getFilter($wallType, $num=10)
    {
        // Create the filter based on walltype
        if ($wallType == '3') {
            // Next check if they are in a context.
            $objContext = $this->getObject('dbcontext', 'context');
            if($objContext->isInContext()){
                $currentContextcode = $objContext->getcontextcode();
                $filter = " AND walltype = '$wallType' AND identifier = '$currentContextcode' ";
            }
        } elseif ($wallType == '2') {
            $objGuessUser = $this->getObject('bestguess', 'utilities');
            $ownerId = $objGuessUser->guessUserId();
            $filter = " AND walltype = '$wallType' AND ownerid= '$ownerId' ";
        } else {
            $filter = " AND walltype = '$wallType' ";
        }
        return $filter;
    }


    /**
    *
    * Save a post and return something to send back to the ajax request.
    *
    * Note that walltypes can be 0 for site wall, 1 for personal wall, and
    * 2 for context wall.
    *
    * @return string The results of the save (true, empty, false)
    *
    */
    public function savePost()
    {

        if ($this->objUser->isLoggedIn()) {
            $wallPost = $this->getParam('wallpost', 'empty');
            $posterId = $this->objUser->userId();
            $ownerId = $this->getParam('ownerid', NULL);
            // Get the owner full name for the activity stream.
            if ($ownerId != NULL) {
                $ownerFullName = $this->objUser->fullName($ownerId);
            } else {
                $ownerFullName = NULL;
            }
            // Figure out the wall.
            $objGuessWall = $this->getObject('wallguesser','wall');
            $wallType = $objGuessWall->guessWall();
            // Do stuff depending on wall type.
            if ($wallType == '3') {
                $objContext = $this->getObject('dbcontext', 'context');
                if($objContext->isInContext()){
                    $identifier = $objContext->getcontextcode();
                    $rep = array('contextcode' => $identifier);
                    $postedWhere = $this->objLanguage->code2Txt("mod_wall_oncontextwall", "wall", $rep);
                } else {
                    // Edge case, should never happen.
                    $identifier = NULL;
                    $postedWhere = NULL;
                }
            } elseif ($wallType == '4') {
                $identifier = $this->getParam('identifier', NULL);
                $postedWhere = $this->objLanguage->languageText("mod_wall_onsimpleblogwall", "wall", "Wrote on a blog post wall.");
            } elseif ($wallType == '1') {
                $identifier="sitewall";
                $postedWhere = $this->objLanguage->languageText("mod_wall_onsitewallwall", "wall");
            } else {
                $identifier=NULL;
                $postedWhere = " on $ownerId wall ";
                $rep = array('owner' => $ownerFullName);
                $postedWhere = $this->objLanguage->code2Txt("mod_wall_onwall", "wall" , $rep);
            }
            if ($wallPost !=='empty') {
                try
                {
                    $this->insert(array(
                        'wallpost' => $wallPost,
                        'posterid' => $posterId,
                        'ownerid' => $ownerId,
                        'identifier' => $identifier,
                        'walltype' => $wallType,
                        'datecreated' => $this->now()));
                    // Log in the activity stream.
                    $objModuleCat = $this->getObject('modules', 'modulecatalogue');
                    if($objModuleCat->checkIfRegistered('activitystreamer')) {
                        // Record a title, even though we don't need it.
                        $title = $this->objLanguage->languageText("mod_wall_wallpost", "wall",  "Wall post");

                        // Construct the message to store as the activity.
                        $rep = array(
                            'activity_doer' => $this->objUser->fullName(),
                            'activity' => $postedWhere
                          );
                        $message = $this->objLanguage->code2Txt("mod_wall_activity", "wall" , $rep);

                        // Construct the link to view the wall post.
                        $link = "index.php?module=wall";
                        // Log the activity to the activity stream.
                        $objActStream = $this->getObject('activityops','activitystreamer');
                        $this->eventDispatcher->addObserver(array($objActStream, 'postmade' ));
                        $this->eventDispatcher->post(
                          $objActStream, 'wall',
                          array(
                              'title' => $title,
                              'link' => $link,
                              'contextcode' => $identifier,
                              'description' => $message
                          )
                        );
                    }
                    return 'true';
                } catch (customException $e)
                {
                    echo customException::cleanUp($e);
                    die();
                }
            } else {
                return 'empty';
            }
        } else {
            return 'spoofattemptfailure';
        }
    }

    /**
     *
     * Delete a wall post
     *
     * @param string $id The id key of the record to delete
     * @return string An indication of the reuslts ('true' or 'norights')
     *
     */
    public function deletePost($id)
    {
        $chSql = "SELECT id, posterid, ownerid FROM tbl_wall_posts WHERE id='$id'";
        $ar = $this->getArray($chSql);
        $me = $this->objUser->userId();
        $posterid = $ar[0]['posterid'];
        $ownerid =  $ar[0]['ownerid'];
        if ($me == $posterid || $me = $ownerid) {
            // I can delete
            $this->delete('id', $id);
            // Delete any comments as well
            $this->dbComments = $this->getObject('dbcomment', 'wall');
            $this->dbComments->deleteAssociatedComments($id);
            return "true";
        } else {
            return 'norights';
        }

    }
}
?>