<?php
/**
 *
 * Provides functionality specifically aimed at the UWC Elearning Mobile website
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
 * @package   uwcelearningmobile
 * @author    Qhamani Fenana qfenama@uwc.ac.za/qfenama@gmail.com
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: mobilesecurity.php,v 1.4 2007-11-25 09:13:27 qfenama Exp $
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
* Class that handles utilities on the uwc elearning mobile. 
* 
* @author Qhamani Fenama 
*
*/
class util extends object {
     
	
	function init() {
		$this->objLanguage = $this->getObject('language', 'language');
		$this->objUser = $this->getObject('user', 'security');
	}


	/*
     * The method that retrieves the users
     */
    public function getUsers($page, $search = '') {
        $start = $page * 20;
        $sql = 'SELECT username, userId FROM tbl_users';
        $where = '';
        if($search != '') {
            $where = ' WHERE firstname LIKE \'%'.$search.'%\' or surname LIKE \'%'.$search.'%\' OR username LIKE \'%'.$search.'%\'';
        }
        $order = ' ORDER BY firstname';
        $limit = ' LIMIT '.$start.', 20';
        $users = $this->objUser->getArray($sql.$where.$order.$limit);
        return $users;
    }

     /**
        * Method to get an assignment from the database.
        * @param string $context The current context.
        * @param string $filter
        * @return array $data List of assignments
        */
        public function getAssignments($context, $filter=NULL)
        {
            $sql = " WHERE context='".$context."'";

            if($filter){
                $sql .= ' AND '.$filter;
            }
            $sql .= ' ORDER BY closing_date DESC';

			$this->objAssignment = $this->getObject('dbassignment', 'assignment');

            return $this->objAssignment->getAll($sql);
        }
		
	 /**
     * this gets student submissions
     * @param <type> $assignmentId
     * @param <type> $orderBy
     * @return <type>
     */
    public function getStudentSubmissions($assignmentId, $orderBy = 'firstname, datesubmitted')
    {
        $sql = ' SELECT tbl_assignment_submit.*, firstName, surname, staffnumber FROM tbl_assignment_submit
        INNER JOIN tbl_users ON tbl_assignment_submit.userid = tbl_users.userid  WHERE assignmentid=\''.$assignmentId.'\' ORDER BY '.$orderBy;
        $this->objAssignmentSubmit = $this->getObject('dbassignmentsubmit', 'assignment');
        $ret = $this->objAssignmentSubmit->getArray($sql);
		return $ret;
    }


	/**
     * this get a single assigntment
     * @param <type> $assignmentId
     * @return <type>
     */
	 public function getAssignment($id)
     {
         $this->objAssignment = $this->getObject('dbassignment', 'assignment');
         return $this->objAssignment->getRow('id', $id);
     }


	 /**
     * gets the submission
     * @param <type> $id
     * @return <type>
     */
    public function getSubmission($id)
    {
		$this->objAssignmentSubmit = $this->getObject('dbassignmentsubmit', 'assignment');
        return $this->objAssignmentSubmit->getRow('id', $id);
    }

	/**
 	*get assignment filename
 	* @param <type> $submissionId
	* @param <type> $fileId
	* @return <type>
 	*/
    public function getAssignmentFilename($submissionId, $fileId)
    {
        $objFile = $this->getObject('dbfile', 'filemanager');
        $file = $objFile->getFile($fileId);

        // Do own search if file not found
        if ($file == FALSE)
        {

        }

        $objConfig = $this->getObject('altconfig', 'config');
        $filePath = $objConfig->getcontentBasePath().'/assignment/submissions/'.$submissionId.'/'.$file['filename'];

        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $filePath = $objCleanUrl->cleanUpUrl($filePath);

        if (!file_exists($filePath)) {
            $originalFile = $objConfig->getcontentBasePath().'/'.$file['path'];
            $originalFile = $objCleanUrl->cleanUpUrl($originalFile);

            if (file_exists($originalFile)) {

                $objMkdir = $this->getObject('mkdir', 'files');
                $objMkdir->mkdirs(dirname($filePath), 0777);

                copy($originalFile, $filePath);
            }
        }

        return $filePath;
		}
} 
?>
