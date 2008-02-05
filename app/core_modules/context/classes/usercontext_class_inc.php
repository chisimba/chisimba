<?php

/**
* User Context
* 
* lass to get the list of contexts a user belongs to, add user to contexts, etc.
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
* @package   context
* @author    Tohir Solomons <tsolomons@uwc.ac.za>
* @copyright 2008 Tohir Solomons
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   CVS: $Id$
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
* User Context
* 
* lass to get the list of contexts a user belongs to, add user to contexts, etc.
* 
* @category  Chisimba
* @package   context
* @author    Tohir Solomons <tsolomons@uwc.ac.za>
* @copyright 2008 Tohir Solomons
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   Release: @package_version@
* @link      http://avoir.uwc.ac.za
* @see       core
*/
class usercontext extends object
{
    /**
    * @var object $objUser : The user Object
    */
    public $objUser;
    
    /**
    * Constructor
    */
    public function init()
    {
       $this->objUser = $this->getObject('user','security');
       $this->objGroups =  $this->getObject('managegroups', 'contextgroups');
       $this->objContext =  $this->getObject('dbcontext');
       
       $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
     * Method to get the list of Contexts that a user belongs to
     *
     * @param string $userId User Id
     * @return array
     */
    public function getUserContext($userId)
    {
       $objGroups =  $this->newObject('managegroups', 'contextgroups');
       return $objGroups->usercontextcodes($userId);
    }
    
    
    /**
     * Method to get the list of user contexts in a formatted display
     * @param string $userId User Id
     * @return string
     */
    public function getUserContextsFormatted($userId)
    {
        
        $contextCodes = $this->getUserContext($userId);
        
        if (count($contextCodes) == 0) {
            return $this->objLanguage->code2Txt('mod_context_youdonotbelongtocontexts', 'context', NULL, 'You do not belong to any [-contexts-]');
        } else {
            
            $arr = array();
            $objDisplayContext = $this->getObject('displaycontext');
            
            foreach ($contextCodes as $code)
            {
                
                $context = $this->objContext->getContext($code);
                
                if ($context != FALSE) {
                    $arr[strtolower(trim($context['title']))] = $objDisplayContext->formatContextDisplayBlock($context);
                }
            }
            
            if (count($arr) == 0) {
                return $this->objLanguage->code2Txt('mod_context_youdonotbelongtocontexts', 'context', NULL, 'You do not belong to any [-contexts-]');
            } else {
                
                $returnStr = '';
                
                ksort($arr);
                
                
                foreach ($arr as $item=>$str)
                {
                   $returnStr .= $str.'<br />';
                }
                
                return $returnStr;
            }
        }
    }

}
?>