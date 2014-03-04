<?php

/**
 * Class that contains the chapters in the learningcontent module
 *
 * At this stage, chapters are not yet assigned to contexts. It provides a list of contexts
 * that can be reused.
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
 * @version    $Id: db_learningcontent_comment_class_inc.php 11380 2010-02-11 00:17:11Z qfenama $
 * @package    learningcontent
 * @author     Qhamani Fenama <qfenama@gmail.com/qfenama@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class that contains the comments of the page
 *
 *
 * @author Qhamani Fenama
 *
 */

class db_learningcontent_comment extends dbtable
{

    /**
     * Constructor
     */
    public function init()
    {
        parent::init('tbl_learningcontent_page_comment');
        $this->objUser =& $this->getObject('user', 'security');
    }

	/**
	 *Function that return the comment of the particular page
	 *param: pageid
	 *returns: comments as array
	 **/
    public function getPageComments($pageid)
	{
		$comments = $this->getAll("WHERE pageid = '{$pageid}'");
		return $comments;
	}

	/**
	 *Function that return the comment of the particular page
	 *params: pageid 
	 *returns: comments as array
	 **/
    public function addPageComment($userid, $pageid, $comment)
	{
		$arrayOfRecords = array(
			'userid' => $userid,
			'pageid' => $pageid,
            'comment' => $comment,
			'datecreated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
        );

        return $this->insert($arrayOfRecords, 'tbl_learningcontent_page_comment');
       
	}

	/**
	 *Function that reomove all the comments of a page
	 *params: pageid
	 **/
	public function deletePageComments($pageid)
    {
        // Delete the all the comments of a page
        return $this->delete('pageid', $pageid);
    }
    
}
?>
