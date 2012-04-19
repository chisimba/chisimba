<?php

/**
* The newest fully public contexts
*
* Shows the newest public contexts, with the context image and a link
* to choose and enter the context.
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
* The newest fully public contexts
*
* Shows the newest public contexts, with the context image and a link
* to choose and enter the context.
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
class block_newestopen extends object
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
    * Constructor
    */
    public function init()
    {
        $this->loadClass('link', 'htmlelements');
        $this->objDb = $this->getObject('dbcontext');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->title = ucwords($this->objLanguage->code2Txt('mod_context_newestopen', 'context', NULL, 'Newest open [-contexts-]'));
        $this->wrapStr = FALSE;
    }

    /**
     * Method to show the block
     */
    public function show()
    {
        $objContextImage = $this->getObject('contextimage', 'context');
        $objShorter = $this->getObject('trimstr', 'strings');
        $publicContexts = $this->objDb->getArrayOfOpenContexts();
        $objTranslatedDate = $this->getObject('translatedatedifference', 'utilities');
        $ret = NULL;
        foreach ($publicContexts as $context) {
            $contextCode = $context['contextcode'];
            $contextImage = $objContextImage->getContextImage($contextCode);
            // Show if it has an image
            if ($contextImage == FALSE) {
                $contextImage = $this->noImage;
            } else {
                $contextImage = '<img class="roundcorners_small" src="' . $contextImage . '" />';
            }
            $title = $objShorter->strTrim($context['title'], 30, TRUE);
            $creationDate = $context['datecreated'];
            $creationDate = $objTranslatedDate->getDifference($creationDate);
            // Get the about text and shorten it.
            $about = $objShorter->strTrim($context ['about'], 120, TRUE);
            if ($this->objUser->isLoggedIn()) {
                $link = new link($this->uri(array('action' => 'joincontext',
                'contextcode' => $contextCode)));
                $link->link = $title;
                $title = $link->show();
            }
            $ret .= "<div class='context_recent_context'>{$contextImage} "
              . "<span class='context_recent_title'>" . $title
              . "</span><br /><span class='context_about'>{$about}</span><br />"
              . "<span class='context_creationdate'>{$creationDate}</span></div>";
        }
        return "<div class='context_recent'>{$ret}</div>";
    }
}
?>