<?php

/**
 * Login stats
 * 
 * Chisimba Context login stats class
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
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check


/**
 * Login stats
 * 
 * Chisimba Context login stats class
 * 
 * @category  Chisimba
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class block_loginstats extends object
{
    /**
    * @var string $title The title of the block
    */
    public   $title;
    
    /**
    * @var object $objLanguage String to hold the language object
    */
    protected $objLanguage;
    
    /**
    * @var object $objUser String to hold the user object
    */
    protected $objUser;
    
    /**
    * Standard init function to instantiate language and user objects
    * and create title
    */
    public function init()
    {
        //Create an instance of the user object
        $this->objUser = $this->getObject('user','security');
        //Create an instance of the language object
        $this->objLanguage = $this->getObject('language','language');
        //Set the title
        $this->title="Login Stats";//$this->objLanguage->languageText("mod_reports_loginstats");
    }
    
    /**
    * Standard block show method. It builds the output based
    * on data obtained via the user class
    */
    public function show()
    {
    
        // Build the display table
        $this->rTable = $this->newObject('htmltable', 'htmlelements'); 
        
        //The number of times the user has logged in
        $this->rTable->startRow();
        $this->rTable->addCell(
          $this->objLanguage->languageText("phrase_numberoflogins") 
          . ":<span class=\"highlight\"> " . $this->objUser->logins() . "</span>");
        $this->rTable->endRow();

        //The date and time of last login
        $lastOnDate = $this->objUser->getLastLoginDate();
        $lastOnDate = date("l, F jS Y - H:i:s", strtotime($lastOnDate));
        $this->rTable->startRow();
        $this->rTable->addCell("<font size=\"-2\"><b>" .
          $this->objLanguage->languageText("phrase_lastlogin") 
          . "</b>: <span class=\"highlight\">" . $lastOnDate . "</span></font>");
        $this->rTable->endRow();

        //The time the current user has been active
        $this->rTable->startRow();
        $this->rTable->addCell("<font size=\"-2\"><b>" .
          $this->objLanguage->languageText("phrase_timeactive") 
          . "</b>: <span class=\"highlight\">" . $this->objUser->myTimeOn() . " "
          . $this->objLanguage->languageText("mod_datetime_mins")
          . "</span></font>");
        $this->rTable->endRow();
        
        //Return the formatted table
        return $this->rTable->show();
    }
}
?>