<?php

/**
* Block to show list of members in a context
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
* @package   contextgroups
* @author    Tohir Solomons <tsolomons@uwc.ac.za>
* @copyright 2007 Tohir Solomons
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   $Id$
* @link      http://avoir.uwc.ac.za
* @see       core
*/
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!
/**
* Description for $GLOBALS
* @global entry point $GLOBALS['kewl_entry_point_run']
* @name   $kewl_entry_point_run
*/
$GLOBALS['kewl_entry_point_run']) {
die("You cannot view this page directly");
}
// end security check


/**
* Block to show list of members in a context
*
* This class generates a block to show the photos and names of members of a context
* 
* @category  Chisimba
* @package   contextgroups
* @author    Tohir Solomons <tsolomons@uwc.ac.za>
* @copyright 2007 Tohir Solomons
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   Release: @package_version@
* @link      http://avoir.uwc.ac.za
* @see       core
*/
class block_contextmembers extends object
{

   /**
   * Constructor
   */
   public function init()
   {
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->objContext->getContextCode();
        
        $this->loadClass('link', 'htmlelements');
        
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        
        $this->title = ucwords($this->objLanguage->code2Txt('mod_contextgroups_contextmembers','contextgroups'));
   }
   
   /**
    * Method to show the block
    */
   public function show()
   {
        if ($this->contextCode == 'root' || $this->contextCode == '') {
            return '';
        }
        
        $objManageGroups = $this->getObject('managegroups', 'contextgroups');
        
        $lecturers = $objManageGroups->contextUsers('Lecturers', $this->contextCode, array( 'tbl_users.userId', 'firstName', 'surname'));
        $students = $objManageGroups->contextUsers('Students', $this->contextCode, array( 'tbl_users.userId', 'firstName', 'surname'));
        
        $str = '';
        
        
        $str .= '<p><strong>'.ucwords($this->objLanguage->code2Txt('word_lecturers','system')).'</strong></p>';
        
        if (count($lecturers) == 0) {
            $str .= '<p>'.$this->objLanguage->code2Txt('mod_contextgroups_nolecturers','contextgroups').'<p>';
        } else {
            $str .= '<p>';
            
            foreach ($lecturers as $lecturer)
            {
                $str .= $this->objUser->getSmallUserImage($lecturer['userid'], $lecturer['firstname'].' '.$lecturer['surname']).' ';
            }
            
            $str .= '</p>';
        }
        
         $str .= '<p><strong>'.ucwords($this->objLanguage->code2Txt('word_students','system')).'</strong></p>';
        
        if (count($students) == 0) {
            $str .= '<p>'.$this->objLanguage->code2Txt('mod_groupadmin_nostuds','groupadmin').'<p>';
        } else {
            $str .= '<p>';
            
            foreach ($students as $student)
            {
                $str .= $this->objUser->getSmallUserImage($student['userid'], $student['firstname'].' '.$student['surname']).' ';
            }
            
            $str .= '</p>';
        }
        
        $link = new link ($this->uri(NULL, 'contextgroups'));
        $link->link = $this->objLanguage->code2Txt('mod_contextgroups_toolbarname','contextgroups');
        
        $str .= '<p>'.$link->show();
        
        
        
        $objModules = $this->getObject('modules', 'modulecatalogue');
        
        if ($objModules->checkIfRegistered('userimport')) {
            $link = new link ($this->uri(NULL, 'userimport'));
            $link->link = $this->objLanguage->languageText('mod_contextgroups_importusers', 'contextgroups', 'Import Users');
            
            $str .= ' / '.$link->show();
        }
        
        $str .= '</p>';
        
        return $str;
   }

}
?>
