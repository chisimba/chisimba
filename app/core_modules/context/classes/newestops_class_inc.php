<?php

/**
*
* Ops class for displaying newest contexts
*
* Shows the newest contexts in a particular category, with the
* context image and a link to choose and enter the context. Parameters
* determine which category is displayed. It is used by a number of blocks
* that start with block_newestXXX.
*
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
* @author    Derek Keats <derek@dkeats.com>
* @copyright 2012 Kenga Solutions
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
*
* Ops class for displaying newest contexts
*
* Shows the newest contexts in a particular category, with the
* context image and a link to choose and enter the context. Parameters
* determine which category is displayed. It is used by a number of blocks
* that start with block_newestXXX.
*
* @category  Chisimba
* @package   context
* @author    Derek Keats <derek@dkeats.com>
* @copyright 2012 Kenga Solutions
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   Release: @package_version@
* @link      http://avoir.uwc.ac.za
* @see       core
*/
class newestops extends object
{
    /**
    * @var object $objContext : The Context Object
    */
    public $objContext;

    /**
    * @var object $objLanguage : The Language Object
    */
    public $objLanguage;
    /**
     * The user Object
     *
     * @var object $objUser
     */
    public $objUser;
    /**
     *
     * An array of contexts in which the current user is registered
     *
     * @var string array $userContexts
     *
     */
    private $userContexts;
    /**
     *
     * A boolean variable indicatin whether the userContexts array is built
     *
     * @var boolean $accessArrayBuilt
     *
     */
    private $accessArrayBuilt;


    /**
    * Constructor
    */
    public function init()
    {
        $this->loadClass('link', 'htmlelements');
        $this->objDb = $this->getObject('dbcontext');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->accessArrayBuilt = FALSE;
    }

    /**
     *
     * Return the content for the block
     *
     * @param string $contextType The type of context (e.g. Public, Open, Private)
     * @param integer $numOfItems The number of items to return
     * @return string The rendered block content
     * @access public
     *
     */
    public function fetchBlock($contextType='Public', $numOfItems=6)
    {
        $objContextImage = $this->getObject('contextimage', 'context');
        $objShorter = $this->getObject('trimstr', 'strings');
        $mustBeMember = FALSE;
        $mustBeLoggedIn = FALSE;
        switch ($contextType) {
            case 'Open':
                $contexts = $this->objDb->getArrayOfOpenContexts($numOfItems);
                break;
            case 'Public':
                $contexts = $this->objDb->getArrayOfPublicContexts($numOfItems);
                break;
            case 'Private':
                $contexts = $this->objDb->getArrayOfPrivateContexts($numOfItems);
                break;
            case 'MostRecentActive':
                $contexts = $this->objDb->getArrayOfMostRecentlyActiveContexts($numOfItems);
                break;
            default:
                return NULL; // replace with error message
                break;
        }
        $objTranslatedDate = $this->getObject('translatedatedifference', 'utilities');
        $ret = NULL;
        if (count($contexts) > 0) {
            foreach ($contexts as $context) {
                $contextCode = $context['contextcode'];
                $access = $context['access'];
                switch ($access) {
                    case 'Private':
                        $mustBeLoggedIn = TRUE;
                        $mustBeMember = TRUE;
                        break;
                    case 'Public':
                        $mustBeLoggedIn = FALSE;
                        $mustBeMember = FALSE;
                        break;
                    case 'Open':
                        $mustBeLoggedIn = TRUE;
                        $mustBeMember = FALSE;
                        break;
                    default:
                        $mustBeLoggedIn = TRUE;
                        $mustBeMember = TRUE;
                        break;
                }
                $contextImage = $objContextImage->getContextImage($contextCode);
                // Show if it has an image
                if ($contextImage == FALSE) {
                    $contextImage = $this->noImage;
                } else {
                    $contextImage = '<img class="roundcorners_small" src="' . $contextImage . '" />';
                }
                $title = $objShorter->strTrim($context['title'], 25, TRUE);
                if ($contextType == 'MostRecentActive') {
                    $creationDate = $context['lastaccessed'];
                    if ($creationDate == NULL || $creationDate == "") {
                        $creationDate = $context['datecreated'];
                    }
                } else {
                    $creationDate = $context['datecreated'];
                }
                $creationDate = $objTranslatedDate->getDifference($creationDate);
                // Get the about text and shorten it.
                $about = $objShorter->strTrim($context ['about'], 90, TRUE);
                if ($mustBeLoggedIn) {
                    if ($this->objUser->isLoggedIn()) {
                        if ($mustBeMember) {
                            if ($this->allowedAccess($contextCode)) {
                                $title = $this->linkItem($contextCode, $title);
                            }
                        } else {
                            $title = $this->linkItem($contextCode, $title);
                        }
                    }
                } else {
                    $title = $this->linkItem($contextCode, $title);
                }
                $ret .= "<div class='context_recent_context'>{$contextImage} "
                . "<span class='context_recent_title'>" . $title
                . "</span><br /><span class='context_about'>{$about}</span><br />"
                . "<span class='context_creationdate'>{$creationDate}</span></div>";
            }
        } else {
            $msg = $this->objLanguage->code2Txt('mod_context_norecordscat', 'context', NULL, 'No [-contexts-] in this category');
            $ret = "<span class='warning'>{$msg}</span>";
        }
        return "<div class='context_recent'>{$ret}</div>";
    }

    /**
     *
     * Make the item linked to enter context
     *
     * @param string $contextCode The context to enter
     * @param string $linkText The text (or image tag) of the link
     * @return string The rendered link
     * @access private
     *
     */
    private function linkItem($contextCode, $linkText)
    {
        $link = new link($this->uri(array('action' => 'joincontext',
        'contextcode' => $contextCode)));
        $link->link = $linkText;
        return $link->show();
    }

    private function allowedAccess($contextCode)
    {
        if (!$this->accessArrayBuilt) {
            $objUserContext = $this->getObject('usercontext');
            if ($this->objUser->isLoggedIn()) {
                $this->userContexts = $objUserContext->getUserContext($this->objUser->userId());
            } else {
                $this->userContexts = array();
            }
            $this->accessArrayBuilt = TRUE;
        }
        if ($this->objUser->isAdmin() ||
          in_array($contextCode, $this->userContexts)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
?>