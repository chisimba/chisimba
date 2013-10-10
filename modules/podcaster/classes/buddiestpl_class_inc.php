<?php
/**
* Class to Render online buddies from buddies module
*
* Render online buddies from buddies module for use in the webpresent module
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
* @version   $Id: buddiestpl_class_inc.php 11934 2008-12-29 21:18:12Z charlvn $
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
* Class for rendering online buddy list into the
* webpresent template
*
* @author Derek Keats
* @category Chisimba
* @package webpresent
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class buddiestpl extends object
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
     * Method to get all online buddies into an array property
     * @return TRUE
     * @access Public
     *
     */
    public function getMessages()
    {

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
        if ($this->objUser->isLoggedIn()) {
            $objBuds = $this->getObject("dbbuddies", "buddies");
            $ar = $objBuds->getBuddiesOnline($this->objUser->userId());
            $buddyLink = new link();
            $buddyLink->link = $this->objLanguage->languageText("mod_webpresent_managebuddies", "webpresent");
            $buddyLink->href = $this->uri(array(), "buddies");
            $rowTable = $this->newObject('htmltable', 'htmlelements');
            $rowTable->startRow();
            $rowTable->addHeaderCell($this->objLanguage->languageText("word_name"));
            $rowTable->addHeaderCell($this->objLanguage->languageText("mod_webpresent_lastactive", "webpresent"));
            $rowTable->addHeaderCell($this->objLanguage->languageText("word_message"));
            $rowTable->endRow();
            $objIcon = $this->newObject('geticon', 'htmlelements');
            $objIcon->setIcon('instantmessaging', 'gif', 'icons/modules');
            $this->objIcon->title = $this->objLanguage->languageText("phrase_sendinstantmessage");
            $imIcon = $objIcon->show();
            if (count($ar)!==0 && !empty($ar)) {
                $this->budCount = " (" . count($ar) . ")";
                foreach ($ar as $buddy) {
                    $rowTable->startRow();
                    $buddyName = $this->objUser->fullName($buddy['buddyid']);
                    $presLink = new link();
                    $buddyLink->link = $buddyName;
                    $buddyLink->href = $this->uri(array("action" => "byuser",
                      "userid" => $buddy['buddyid']), "webpresent");
                    $rowTable->addCell($buddyLink->show());
                    $rowTable->addCell($buddy['whenlastactive']);
                    $rowTable->addCell($imIcon);
                    $rowTable->endRow();
                }
                $ret = $rowTable->show();
            } else {
                $this->budCount = "";
                $ret = "<span class='noRecordsMessage'>" .
                  $this->objLanguage->languageText("mod_webpresent_nobuds", "webpresent")
                  . "</span>";
            }
            $buddyLink->link = $this->objLanguage->languageText("mod_webpresent_managebuddies", "webpresent");
            $buddyLink->href = $this->uri(array(), "buddies");
            return $ret . "<br />" . $buddyLink->show();
        } else {
            return NULL;
        }

    }
}
?>
