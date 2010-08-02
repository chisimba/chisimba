<?php

/***
* STories category block
*
* A wide block class to display the stories according to  category
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
* @package   dynamiccanvas
* @author    Derek Keats derek.keats@wits.ac.za
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   CVS: $Id: dbdynamiccanvas.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
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
* STories category block
*
* A wide block class to display the stories according to  category
*
*
* @author Derek Keats
* 
* $Id: block_storiescat_class_inc.php 18284 2010-07-03 13:45:02Z dkeats $
*
*/
class block_storiescat extends object
{
    /**
    * @var object $objLanguage String to hold the language object
    */
    private $objLanguage;
    
    /**
    * @var string $title The title of the block
    */
    public $title;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
    */
    public function init()
    {
        try {
            $this->objLanguage =  $this->getObject('language', 'language');
            $this->title = ucwords($this->objLanguage->code2Txt('word_stories', 'stories', NULL, '[-stories-]'));
            $this->expose=TRUE;
        } catch (customException $e) {
            customException::cleanUp();
        }
    }
    
    /**
    * Standard block show method. It uses the renderform
    * class to render the login box
    */
    public function show()
    {
        try {
            $objBc = $this->getObject('blockconfig', 'blocks');
            $configArray = $objBc->getConfigArray($this->configData);
            $storyCategory = $configArray['category'];
            if ($storyCategory == "") {
                $storyCategory = 'prelogin';
            }
            
            $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $useSummaries = $objSysConfig->getValue('USESUMMARIES', 'stories');
            $objStories = $this->getObject('sitestories', 'stories');
            
            if($useSummaries == 'Y'){
                return $objStories->fetchPreLoginCategory($storyCategory, 3);
            } else {
                return $objStories->fetchCategory($storyCategory);
            }
        } catch (customException $e) {
            customException::cleanUp();
        }
    }
}
?>