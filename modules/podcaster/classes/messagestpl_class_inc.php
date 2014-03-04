<?php
/**
* Class to Render latest messages from email module
*
* Render latest messages from email module for use in the webpresent module
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
* @package   filters
* @author    Derek Keats <dkeats[AT]uwc[DOT]ac[DOT]za>
* @copyright 2007 UWC and AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
* @version   $Id: messagestpl_class_inc.php 11934 2008-12-29 21:18:12Z charlvn $
* @link      http://avoir.uwc.ac.za
*/


// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
*
* Class for rendering email messages into the
* webpresent template
*
* @author Derek Keats
* @category Chisimba
* @package webpresent
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class messagestpl extends object 
{
    /**
    * 
    * @var $objLanguage String object property for holding the 
    * language object
    * @access private
    * 
    */
    private $objLanguage;
    
    /**
    *
    * Standard init method  
    *
    */
    public function init()
    {
        // Get the language object. 
        $this->objLanguage = $this->getObject('language', 'language');
        // Get the user object.
        $this->objUser = $this->getObject("user", "security");
        $this->loadClass('link', 'htmlelements');
    }
    
    /**
     * 
     * Method to get all unread mail messages into an array property
     * @return TRUE
     * @access Public
     * 
     */
    public function getMessages()
    {
        $userId = $this->objUser->userId();
        $sortOrder = array(0 => 'date_sent',
          1 => 2,
          2 => 'ASC');
        $objRoute = $this->getObject("dbrouting", "email");
        $this->ar = $objRoute->getAllMail("init_1", $sortOrder, "2");
        return TRUE;
    }
    
    /**
     * 
     * Method to render the unread email messages
     * @return The rendered messages as String 
     * @access Public
     */
    public function show()
    {
        //Instantiate the modules class to check if youtube is registered
        if ($this->objUser->isLoggedIn()) {
            $mailLink = new link();
            $mailLink->link = $this->objLanguage->languageText("word_email");
            $mailLink->href = $this->uri(array(), "email");
            $rowTable = $this->newObject('htmltable', 'htmlelements');
            $rowTable->startRow();
            $rowTable->addHeaderCell($this->objLanguage->languageText("word_email"));
            $rowTable->addHeaderCell($this->objLanguage->languageText("word_from"));
            $rowTable->addHeaderCell($this->objLanguage->languageText("word_on"));
            $rowTable->endRow();
            $this->getMessages();
            if (count($this->ar)!==0 && !empty($this->ar)) {
                $this->msgCount = " (" . count($this->ar) . ")";
                foreach ($this->ar as $mailitem) {
                    $subject = $mailitem['subject'];
                    // Make the subject the link to view mail
                    $linkAr = array("action" => "gotomessage",
                      "folderId" => "init_1",
                      "routingId" => $mailitem['routing_id']);
                    $anchor = new link();
                    $anchor->link = $subject;
                    $anchor->href = $this->uri($linkAr, "email");
                    $from = $this->objUser->fullName($mailitem['sender_id']);
                    $sendDate = $mailitem['date_sent'];
                    $rowTable->startRow();
                    $rowTable->addCell($anchor->show());
                    $rowTable->addCell($from);
                    $rowTable->addCell($sendDate);
                    $rowTable->endRow();
                }
                return $rowTable->show() . $mailLink->show();
            } else {
                $this->msgCount = "";
                return "<span class='noRecordsMessage'>" 
                  . $this->objLanguage->languageText("mod_webpresent_nomessages", "webpresent") 
                  . "</span><br /><br />" . $mailLink->show();
            }
        } else {
            return NULL;
        }          
    }
}
?>
