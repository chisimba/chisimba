<?php
/**
 *
 * File access for Banner Helper
 *
 * File access for Banner Helper. Accesses the Banner Helper files for this site.
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
 * @package   bannerhelper
 * @author    Derek Keats derek@dkeats.com
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
 * File access for Banner Helper
 *
 * File access for Banner Helper. Accesses the Banner Helper file for this site.
*
* @package   bannerhelper
* @author    Derek Keats derek@dkeats.com
*
*/
class fsbannerhelper extends object
{

    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;
    
    /**
    *
    * Intialiser for the Banner Helper file system
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
     *
     * Get the contents of the $file.html file from
     * the BannerHelper directory in usrfiles. Format it for 
     * display.
     * 
     * @return string The rendered contents
     * @access public
     * 
     */
    public function getContentsForDisplay($file=FALSE, $encode = FALSE)
    {
        if (!$file) {
            // Read the file from querystring and default to 'default'.
            $file = $this->getParam("file", "banner0");
        }
        $strContent = $this->readContents($file);
        // Parse for filters
        $objWashOut = $this->getObject('washout', 'utilities');
        $strContent = $objWashOut->parseText($strContent);
        $canEdit = $this->checkEditRights();
        // Add the edit icon and link.
        if ($canEdit) {
            $ed = $this->getEditIcon($file);
        } else {
            $ed=NULL;
        }
        if ($encode) {
            $strContent = htmlentities($strContent);
        }
        return $strContent . "<br />" . $ed;
    }
    
    /**
     *
     * Get the contents of the $file.html file for editing
     * 
     * @return string the file contents
     * @access public
     * 
     */
    public function getContentsForEdit($file=FALSE)
    {
        if (!$file) {
            // Read the file from querystring and default to 'default'.
            $file = $this->getParam("file", "banner0");
        }
        $this->loadClass('form','htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        //Set up the form processor
        $paramArray=array(
            'action'=>'save',
            'file' => $file);
        $formAction=$this->uri($paramArray);
        $objForm = new form('bannerblock_text');
        $objForm->setAction($formAction);
        $objForm->displayType=3; 
        $strContent = $this->readContents($file);
        $editor =  new textarea('banner', $strContent);
        $objForm->addToForm($editor->show());
        $buttonTitle = $this->objLanguage->languageText('word_save');
        $button = new button('saveBanner', $buttonTitle);
        $button->setToSubmit();
        $objForm->addToForm("<br />" . $button->show());
        $hidMode = new hiddeninput('file');
        $hidMode->value = $file;
        $objForm->addToForm($hidMode->show());
        return $objForm->show();
    }
    
    /**
     * 
     * Save the edited contents back to the $file.html file
     * @access public
     * @return VOID
     * 
     */
    public function save($file=FALSE)
    {
        if ($this->checkEditRights()) {
            if (!$file) {
                // Read the file from querystring and default to 'default'.
                $file = $this->getParam("file", "banner0");
            }
            $banner = $this->getParam('banner', NULL);
            $objAltConfig = $this->getObject('altconfig', 'config');
            $targetPath = $objAltConfig->getSiteRootPath() 
              . 'usrfiles/bannerhelper/' . $file . '.txt';
            $strContent = file_put_contents($targetPath, $banner);
        }
    }
    
    /**
     *
     * Create the linked edit icon
     * 
     * @return string The rendered icon
     * @access private
     * 
     */
    private function getEditIcon($file="default")
    {
        $this->loadClass('link', 'htmlelements');
        $uri = $this->uri(array('action' => 'edit',
            'file' => $file), 'bannerhelper');
        $link = new link($uri);
        // Edit icon
        $edIcon = $this->newObject('geticon', 'htmlelements');
        $edIcon->setIcon('edit');
        $link->link = $edIcon->show();
        unset($edIcon);
        $ret = $link->show();
        return $ret;
    }

    /**
     *
     * Get the raw contents of the $file.html file from
     * the BannerHelper directory in usrfiles. 
     * 
     * @return string The raw contents
     * @access public
     * 
     */
    public function readContents($file="banner0")
    {
        $objAltConfig = $this->getObject('altconfig', 'config');
        $targetPath = $objAltConfig->getSiteRootPath() . 'usrfiles/bannerhelper/' . $file . '.txt';
        $strContent = file_get_contents($targetPath);
        return $strContent;
    }
    
    /**
     *
     * Check if a user should be able to edit based on isAdmin
     * and membership of the BannerHelper group
     * 
     * @return boolean If they have edit rights or not 
     * @access public
     */
    public function checkEditRights()
    {
        $ret=FALSE;
        $objUser = $this->getObject('user', 'security');
        $userId = $objUser->userId();
        // Admins can edit.
        $objGa = $this->getObject('gamodel','groupadmin');
        $edGroup = $objGa->isGroupMember($userId, "BannerHelper");
        if ($objUser->isLoggedIn()) {
            if ($objUser->isAdmin() || $edGroup ) {
                $ret = TRUE;
            }
        }
        return $ret;
    }

}
?>