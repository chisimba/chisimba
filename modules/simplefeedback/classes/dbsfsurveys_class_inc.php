<?php
/**
 *
 * Database access for Simple feedback surveys
 *
 * Database access for Simple feedback surveys. This is a database model class
 * that provides data access to the table - tbl_simplefeedback_surveys.
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
 * @package   simplefeedback
 * @author    Derek Keats derekkeats@gmail.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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
 *
 * Database access for Simple feedback surveys
 *
 * Database access for Simple feedback surveys. This is a database model class
 * that provides data access to the table - tbl_simplefeedback_surveys.
*
* @package   simplefeedback
* @author    Derek Keats derekkeats@gmail.com
*
*/
class dbsfsurveys extends dbtable
{

    /**
    *
    * Intialiser for the simplefeedback database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_simplefeedback_surveys');
    }
    
    /**
     * 
     * Get the title and description for a particular survey, identified
     * by $surveyId (id in the database)
     * 
     * @param string $surveyId The id of the record
     * @return string array The survey information
     * 
     */
    public function getSurveyInfo($surveyId)
    {
        $res = $this->getAll(" where id='$surveyId' ");
        return $res[0];
    }
    
    
    
    
    
    
    
    /*public function save()
    {
        $fullname = $this->getParam('name', NULL);
        $email = $this->getParam('email', NULL);
        $question_1 = $this->getParam('question_1', NULL);
        $question_2 = $this->getParam('question_2', NULL);
        $question_3 = $this->getParam('question_3', NULL);
        return $this->insert(array(
                'post_title'=>$title,
                'post_content'=>$content,
                'post_status'=>$status,
                'post_type' => $blogType,
                'userid'=>$userId,
                'blogid'=>$blogId,
                'datecreated'=>$this->now(),
                'post_tags' => $postTags
            ));
        
    }*/
}
?>